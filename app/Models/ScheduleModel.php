<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table            = 'schedules';
    protected $primaryKey       = 'sched_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['usched_id', 'commercial', 'program', 'platform', 'marketing_ex', 'added_by', 'remarks', 'published', 'total_budget'];

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

    public function getSchedules($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue = "", $filters = null)
    {
        $data = array();

        $builder = $this->db->table('schedules as s');
        $builder->where('s.deleted_at IS NULL', null, false);

        // Total number of records without filtering

        $data['totalRecords'] = $builder->countAllResults();

        // Total number of records with filtering

        $query = $builder->select('s.sched_id');

        // Filter by Search Value
        if ($searchValue != '') {
            $query->orLike('s.usched_id', $searchValue);
        }

        // Filter by Programs
        if (isset($filters['program']) && !empty($filters['program'])) {
            $query->where('s.program', $filters['program']['name']);
        }

        // Filter by Commercial
        if (isset($filters['commercial']) && !empty($filters['commercial'])) {
            $query->where('s.commercial', $filters['commercial']['name']);
        }

        // Filter by Platform
        if (isset($filters['platform']) && !empty($filters['platform'])) {
            $query->where('s.platform', $filters['platform']['name']);
        }

        // Filter by Client & Format
        if (isset($filters['commercials']) && !empty($filters['commercials'])) {
            $query->whereIn('s.commercial', $filters['commercials']['ids'], false);
        }

        // Filter by Date Range (Schedule IDs)
        if (isset($filters['schedules']) && !empty($filters['schedules'])) {
            $query->whereIn('s.sched_id', $filters['schedules']['ids'], false);
        }

        $builder->where('s.deleted_at IS NULL', null, false);

        $data['totalRecordwithFilter'] = $builder->countAllResults(false);

        // Fetch Records

        $query = $builder->select($returnFields);

        // Filter by Search Value
        if ($searchValue != '') {
            $query->orLike('c.name', $searchValue);
            $query->orLike('c.ucom_id', $searchValue);
        }

        // Filter by Programs
        if (isset($filters['program']) && !empty($filters['program'])) {
            $query->where('program', $filters['program']['name']);
        }

        // Filter by Commercial
        if (isset($filters['commercial']) && !empty($filters['commercial'])) {
            $query->where('s.commercial', $filters['commercial']['name']);
        }

        // Filter by Platform
        if (isset($filters['platform']) && !empty($filters['platform'])) {
            $query->where('s.platform', $filters['platform']['name']);
        }

        // Filter by Client & Format
        if (isset($filters['commercials']) && !empty($filters['commercials'])) {
            $query->whereIn('s.commercial', $filters['commercials']['ids'], false);
        }

        // Filter by Date Range (Schedule IDs)
        if (isset($filters['schedules']) && !empty($filters['schedules'])) {
            $query->whereIn('s.sched_id', $filters['schedules']['ids'], false);
        }

        // Default Filters by DataTable
        if (isset($columnName) &&  isset($columnSortOrder)) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        if (isset($rowsPerPage)) {
            $query->limit($rowsPerPage);
        }

        if (isset($start)) {
            $query->offset($start);
        }

        $clientSubquery = $this->db->table('clients');
        $clientSubquery->select('*');

        $clientSubqueryString = $clientSubquery->getCompiledSelect();

        $formatSubquery = $this->db->table('formats');
        $formatSubquery->select('*');

        $formatSubqueryString = $formatSubquery->getCompiledSelect();

        $query->join('commercials as c', 'c.com_id = s.commercial');
        $query->join('programs as p', 'p.prog_id = s.program');
        // $query->join('spots as sp', 'sp.spot_id = s.spot');
        $query->join('platforms as pl', 'pl.pfm_id = s.platform');
        $query->join('users as u', 'u.id = s.added_by');

        $query->join('(' . $clientSubqueryString . ') as cl', 'c.client = cl.client_id');
        $query->join('(' . $formatSubqueryString . ') as f', 'c.format = f.format_id');

        $query->join('schedule_items as si', 'si.sched_id = s.sched_id', 'left');
        $query->groupBy('s.sched_id');

        $data['records'] = $query->get()->getResultArray();

        return $data;
    }

    public function getDailySchedule($filters)
    {
        if (isset($filters) && !is_null($filters)) {
            $date = $filters['date'];
            $platform = $filters['platform'];
        }

        $data = array();

        $builder = $this->db->table('programs as p');
        $builder->select('prog_id, name as program_name, thumbnail');
        $builder->where('p.deleted_at IS NULL', null, false);

        $programs = $builder->get()->getResultArray();

        if (isset($programs) && !is_null($programs)) {
            for ($i = 0; $i < count($programs); $i++) {
                $schdule = $this->getAllSchedulesGroupedByPlatform($programs[$i]['prog_id'], $date, $platform);

                if (count($schdule) > 0) {
                    // Assign Schedule to the array
                    $data[$i]['schedule'] = $schdule;
                    // Assign Program to the array
                    $data[$i]['program'] = $programs[$i];
                }
            }
        }

        return $data;
    }

    public function getAllSchedules($program, $date, $platform = NULL)
    {
        $builder = $this->db->table('schedule_items as si');
        $builder->select('s.sched_id, si.scd_id, pl.name as platform, pl.channel as channel, c.duration as duration, c.name as commercial, c.category as category, c.sub_category as sub_category, sp.name as spot, sp.priority, si.published, si.link, si.remarks, f.name as format');
        $builder->where('s.deleted_at IS NULL', null, false);
        $builder->where('si.deleted_at IS NULL', null, false);
        $builder->where('si.sched_date', $date);
        $builder->where('s.program', $program);

        if (isset($platform) && !is_null($platform)) {
            $builder->where('s.platform', $platform);
        }

        $builder->join('schedules as s', 's.sched_id = si.sched_id');
        $builder->join('commercials as c', 'c.com_id = s.commercial');
        $builder->join('programs as p', 'p.prog_id = s.program');
        $builder->join('spots as sp', 'sp.spot_id = si.spot');
        $builder->join('platforms as pl', 'pl.pfm_id = s.platform');
        $builder->join('formats as f', 'f.format_id = c.format');

        // Order first by platform order (ascending by platform id or name)
        // Then order by spot priority ascending
        $builder->orderBy('pl.pfm_id', 'asc');
        $builder->orderBy('sp.priority', 'asc');

        return $builder->get()->getResultArray();
    }

    public function getAllSchedulesGroupedByPlatform($program, $date, $platform = null)
    {
        $builder = $this->db->table('schedule_items as si');
        $builder->select('
        s.sched_id,
        s.remarks as schedule_remarks,
        si.scd_id,
        pl.pfm_id,
        pl.name as platform_name,
        pl.channel,
        c.duration,
        c.name as commercial,
        c.category,
        c.sub_category,
        sp.name as spot,
        sp.priority,
        si.published,
        si.link,
        si.remarks as item_remarks,
        f.name as format
    ');
        $builder->where('s.deleted_at IS NULL', null, false);
        $builder->where('si.deleted_at IS NULL', null, false);
        $builder->where('si.sched_date', $date);
        $builder->where('s.program', $program);

        if ($platform !== null) {
            $builder->where('s.platform', $platform);
        }

        $builder->join('schedules as s', 's.sched_id = si.sched_id');
        $builder->join('commercials as c', 'c.com_id = s.commercial');
        $builder->join('programs as p', 'p.prog_id = s.program');
        $builder->join('spots as sp', 'sp.spot_id = si.spot');
        $builder->join('platforms as pl', 'pl.pfm_id = s.platform');
        $builder->join('formats as f', 'f.format_id = c.format');

        $builder->orderBy('pl.pfm_id', 'asc');
        $builder->orderBy('sp.priority', 'asc');

        $results = $builder->get()->getResultArray();

        $grouped = [];

        foreach ($results as $item) {
            $pfm_id = $item['pfm_id'];
            if (!isset($grouped[$pfm_id])) {
                $grouped[$pfm_id] = [
                    'platform_name' => $item['platform_name'],
                    'channel' => $item['channel'],
                    'items' => [],
                ];
            }
            $grouped[$pfm_id]['items'][] = $item;
        }

        return $grouped;
    }

    public function getScheduleRemarks($scheduleId)
    {
        return $this->select('remarks, sched_id')
            ->where('sched_id', $scheduleId)
            ->where('remarks IS NOT NULL', null, false)
            ->get()
            ->getRowArray();
    }

    public function getSchedulesForAccounts($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue = "", $filters = null)
    {
        $data = array();

        $builder = $this->db->table('schedules as s');
        $builder->where('s.deleted_at IS NULL', null, false);

        // Total number of records without filtering

        $data['totalRecords'] = $builder->countAllResults();

        // Total number of records with filtering

        $query = $builder->select('s.sched_id');

        // Filter by Search Value
        if ($searchValue != '') {
            $query->orLike('s.usched_id', $searchValue);
        }

        // Filter by Date Range (Schedule IDs)
        if (isset($filters['schedules']) && !empty($filters['schedules'])) {
            $query->whereIn('s.sched_id', $filters['schedules']['ids'], false);
        }

        // Filter by Programs
        if (isset($filters['program']) && !empty($filters['program'])) {
            $query->where('s.program', $filters['program']['name']);
        }

        // Filter by Commercial
        if (isset($filters['commercial']) && !empty($filters['commercial'])) {
            $query->where('s.commercial', $filters['commercial']['name']);
        }

        // Filter by Platform
        if (isset($filters['platform']) && !empty($filters['platform'])) {
            $query->where('s.platform', $filters['platform']['name']);
        }

        // Filter by Client & Format
        if (isset($filters['commercials']) && !empty($filters['commercials'])) {
            $query->whereIn('s.commercial', $filters['commercials']['ids'], false);
        }

        $builder->where('s.deleted_at IS NULL', null, false);

        $data['totalRecordwithFilter'] = $builder->countAllResults(false);

        // Fetch Records

        $query = $builder->select($returnFields);

        // Filter by Search Value
        if ($searchValue != '') {
            $query->orLike('c.name', $searchValue);
            $query->orLike('c.ucom_id', $searchValue);
        }

        // Filter by Date Range (Schedule IDs)
        if (isset($filters['schedules']) && !empty($filters['schedules'])) {
            $query->whereIn('s.sched_id', $filters['schedules']['ids'], false);
        }

        // Filter by Programs
        if (isset($filters['program']) && !empty($filters['program'])) {
            $query->where('program', $filters['program']['name']);
        }

        // Filter by Commercial
        if (isset($filters['commercial']) && !empty($filters['commercial'])) {
            $query->where('s.commercial', $filters['commercial']['name']);
        }

        // Filter by Platform
        if (isset($filters['platform']) && !empty($filters['platform'])) {
            $query->where('s.platform', $filters['platform']['name']);
        }

        // Filter by Client & Format
        if (isset($filters['commercials']) && !empty($filters['commercials'])) {
            $query->whereIn('s.commercial', $filters['commercials']['ids'], false);
        }

        // Default Filters by DataTable
        if (isset($columnName) &&  isset($columnSortOrder)) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        if (isset($rowsPerPage)) {
            $query->limit($rowsPerPage);
        }

        if (isset($start)) {
            $query->offset($start);
        }

        $clientSubquery = $this->db->table('clients');
        $clientSubquery->select('*');

        $clientSubqueryString = $clientSubquery->getCompiledSelect();

        $formatSubquery = $this->db->table('formats');
        $formatSubquery->select('*');

        $formatSubqueryString = $formatSubquery->getCompiledSelect();

        $query->join('commercials as c', 'c.com_id = s.commercial');
        $query->join('programs as p', 'p.prog_id = s.program');
        $query->join('spots as sp', 'sp.spot_id = s.spot');
        $query->join('platforms as pl', 'pl.pfm_id = s.platform');
        $query->join('users as u', 'u.id = s.added_by');

        $query->join('(' . $clientSubqueryString . ') as cl', 'c.client = cl.client_id');
        $query->join('(' . $formatSubqueryString . ') as f', 'c.format = f.format_id');

        $data['records'] = $query->groupBy('usched_id')->get()->getResultArray();

        return ($data);
    }

    public function scheduleIdCheck($id)
    {
        $builder = $this->db->table('schedules');
        $builder->select('usched_id')->where('usched_id', $id);

        if ($builder->countAllResults() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getLastScheduleID()
    {
        // Get the last inserted ID from your table
        $builder = $this->db->table('schedules');
        $query = $builder->select('usched_id, created_at')->orderBy('usched_id', 'DESC')->limit(1)->get();

        return $query->getRow();
    }
}
