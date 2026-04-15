<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingPurposeModel extends Model
{
    protected $table            = 'booking_purposes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['group_id', 'name', 'description', 'is_active'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getWithGroup(?int $id = null): array
    {
        $builder = $this->select('booking_purposes.*, booking_purpose_groups.name AS group_name, booking_purpose_groups.sort_order AS group_sort_order')
            ->join('booking_purpose_groups', 'booking_purpose_groups.id = booking_purposes.group_id', 'left')
            ->orderBy('booking_purpose_groups.sort_order', 'ASC')
            ->orderBy('booking_purpose_groups.name', 'ASC')
            ->orderBy('booking_purposes.name', 'ASC');

        if ($id !== null) {
            return (array) $builder->where('booking_purposes.id', $id)->first();
        }

        return $builder->findAll();
    }

    public function getGroupedActivePurposes(): array
    {
        $purposes = $this->select('booking_purposes.*, booking_purpose_groups.name AS group_name, booking_purpose_groups.sort_order AS group_sort_order')
            ->join('booking_purpose_groups', 'booking_purpose_groups.id = booking_purposes.group_id', 'inner')
            ->where('booking_purposes.is_active', 1)
            ->where('booking_purpose_groups.is_active', 1)
            ->orderBy('booking_purpose_groups.sort_order', 'ASC')
            ->orderBy('booking_purpose_groups.name', 'ASC')
            ->orderBy('booking_purposes.name', 'ASC')
            ->findAll();

        $grouped = [];

        foreach ($purposes as $purpose) {
            $groupName = trim((string) ($purpose['group_name'] ?? '')) ?: 'Other';
            $grouped[$groupName][] = $purpose;
        }

        return $grouped;
    }
}
