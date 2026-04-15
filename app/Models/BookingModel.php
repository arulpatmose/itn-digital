<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'user_id',
        'resource_id',
        'purpose_id',
        'time_slot_id',
        'booking_date',
        'status',
        'remarks',
        'approval_remarks',
        'approved_by',
    ];

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

    public function getFullDetails($filters = [])
    {
        $builder = $this->select('
            bookings.*,
            CONCAT(requester.first_name, " ", requester.last_name) AS user_name,
            CONCAT(approver.first_name, " ", approver.last_name) AS approved_by_name,
            resources.name AS resource_name,
            time_slots.label AS time_label,
            time_slots.start_time AS time_start,
            time_slots.end_time AS time_end,
            resource_types.name AS resource_type,
            booking_purposes.name AS booking_purpose
        ')
            ->join('users AS requester', 'requester.id = bookings.user_id', 'left')
            ->join('users AS approver', 'approver.id = bookings.approved_by', 'left')
            ->join('resources', 'resources.id = bookings.resource_id', 'left')
            ->join('time_slots', 'time_slots.id = bookings.time_slot_id', 'left')
            ->join('resource_types', 'resource_types.id = resources.type_id', 'left')
            ->join('booking_purposes', 'booking_purposes.id = bookings.purpose_id', 'left');

        if (!empty($filters)) {
            $builder->where($filters);
        }

        return $builder->orderBy('bookings.booking_date', 'DESC')->findAll();
    }

    public function isSlotTaken(int $resourceId, string $date, int $timeSlotId, ?int $excludeBookingId = null): bool
    {
        $builder = $this->where('resource_id', $resourceId)
            ->where('booking_date', $date)
            ->where('time_slot_id', $timeSlotId)
            ->whereNotIn('status', ['rejected', 'cancelled']);

        if ($excludeBookingId !== null) {
            $builder->where('id !=', $excludeBookingId);
        }

        return $builder->countAllResults() > 0;
    }
}
