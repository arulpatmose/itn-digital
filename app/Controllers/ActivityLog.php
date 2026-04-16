<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ActivityLog extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('activity_log.access')) {
            return redirect()->back()->with('error', 'You are not allowed to view this page!');
        }

        return view('backend/activity_log/index', [
            'page_title'       => 'Activity Log',
            'page_description' => 'Audit trail of all actions performed in the system.',
            'scope'            => $this->getLogScope(),
        ]);
    }

    public function getLogs()
    {
        if (!auth()->user()->can('activity_log.access')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (!$this->request->isAJAX()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db    = \Config\Database::connect();
        $scope = $this->getLogScope();

        $builder = $db->table('activity_logs as al')
            ->select("al.id, al.action, al.target_type, al.target_id,
                      al.description, al.ip_address, al.created_at,
                      NULLIF(TRIM(CONCAT_WS(' ', u.first_name, u.last_name)), '') AS user_name,
                      u.username")
            ->join('users as u', 'u.id = al.user_id', 'left');

        // Scope — always enforced, cannot be bypassed via filters
        if ($scope['user_only']) {
            $builder->where('al.user_id', auth()->id());
        }

        // User-defined filters
        $filterFrom   = $this->request->getPost('filter_from');
        $filterTo     = $this->request->getPost('filter_to');
        $filterAction = $this->request->getPost('filter_action');
        $filterTarget = $this->request->getPost('filter_target');
        $search       = $this->request->getPost('search')['value'] ?? '';

        if (!$scope['user_only']) {
            $filterUser = $this->request->getPost('filter_user');
            if ($filterUser) {
                $builder->groupStart()
                    ->like('u.first_name', $filterUser)
                    ->orLike('u.last_name', $filterUser)
                    ->orLike('u.username', $filterUser)
                    ->groupEnd();
            }
        }

        if ($filterAction) {
            $builder->like('al.action', $filterAction);
        }

        if ($filterTarget) {
            $builder->where('al.target_type', $filterTarget);
        }

        if ($filterFrom) {
            $builder->where('al.created_at >=', $filterFrom . ' 00:00:00');
        }

        if ($filterTo) {
            $builder->where('al.created_at <=', $filterTo . ' 23:59:59');
        }

        if ($search) {
            $builder->groupStart()
                ->like('al.action', $search)
                ->orLike('al.description', $search)
                ->orLike('al.target_type', $search)
                ->orLike('u.first_name', $search)
                ->orLike('u.last_name', $search)
                ->orLike('u.username', $search)
                ->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        // Total within scope only (before user-defined filters)
        $totalBuilder = $db->table('activity_logs as al')
            ->join('users as u', 'u.id = al.user_id', 'left');
        if ($scope['user_only']) {
            $totalBuilder->where('al.user_id', auth()->id());
        }
        $total = $totalBuilder->countAllResults();

        $start  = (int) ($this->request->getPost('start')  ?? 0);
        $length = (int) ($this->request->getPost('length') ?? 25);

        $logs = $builder
            ->orderBy('al.created_at', 'DESC')
            ->limit($length, $start)
            ->get()->getResultArray();

        $rows = [];
        foreach ($logs as $log) {
            $rows[] = [
                'created_at'  => $log['created_at'],
                'user_name'   => $log['user_name'] ?: ($log['username'] ?: 'System'),
                'action'      => $log['action'],
                'target_type' => $log['target_type'],
                'target_id'   => $log['target_id'],
                'description' => $log['description'],
                'ip_address'  => $log['ip_address'],
            ];
        }

        return $this->response->setJSON([
            'draw'            => (int) ($this->request->getPost('draw') ?? 1),
            'recordsTotal'    => $total,
            'recordsFiltered' => $totalFiltered,
            'data'            => $rows,
        ]);
    }

    /**
     * Derives data scope from the user's activity log permissions.
     *
     * activity_log.all  → unrestricted view of all entries
     * activity_log.own  → own entries only (fallback)
     */
    private function getLogScope(): array
    {
        if (auth()->user()->can('activity_log.all')) {
            return ['user_only' => false];
        }

        return ['user_only' => true];
    }
}
