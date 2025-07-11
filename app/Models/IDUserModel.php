<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class IDUserModel extends ShieldUserModel
{
    protected function initialize(): void
    {
        parent::initialize();

        $this->allowedFields = [
            ...$this->allowedFields,
            'first_name',
            'last_name',
            'deleted_at'
        ];
    }

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

    // Function to restore a deleted user
    public function restoreUser($id)
    {
        return $this->set('deleted_at', null)
            ->where('id', $id)
            ->update();
    }
}
