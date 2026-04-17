<?php

namespace App\Models;

use CodeIgniter\Model;

class ChipTransactionModel extends Model
{
    protected $table         = 'chip_transactions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'transaction_type', 'from_participant_id', 'to_participant_id',
        'to_location', 'ingest_session_id', 'handled_by', 'remarks',
    ];

    /**
     * All transactions with participant names, handler, chip count, session title.
     */
    public function getAllWithDetails(array $filters = []): array
    {
        $builder = $this->db->table('chip_transactions ct')
            ->select("
                ct.*,
                fp.name AS from_name,
                tp.name AS to_name,
                CONCAT(u.first_name, ' ', u.last_name) AS handler_name,
                s.title AS session_title,
                COUNT(ti.id) AS chip_count
            ")
            ->join('participants fp',  'fp.id = ct.from_participant_id', 'left')
            ->join('participants tp',  'tp.id = ct.to_participant_id',   'left')
            ->join('users u',          'u.id  = ct.handled_by',          'left')
            ->join('ingest_sessions s','s.id  = ct.ingest_session_id',   'left')
            ->join('transaction_items ti', 'ti.transaction_id = ct.id',  'left')
            ->groupBy('ct.id')
            ->orderBy('ct.id', 'DESC');

        if (!empty($filters)) {
            $builder->where($filters);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Full transaction detail including chips.
     */
    public function getDetail(int $transactionId): ?array
    {
        $tx = $this->db->table('chip_transactions ct')
            ->select("
                ct.*,
                fp.name AS from_name,
                tp.name AS to_name,
                CONCAT(u.first_name, ' ', u.last_name) AS handler_name,
                s.title AS session_title
            ")
            ->join('participants fp',  'fp.id = ct.from_participant_id', 'left')
            ->join('participants tp',  'tp.id = ct.to_participant_id',   'left')
            ->join('users u',          'u.id  = ct.handled_by',          'left')
            ->join('ingest_sessions s','s.id  = ct.ingest_session_id',   'left')
            ->where('ct.id', $transactionId)
            ->get()->getRowArray();

        if (!$tx) return null;

        $tx['chips'] = $this->db->query("
            SELECT c.* FROM transaction_items ti
            JOIN chips c ON c.id = ti.chip_id
            WHERE ti.transaction_id = ?
        ", [$transactionId])->getResultArray();

        return $tx;
    }
}
