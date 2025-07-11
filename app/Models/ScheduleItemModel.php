<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleItemModel extends Model
{
    protected $table            = 'schedule_items';
    protected $primaryKey       = 'scd_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['sched_id', 'sched_date', 'spot', 'remarks', 'link', 'published', 'added_by', 'updated_by'];

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

    public function schedulesDyDate($dates)
    {
        $builder = $this->db->table('schedule_items');
        $builder->where('deleted_at IS NULL', null, false);
        $builder->where('sched_date >=', $dates['fromDate']);
        $builder->where('sched_date <=', $dates['toDate']);

        $query = $builder->select('sched_id');

        return $query->get()->getResultArray();
    }

    public function getScheduleItems($scheduleID)
    {
        $builder = $this->db->table('schedule_items as s');
        $builder->select(
            's.published,
            s.scd_id,
            s.sched_id,
            s.sched_date,
            s.remarks,
            s.link,
            ss.name as spot_name,
            ss.priority as spot_priority,
            ua.first_name as a_first_name,
            s.created_at,
            IFNULL(uu.first_name, "N/A") as u_first_name'
        );

        $builder->where('s.deleted_at IS NULL', null, false);
        $builder->where('s.sched_id', $scheduleID);
        $builder->join('users as ua', 'ua.id = s.added_by', 'left');
        $builder->join('users as uu', 'uu.id = s.updated_by', 'left');
        $builder->join('spots as ss', 'ss.spot_id = s.spot', 'left');
        $builder->orderBy('s.sched_date', 'ASC');
        $query = $builder->get()->getResultArray();

        return $query;
    }

    public function getCommentsByScheduleItem($itemId)
    {
        return $this->select('remarks, scd_id')
            ->where('scd_id', $itemId)
            ->where('remarks IS NOT NULL', null, false)
            ->get()
            ->getRowArray();
    }
}
