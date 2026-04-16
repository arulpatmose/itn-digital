<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionItemModel extends Model
{
    protected $table         = 'transaction_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['transaction_id', 'chip_id', 'copy_status', 'copied_at', 'copied_by'];

    public function insertForTransaction(int $transactionId, array $chipIds): void
    {
        $rows = array_map(fn($chipId) => [
            'transaction_id' => $transactionId,
            'chip_id'        => (int) $chipId,
            'copy_status'    => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ], $chipIds);

        $this->db->table('transaction_items')->insertBatch($rows);
    }

    public function setCopyStatus(int $itemId, string $status, int $userId): void
    {
        $this->update($itemId, [
            'copy_status' => $status,
            'copied_at'   => $status === 'done' ? date('Y-m-d H:i:s') : null,
            'copied_by'   => $status === 'done' ? $userId : null,
        ]);
    }

    public function getSessionProgress(int $sessionId): array
    {
        $row = $this->db->query("
            SELECT
                COUNT(ti.id)                    AS total,
                SUM(ti.copy_status = 'done')    AS done
            FROM transaction_items ti
            JOIN chip_transactions ct ON ct.id = ti.transaction_id
            WHERE ct.ingest_session_id = ?
              AND ct.transaction_type  = 'INGEST'
        ", [$sessionId])->getRowArray();

        return [
            'total' => (int) ($row['total'] ?? 0),
            'done'  => (int) ($row['done']  ?? 0),
        ];
    }
}
