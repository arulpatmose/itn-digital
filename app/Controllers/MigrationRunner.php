<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Config\Services;
use CodeIgniter\Database\Migration;

/**
 * MigrationRunner
 *
 * Superadmin-only tool to manage database migrations when the DB was
 * bootstrapped via raw SQL import rather than through CI4 migrations.
 *
 * Workflow for an out-of-sync DB:
 *   1. Use "Run" (▶) on individual new migrations that need to be executed.
 *   2. Use "Sync History" to bulk-mark all remaining historical migrations
 *      as recorded without re-executing them.
 *   3. Going forward, "Run Pending" handles everything in the normal way.
 */
class MigrationRunner extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // -------------------------------------------------------------------------
    // Guard: superadmin only
    // -------------------------------------------------------------------------
    private function guardSuperadmin()
    {
        if (! auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        if (! auth()->user()->can('admin.migrations')) {
            return redirect()->to('/')->with('error', 'You are not authorized to access the migration runner.');
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // GET /migrations  — show status
    // -------------------------------------------------------------------------
    public function index()
    {
        if ($redirect = $this->guardSuperadmin()) {
            return $redirect;
        }

        $allFiles    = $this->scanMigrationFiles();
        $recorded    = $this->getRecordedMigrations();
        $recordedMap = array_column($recorded, null, 'version');

        $migrations = [];
        foreach ($allFiles as $version => $info) {
            $migrations[] = [
                'version'  => $version,
                'class'    => $info['class'],
                'file'     => $info['file'],
                'recorded' => isset($recordedMap[$version]),
                'batch'    => $recordedMap[$version]['batch'] ?? null,
            ];
        }

        usort($migrations, fn($a, $b) => strcmp($a['version'], $b['version']));

        $pendingCount  = count(array_filter($migrations, fn($m) => ! $m['recorded']));
        $recordedCount = count(array_filter($migrations, fn($m) => $m['recorded']));
        $maxBatch      = $this->db->table('migrations')->selectMax('batch')->get()->getRow()->batch ?? 0;

        $data = [
            'page_title'       => 'Migration Runner',
            'page_description' => 'Manage and sync database migrations.',
            'migrations'       => $migrations,
            'pending_count'    => $pendingCount,
            'recorded_count'   => $recordedCount,
            'max_batch'        => $maxBatch,
        ];

        return view('backend/migrations/index', $data);
    }

    // -------------------------------------------------------------------------
    // POST /migrations/run
    // Run ALL pending migrations via CI4's runner (normal workflow).
    // Only use this when the migrations table is fully in sync.
    // -------------------------------------------------------------------------
    public function run()
    {
        if ($redirect = $this->guardSuperadmin()) {
            return $redirect;
        }

        try {
            $migrate = Services::migrations();
            $migrate->latest();

            return redirect()->to('/migrations')->with('success', 'All pending migrations ran successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/migrations')->with('error', 'Migration error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // POST /migrations/run-single
    // Instantiate and run a single migration by version, then record it.
    // Use this for new migrations when historical ones are not yet synced.
    // -------------------------------------------------------------------------
    public function runSingle()
    {
        if ($redirect = $this->guardSuperadmin()) {
            return $redirect;
        }

        $version = $this->request->getPost('version');

        if (empty($version)) {
            return redirect()->to('/migrations')->with('error', 'No version specified.');
        }

        $allFiles = $this->scanMigrationFiles();

        if (! isset($allFiles[$version])) {
            return redirect()->to('/migrations')->with('error', "Migration version '{$version}' not found.");
        }

        // Check not already recorded
        $alreadyRun = $this->db->table('migrations')->where('version', $version)->countAllResults();
        if ($alreadyRun > 0) {
            return redirect()->to('/migrations')->with('error', "Migration '{$version}' is already recorded.");
        }

        $info     = $allFiles[$version];
        $filePath = APPPATH . 'Database/Migrations/' . $info['file'] . '.php';

        try {
            require_once $filePath;

            $className = $info['class'];
            /** @var Migration $migration */
            $migration = new $className();
            $migration->up();

            // Record it in the migrations table
            $maxBatch = $this->db->table('migrations')->selectMax('batch')->get()->getRow()->batch ?? 0;
            $this->db->table('migrations')->insert([
                'version'   => $version,
                'class'     => $info['class'],
                'group'     => 'default',
                'namespace' => 'App',
                'time'      => time(),
                'batch'     => $maxBatch + 1,
            ]);

            return redirect()->to('/migrations')->with('success', "Migration '{$version}' executed and recorded successfully.");
        } catch (\Throwable $e) {
            return redirect()->to('/migrations')->with('error', "Failed to run '{$version}': " . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // POST /migrations/sync
    // Mark all unrecorded migration files as run WITHOUT executing them.
    // Use this for migrations that were already applied via raw SQL import.
    // -------------------------------------------------------------------------
    public function sync()
    {
        if ($redirect = $this->guardSuperadmin()) {
            return $redirect;
        }

        $allFiles         = $this->scanMigrationFiles();
        $recorded         = $this->getRecordedMigrations();
        $recordedVersions = array_column($recorded, 'version');

        $maxBatch = $this->db->table('migrations')->selectMax('batch')->get()->getRow()->batch ?? 0;
        $newBatch = $maxBatch + 1;

        $inserted = 0;
        foreach ($allFiles as $version => $info) {
            if (in_array($version, $recordedVersions, true)) {
                continue;
            }

            $this->db->table('migrations')->insert([
                'version'   => $version,
                'class'     => $info['class'],
                'group'     => 'default',
                'namespace' => 'App',
                'time'      => time(),
                'batch'     => $newBatch,
            ]);

            $inserted++;
        }

        if ($inserted === 0) {
            return redirect()->to('/migrations')->with('success', 'All migrations are already recorded — nothing to sync.');
        }

        return redirect()->to('/migrations')->with('success', "Synced {$inserted} migration(s) into batch {$newBatch} (marked as run, not executed).");
    }

    // -------------------------------------------------------------------------
    // POST /migrations/rollback/(:num)
    // Roll back to a specific batch via CI4's runner.
    // -------------------------------------------------------------------------
    public function rollback(int $batch)
    {
        if ($redirect = $this->guardSuperadmin()) {
            return $redirect;
        }

        try {
            $migrate = Services::migrations();

            if ($migrate->regress($batch)) {
                return redirect()->to('/migrations')->with('success', "Rolled back to batch {$batch}.");
            }

            return redirect()->to('/migrations')->with('error', 'No migrations rolled back (already at that batch or lower).');
        } catch (\Throwable $e) {
            return redirect()->to('/migrations')->with('error', 'Rollback error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Scan APPPATH/Database/Migrations/ and return array keyed by version.
     */
    private function scanMigrationFiles(): array
    {
        $path   = APPPATH . 'Database/Migrations/';
        $files  = glob($path . '*.php') ?: [];
        $result = [];

        foreach ($files as $file) {
            $filename = basename($file, '.php');

            $underscorePos = strpos($filename, '_');
            if ($underscorePos === false) {
                continue;
            }

            $version   = substr($filename, 0, $underscorePos);
            $className = substr($filename, $underscorePos + 1);

            $result[$version] = [
                'class' => 'App\\Database\\Migrations\\' . $className,
                'file'  => $filename,
            ];
        }

        return $result;
    }

    private function getRecordedMigrations(): array
    {
        return $this->db->table('migrations')
            ->orderBy('batch', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }
}
