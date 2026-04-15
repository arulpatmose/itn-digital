<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table         = 'activity_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'description',
        'metadata',
        'ip_address',
        'created_at',
    ];

    public function log(
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $description = null,
        ?array $metadata = null
    ): void {
        $this->insert([
            'user_id'     => auth()->id() ?? 0,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'description' => $description ? substr($description, 0, 500) : null,
            'metadata'    => $metadata ? json_encode($metadata) : null,
            'ip_address'  => service('request')->getIPAddress(),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Total count without filters.
     */
    public function countTotal(string $search = ''): int
    {
        $builder = $this->db->table($this->table . ' al')
            ->join('users u', 'u.id = al.user_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('al.action', $search)
                ->orLike('al.description', $search)
                ->orLike('al.target_type', $search)
                ->orLike('al.ip_address', $search)
                ->orLike('u.first_name', $search)
                ->orLike('u.last_name', $search)
                ->groupEnd();
        }

        return (int) $builder->countAllResults();
    }

    /**
     * Fetch paginated rows for DataTables.
     */
    public function getForDataTable(
        string $search,
        string $orderCol,
        string $orderDir,
        int $start,
        int $length
    ): array {
        $allowedCols = ['al.id', 'al.action', 'al.description', 'al.target_type', 'al.ip_address', 'al.created_at'];
        if (!in_array($orderCol, $allowedCols, true)) {
            $orderCol = 'al.created_at';
        }
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        $builder = $this->db->table($this->table . ' al')
            ->select('al.id, al.user_id, al.action, al.target_type, al.target_id, al.description, al.ip_address, al.created_at, u.first_name, u.last_name')
            ->join('users u', 'u.id = al.user_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('al.action', $search)
                ->orLike('al.description', $search)
                ->orLike('al.target_type', $search)
                ->orLike('al.ip_address', $search)
                ->orLike('u.first_name', $search)
                ->orLike('u.last_name', $search)
                ->groupEnd();
        }

        return $builder
            ->orderBy($orderCol, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();
    }
}
