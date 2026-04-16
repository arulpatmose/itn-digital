<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionItemModel extends Model
{
    protected $table         = 'transaction_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['transaction_id', 'chip_id'];

    public function insertForTransaction(int $transactionId, array $chipIds): void
    {
        $rows = array_map(fn($chipId) => [
            'transaction_id' => $transactionId,
            'chip_id'        => (int) $chipId,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ], $chipIds);

        $this->db->table('transaction_items')->insertBatch($rows);
    }
}
