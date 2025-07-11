<?php

namespace App\Models;

use CodeIgniter\Model;

class CommercialModel extends Model
{
    protected $table            = 'commercials';
    protected $primaryKey       = 'com_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['ucom_id', 'name', 'duration', 'format', 'client', 'added_by', 'category', 'sub_category', 'remarks', 'link'];

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

    public function getCommercials($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue = "")
    {
        $data = array();

        $builder = $this->db->table('commercials as c');
        $builder->where('c.deleted_at IS NULL', null, false);

        // Total number of records without filtering

        $data['totalRecords'] = $builder->countAllResults();

        // Total number of records with filtering

        $query = $builder->select('c.com_id');

        if ($searchValue != '') {
            $query->orLike('c.name', $searchValue);
            $query->orLike('c.ucom_id', $searchValue);
        }

        $builder->where('c.deleted_at IS NULL', null, false);

        $data['totalRecordwithFilter'] = $builder->countAllResults(false);

        // Fetch Records

        $query = $builder->select($returnFields);

        if ($searchValue != '') {
            $query->orLike('c.name', $searchValue);
            $query->orLike('c.ucom_id', $searchValue);
        }

        if (isset($columnName) &&  isset($columnSortOrder)) {
            $query->orderBy($columnName, $columnSortOrder);
        }

        if (isset($rowsPerPage)) {
            $query->limit($rowsPerPage);
        }

        if (isset($start)) {
            $query->offset($start);
        }

        $query->join('formats as f', 'f.format_id = c.format');
        $query->join('clients as cl', 'cl.client_id = c.client');
        $query->join('users as u', 'u.id = c.added_by');

        $data['records'] = $query->get()->getResultArray();

        return ($data);
    }

    public function commercialIdCheck($id)
    {
        $builder = $this->db->table('commercials');
        $builder->select('ucom_id')->where('ucom_id', $id);

        if ($builder->countAllResults() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function commercialsByClient($client)
    {
        $builder = $this->db->table('commercials');
        $builder->where('deleted_at IS NULL', null, false);
        $query = $builder->select('com_id')->where('client', $client);
        return $query->get()->getResultArray();
    }

    public function commercialsByFormat($format)
    {
        $builder = $this->db->table('commercials');
        $builder->where('deleted_at IS NULL', null, false);
        $query = $builder->select('com_id')->where('format', $format);
        return $query->get()->getResultArray();
    }

    public function getLastCommercialID()
    {
        // Get the last inserted ID from your table
        $builder = $this->db->table('commercials');
        $query = $builder->select('ucom_id, created_at')->orderBy('ucom_id', 'DESC')->limit(1)->get();

        return $query->getRow();
    }
}
