<?php

namespace App\Models;

use CodeIgniter\Model;

class IngestSessionModel extends Model
{
    protected $table         = 'ingest_sessions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title', 'ingest_location', 'description',
        'status', 'created_by', 'closed_at',
    ];

    public function getAllWithCreator(): array
    {
        return $this->db->query("
            SELECT s.*,
                CONCAT(u.first_name, ' ', u.last_name) AS creator_name,
                COUNT(DISTINCT ti.chip_id) AS chip_count
            FROM ingest_sessions s
            LEFT JOIN users u ON u.id = s.created_by
            LEFT JOIN chip_transactions ct ON ct.ingest_session_id = s.id AND ct.transaction_type = 'INGEST'
            LEFT JOIN transaction_items ti ON ti.transaction_id = ct.id
            GROUP BY s.id
            ORDER BY s.id DESC
        ")->getResultArray();
    }

    public function getWithCreator(int $id): ?array
    {
        return $this->db->query("
            SELECT s.*,
                CONCAT(u.first_name, ' ', u.last_name) AS creator_name
            FROM ingest_sessions s
            LEFT JOIN users u ON u.id = s.created_by
            WHERE s.id = ?
        ", [$id])->getRowArray() ?: null;
    }
}
