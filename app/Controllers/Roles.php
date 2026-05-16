<?php

namespace App\Controllers;

class Roles extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('admin.settings')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $groups = config('AuthGroups')->groups;

        $db     = db_connect();
        $counts = $db->table('auth_groups_users')
            ->select('group, COUNT(*) as total')
            ->groupBy('group')
            ->get()
            ->getResultArray();
        $userCounts = array_column($counts, 'total', 'group');

        return view('backend/roles/index', [
            'pageTitle'       => 'Roles & Permissions',
            'pageDescription' => 'Manage what each role can do in the system.',
            'groups'          => $groups,
            'userCounts'      => $userCounts,
        ]);
    }

    public function show(string $group)
    {
        if (!auth()->user()->can('admin.settings')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $groups = config('AuthGroups')->groups;
        if (!array_key_exists($group, $groups)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $allPerms   = config('AuthGroups')->permissions;
        $matrix     = setting('AuthGroups.matrix') ?? config('AuthGroups')->matrix;
        $groupPerms = $matrix[$group] ?? [];

        $granted = [];
        foreach (array_keys($allPerms) as $perm) {
            [$module] = explode('.', $perm);
            $granted[$perm] = in_array($perm, $groupPerms) || in_array("{$module}.*", $groupPerms);
        }

        $modules = [];
        foreach ($allPerms as $perm => $desc) {
            [$module] = explode('.', $perm);
            $modules[$module][] = ['key' => $perm, 'desc' => $desc, 'granted' => $granted[$perm]];
        }

        return view('backend/roles/show', [
            'pageTitle'       => $groups[$group]['title'] . ' — Permissions',
            'pageDescription' => $groups[$group]['description'],
            'group'           => $group,
            'groupDef'        => $groups[$group],
            'modules'         => $modules,
            'isLocked'        => $group === 'superadmin',
        ]);
    }

    public function syncMatrix()
    {
        if (!auth()->user()->can('admin.settings')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $configGroups = config('AuthGroups')->groups;
        $configMatrix = config('AuthGroups')->matrix;
        $dbMatrix     = setting('AuthGroups.matrix') ?? $configMatrix;

        $added   = [];
        $skipped = [];

        foreach ($configGroups as $group => $def) {
            $configGrants = $configMatrix[$group] ?? [];
            $dbGrants     = $dbMatrix[$group] ?? null;

            if ($dbGrants === null) {
                $dbMatrix[$group] = $configGrants;
                $added[] = $def['title'] . ' (new group, ' . count($configGrants) . ' permissions)';
                continue;
            }

            $missing = array_values(array_diff($configGrants, $dbGrants));
            if (empty($missing)) {
                $skipped[] = $def['title'];
                continue;
            }

            $dbMatrix[$group] = array_values(array_unique(array_merge($dbGrants, $missing)));
            $added[] = $def['title'] . ' (+' . count($missing) . ' permission' . (count($missing) > 1 ? 's' : '') . ')';
        }

        service('settings')->set('AuthGroups.matrix', $dbMatrix);
        log_activity('roles.synced', 'role', null, 'Synced permissions matrix from config.');

        return $this->response->setJSON([
            'status'  => 'success',
            'added'   => $added,
            'skipped' => count($skipped),
        ]);
    }

    public function update(string $group)
    {
        if (!auth()->user()->can('admin.settings')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($group === 'superadmin') {
            return redirect()->back()->with('error', 'Superadmin permissions cannot be edited.');
        }

        $groups = config('AuthGroups')->groups;
        if (!array_key_exists($group, $groups)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $knownPerms = array_keys(config('AuthGroups')->permissions);
        $submitted  = $this->request->getPost('permissions') ?? [];
        $selected   = array_values(array_filter((array) $submitted, fn($p) => in_array($p, $knownPerms)));

        $matrix         = setting('AuthGroups.matrix') ?? config('AuthGroups')->matrix;
        $matrix[$group] = $selected;

        service('settings')->set('AuthGroups.matrix', $matrix);

        log_activity('roles.updated', 'role', null, "Updated permissions for '{$groups[$group]['title']}'");

        return redirect()->to('roles/' . $group)->with('success', 'Permissions updated.');
    }
}
