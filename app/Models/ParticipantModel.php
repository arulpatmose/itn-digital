<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table         = 'participants';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'type', 'user_id', 'notes', 'is_active'];

    public function getActive(): array
    {
        return $this->where('is_active', 1)->orderBy('type')->orderBy('name')->findAll();
    }

    public function getByType(string $type): array
    {
        return $this->where('is_active', 1)->where('type', $type)->orderBy('name')->findAll();
    }

    /**
     * Participant with their Shield user details if linked.
     */
    public function getAllWithUser(): array
    {
        return $this->db->query("
            SELECT p.*,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name
            FROM participants p
            LEFT JOIN users u ON u.id = p.user_id
            ORDER BY p.type, p.name
        ")->getResultArray();
    }
}
