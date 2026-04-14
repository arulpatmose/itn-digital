<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
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
        $data = [];

        // Whitelist sortable columns to prevent SQL injection
        $allowedSortColumns = ['id', 'first_name', 'last_name', 'last_active', 'status', 'active'];
        if (!in_array($columnName, $allowedSortColumns)) {
            $columnName    = 'id';
            $columnSortOrder = 'desc';
        }

        // Total records — all users including soft-deleted
        $data['totalRecords'] = $this->db->table('users')->countAllResults();

        // Filtered count
        $filteredBuilder = $this->db->table('users');
        if ($searchValue !== '') {
            $filteredBuilder->groupStart()
                ->like('first_name', $searchValue)
                ->orLike('last_name', $searchValue)
                ->groupEnd();
        }
        $data['totalRecordwithFilter'] = $filteredBuilder->countAllResults();

        // Fetch records
        $builder = $this->db->table('users');
        $builder->select($returnFields);

        if ($searchValue !== '') {
            $builder->groupStart()
                ->like('first_name', $searchValue)
                ->orLike('last_name', $searchValue)
                ->groupEnd();
        }

        $builder->orderBy($columnName, $columnSortOrder);
        $builder->limit($rowsPerPage, $start);

        $data['records'] = $builder->get()->getResultArray();

        return $data;
    }

    // Function to restore a deleted user
    public function restoreUser($id)
    {
        return $this->set('deleted_at', null)
            ->where('id', $id)
            ->update();
    }
}
