<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel;

class IDUserModel extends UserModel
{
    protected $allowedFields  = [
        'username',
        'status',
        'status_message',
        'active',
        'last_active',
        'deleted_at',
        'first_name',
        'last_name'
    ];

    public function getUsers($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue = "")
    {
        $data = array();

        $builder = $this->db->table('users');
        $builder->where('deleted_at IS NULL', null, false);

        // Total number of records without filtering

        $data['totalRecords'] = $builder->countAllResults();

        // Total number of records with filtering

        $query = $builder->select('users.id');

        if ($searchValue != '') {
            $query->orLike('first_name', $searchValue);
        }

        $builder->where('deleted_at IS NULL', null, false);

        $data['totalRecordwithFilter'] = $builder->countAllResults(false);

        // Fetch Records

        $query = $builder->select($returnFields);

        if ($searchValue != '') {
            $query->orLike('first_name', $searchValue);
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

        $data['records'] = $query->get()->getResultArray();

        return ($data);
    }
}
