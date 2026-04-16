<?php

namespace App\Services;

use App\Models\IngestSessionModel;
use App\Models\ChipModel;

class IngestSessionService
{
    protected IngestSessionModel $sessionModel;
    protected ChipModel          $chipModel;

    public function __construct()
    {
        $this->sessionModel = new IngestSessionModel();
        $this->chipModel    = new ChipModel();
    }

    public function create(string $title, int $createdBy, ?string $location = null, ?string $description = null): int
    {
        $id = $this->sessionModel->insert([
            'title'            => $title,
            'ingest_location'  => $location,
            'description'      => $description,
            'status'           => 'open',
            'created_by'       => $createdBy,
        ], true);

        log_activity('ingest_session.created', 'ingest_session', $id, "Created session \"{$title}\"");
        return $id;
    }

    /**
     * Close a session. If some chips haven't been fully returned, mark as partial.
     * This is determined by caller context; we simply accept the desired status.
     */
    public function close(int $sessionId, string $status = 'closed'): bool
    {
        $allowed = ['closed', 'partial'];
        if (!in_array($status, $allowed, true)) $status = 'closed';

        $updated = $this->sessionModel->update($sessionId, [
            'status'    => $status,
            'closed_at' => date('Y-m-d H:i:s'),
        ]);

        if ($updated) {
            log_activity("ingest_session.{$status}", 'ingest_session', $sessionId, "Session marked as {$status}");
        }
        return $updated;
    }

    /**
     * Summary stats for a session: total chips ingested, unique chips.
     */
    public function getStats(int $sessionId): array
    {
        $chips = $this->chipModel->getBySession($sessionId);
        return [
            'chip_count' => count($chips),
            'chips'      => $chips,
        ];
    }
}
