<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProgramsModel;
use App\Models\SpotsModel;
use App\Models\FormatsModel;
use App\Models\ClientsModel;
use App\Models\CommercialsModel;
use App\Models\IDUserModel;
use App\Models\PlatformsModel;
use App\Models\ScheduleItemsModel;
use App\Models\SchedulesModel;

class APIServices extends BaseController
{
    public function index()
    {
        // Silence is golden
    }

    public function getAllPrograms()
    {
        if ($this->request->isAjax()) {
            $programModel = new ProgramsModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            // Read Values
            $draw = $dtPostData['draw'];
            $start = $dtPostData['start'];
            $rowsPerPage = $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            // Total number of records without filtering
            $totalRecords = $programModel->select('prog_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $programModel->select('prog_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $programModel->select('*');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $records = $searchQuery->orderBy($columnName, $columnSortOrder)->findAll($rowsPerPage, $start);

            // Prepare Data
            $data = array();

            foreach ($records as $record) {
                $data[] = array(
                    "prog_id" => $record['prog_id'],
                    "thumbnail" => $record['thumbnail'],
                    "name" => $record['name'],
                    "type" => $record['type']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getAllSpots()
    {
        if ($this->request->isAjax()) {
            $spotsModel = new SpotsModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            // Read Values
            $draw = $dtPostData['draw'];
            $start = $dtPostData['start'];
            $rowsPerPage = $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            // Total number of records without filtering
            $totalRecords = $spotsModel->select('spot_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $spotsModel->select('spot_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $spotsModel->select('*');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $records = $searchQuery->orderBy($columnName, $columnSortOrder)->findAll($rowsPerPage, $start);

            // Prepare Data
            $data = array();

            foreach ($records as $record) {
                $data[] = array(
                    "spot_id" => $record['spot_id'],
                    "name" => $record['name'],
                    "priority" => $record['priority']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getAllFormats()
    {
        if ($this->request->isAjax()) {
            $formatsModel = new FormatsModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            // Read Values
            $draw = $dtPostData['draw'];
            $start = $dtPostData['start'];
            $rowsPerPage = $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            // Total number of records without filtering
            $totalRecords = $formatsModel->select('format_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $formatsModel->select('format_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $formatsModel->select('*');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $records = $searchQuery->orderBy($columnName, $columnSortOrder)->findAll($rowsPerPage, $start);

            // Prepare Data
            $data = array();

            foreach ($records as $record) {
                $data[] = array(
                    "format_id" => $record['format_id'],
                    "name" => $record['name'],
                    "code" => $record['code']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getAllClients()
    {
        if ($this->request->isAjax()) {
            $clientsModel = new ClientsModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            // Read Values
            $draw = $dtPostData['draw'];
            $start = $dtPostData['start'];
            $rowsPerPage = $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            // Total number of records without filtering
            $totalRecords = $clientsModel->select('client_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $clientsModel->select('client_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $clientsModel->select('*');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $records = $searchQuery->orderBy($columnName, $columnSortOrder)->findAll($rowsPerPage, $start);

            // Prepare Data
            $data = array();

            foreach ($records as $record) {
                $data[] = array(
                    "client_id" => $record['client_id'],
                    "name" => $record['name'],
                    "address" => $record['address']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getAllPlatforms()
    {
        if ($this->request->isAjax()) {
            $platformsModel = new PlatformsModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            // Read Values
            $draw = $dtPostData['draw'];
            $start = $dtPostData['start'];
            $rowsPerPage = $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            // Total number of records without filtering
            $totalRecords = $platformsModel->select('pfm_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $platformsModel->select('pfm_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $platformsModel->select('*');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $records = $searchQuery->orderBy($columnName, $columnSortOrder)->findAll($rowsPerPage, $start);

            // Prepare Data
            $data = array();

            foreach ($records as $record) {
                $data[] = array(
                    "pfm_id" => $record['pfm_id'],
                    "name" => $record['name'],
                    "channel" => $record['channel']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getAllCommercials()
    {
        if ($this->request->isAjax()) {
            $commercialsModel = new CommercialsModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            // Read Values
            $draw = $dtPostData['draw'];
            $start = $dtPostData['start'];
            $rowsPerPage = $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            $returnFields = 'com_id, ucom_id, c.name, f.name as format, c.duration, cl.name as client, u.first_name as added_by, c.remarks, c.category, c.sub_category, c.link';

            $results = $commercialsModel->getCommercials($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue);

            // Total number of records without filtering
            $totalRecords = $results['totalRecords'];

            // Total number of records with filtering
            $totalRecordwithFilter = $results['totalRecordwithFilter'];

            // Fetch Records
            $records = $results['records'];

            // Prepare Data
            $data = array();

            foreach ($records as $record) {
                $data[] = array(
                    "com_id" => $record['com_id'],
                    "ucom_id" => $record['ucom_id'],
                    "name" => $record['name'],
                    "duration" => $record['duration'],
                    "format" => $record['format'],
                    "category" => $record['category'],
                    "sub_category" => $record['sub_category'],
                    "client" => $record['client'],
                    "added_by" => $record['added_by'],
                    "remarks" => $record['remarks'],
                    "link" => $record['link']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getAllSchedules()
    {
        if ($this->request->isAjax()) {
            $schedulesModel = new SchedulesModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            $dateRange = $dtPostData['dateRange'];
            $program = $dtPostData['program'];
            $commercial = $dtPostData['commercial'];
            $platform = $dtPostData['platform'];
            $client = $dtPostData['client'];
            $format = $dtPostData['format'];

            $filters = array();

            if (isset($dateRange) && !empty($dateRange)) {
                $dates = explode(' to ', $dateRange);

                if (isset($dates[0]) && isset($dates[1])) {
                    $_dateRange = array(
                        'fromDate' => $dates[0],
                        'toDate' => $dates[1]
                    );
                }

                $scheduleItemsModel = new ScheduleItemsModel();

                $schedulesByDate = $scheduleItemsModel->schedulesDyDate($_dateRange);

                $scheduleIDs = array();

                foreach ($schedulesByDate as $scheduleItem) {
                    array_push($scheduleIDs, $scheduleItem['sched_id']);
                }

                if (!empty($scheduleIDs)) {
                    $filters['schedules'] = array(
                        'ids' => array(implode(',', array_unique($scheduleIDs)))
                    );
                } else {
                    $filters['schedules'] = array(
                        'ids' => array(0)
                    );
                }
            }

            if (isset($program) && !empty($program)) {
                $filters['program'] = array(
                    'name' => $program
                );
            }

            if (isset($commercial) && !empty($commercial)) {
                $filters['commercial'] = array(
                    'name' => $commercial
                );
            }

            if (isset($platform) && !empty($platform)) {
                $filters['platform'] = array(
                    'name' => $platform
                );
            }

            if (isset($client) && !empty($client)) {
                $commercialsModel = new CommercialsModel();

                $commercialsByClient = $commercialsModel->commercialsByClient($client);

                $commercialIDs = array();

                foreach ($commercialsByClient as $commercial) {
                    array_push($commercialIDs, $commercial['com_id']);
                }

                if (!empty($commercialIDs)) {
                    $filters['commercials'] = array(
                        'ids' => array(implode(',', $commercialIDs))
                    );
                } else {
                    $filters['commercials'] = array(
                        'ids' => array(0)
                    );
                }
            }

            if (isset($format) && !empty($format)) {
                $commercialsModel = new CommercialsModel();

                $commercialsByFormat = $commercialsModel->commercialsByFormat($format);

                $commercialIDs = array();

                foreach ($commercialsByFormat as $commercial) {
                    array_push($commercialIDs, $commercial['com_id']);
                }

                if (!empty($commercialIDs)) {
                    $filters['commercials'] = array(
                        'ids' => array(implode(',', $commercialIDs))
                    );
                } else {
                    $filters['commercials'] = array(
                        'ids' => array(0)
                    );
                }
            }

            // Read Values
            $draw = $dtPostData['draw'];
            $start = $dtPostData['start'];
            $rowsPerPage = $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            $concatCommercials = 'CONCAT("<span class=\"me-1 text-success\">", c.ucom_id, "</span>", " - " , c.name, "<span class=\"ms-1 text-gray-dark\">(", c.duration , "s)</span>" ) AS commercial';
            $returnFields = 'usched_id, sched_id,' . $concatCommercials . ', p.name as program, pl.name as platform, pl.channel as channel, u.first_name as added_by, s.remarks, cl.name as client_name, f.name as format, s.published, s.marketing_ex, s.total_budget';

            $results = $schedulesModel->getSchedules($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue, $filters);

            // Total number of records without filtering
            $totalRecords = $results['totalRecords'];

            // Total number of records with filtering
            $totalRecordwithFilter = $results['totalRecordwithFilter'];

            // Fetch Records
            $records = $results['records'];

            // Prepare Data
            $data = array();

            foreach ($records as $record) {
                $data[] = array(
                    "published" => $record['published'],
                    "usched_id" => $record['usched_id'],
                    "sched_id" => $record['sched_id'],
                    "commercial" => $record['commercial'],
                    "client_name" => $record['client_name'],
                    "format" => $record['format'],
                    "program" => $record['program'],
                    "platform" => $record['platform'],
                    "channel" => $record['channel'],
                    "marketing_ex" => $record['marketing_ex'],
                    "total_budget" => $record['total_budget'],
                    "added_by" => $record['added_by'],
                    "remarks" => $record['remarks']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getAllScheduleForAccounts()
    {
        if ($this->request->isAjax()) {
            $schedulesModel = new SchedulesModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            $dateRange = $dtPostData['dateRange'];
            $program = $dtPostData['program'];
            $commercial = $dtPostData['commercial'];
            $platform = $dtPostData['platform'];
            $client = $dtPostData['client'];
            $format = $dtPostData['format'];

            $filters = array();

            if (isset($dateRange) && !empty($dateRange)) {
                $dates = explode(' to ', $dateRange);

                if (isset($dates[0]) && isset($dates[1])) {
                    $_dateRange = array(
                        'fromDate' => $dates[0],
                        'toDate' => $dates[1]
                    );
                }

                $scheduleItemsModel = new ScheduleItemsModel();

                $schedulesByDate = $scheduleItemsModel->schedulesDyDate($_dateRange);

                $scheduleIDs = array();

                foreach ($schedulesByDate as $scheduleItem) {
                    array_push($scheduleIDs, $scheduleItem['sched_id']);
                }

                if (!empty($scheduleIDs)) {
                    $filters['schedules'] = array(
                        'ids' => array(implode(',', array_unique($scheduleIDs)))
                    );
                } else {
                    $filters['schedules'] = array(
                        'ids' => array(0)
                    );
                }
            }

            if (isset($program) && !empty($program)) {
                $filters['program'] = array(
                    'name' => $program
                );
            }

            if (isset($commercial) && !empty($commercial)) {
                $filters['commercial'] = array(
                    'name' => $commercial
                );
            }

            if (isset($platform) && !empty($platform)) {
                $filters['platform'] = array(
                    'name' => $platform
                );
            }

            if (isset($client) && !empty($client)) {
                $commercialsModel = new CommercialsModel();

                $commercialsByClient = $commercialsModel->commercialsByClient($client);

                $commercialIDs = array();

                foreach ($commercialsByClient as $commercial) {
                    array_push($commercialIDs, $commercial['com_id']);
                }

                if (!empty($commercialIDs)) {
                    $filters['commercials'] = array(
                        'ids' => array(implode(',', $commercialIDs))
                    );
                } else {
                    $filters['commercials'] = array(
                        'ids' => array(0)
                    );
                }
            }

            if (isset($format) && !empty($format)) {
                $commercialsModel = new CommercialsModel();

                $commercialsByFormat = $commercialsModel->commercialsByFormat($format);

                $commercialIDs = array();

                foreach ($commercialsByFormat as $commercial) {
                    array_push($commercialIDs, $commercial['com_id']);
                }

                if (!empty($commercialIDs)) {
                    $filters['commercials'] = array(
                        'ids' => array(implode(',', $commercialIDs))
                    );
                } else {
                    $filters['commercials'] = array(
                        'ids' => array(0)
                    );
                }
            }

            // Read Values
            $draw = $dtPostData['draw'];
            $start = $dtPostData['start'];
            $rowsPerPage = $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            $concatCommercials = 'CONCAT("<span class=\"me-1 text-success\">", c.ucom_id, "</span>", " - " , c.name, "<span class=\"ms-1 text-gray-dark\">(", c.duration , "s)</span>" ) AS commercial';
            $countSchedules = '(SELECT COUNT(*) FROM schedule_items WHERE s.sched_id = schedule_items.sched_id AND schedule_items.deleted_at IS NULL) AS num_schedules';
            $dailyBudget = 'ROUND(s.total_budget / (SELECT COUNT(*) FROM schedule_items WHERE s.sched_id = schedule_items.sched_id AND schedule_items.deleted_at IS NULL), 2) AS daily_budget';
            $returnFields = 'usched_id, sched_id,' . $concatCommercials . ', sp.name as spot, p.name as program, pl.name as platform, pl.channel as channel, u.first_name as added_by, s.remarks, cl.name as client_name, f.name as format, s.published, s.marketing_ex, s.total_budget,' . $countSchedules . ', ' . $dailyBudget;

            $results = $schedulesModel->getSchedulesForAccounts($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue, $filters);

            // Total number of records without filtering
            $totalRecords = $results['totalRecords'];

            // Total number of records with filtering
            $totalRecordwithFilter = $results['totalRecordwithFilter'];

            // Fetch Records
            $records = $results['records'];

            // Prepare Data
            $data = array();

            foreach ($records as $record) {
                $data[] = array(
                    "sched_id" => $record['sched_id'],
                    "usched_id" => $record['usched_id'],
                    "commercial" => $record['commercial'],
                    "client_name" => $record['client_name'],
                    "format" => $record['format'],
                    "program" => $record['program'],
                    "platform" => $record['platform'],
                    "channel" => $record['channel'],
                    "marketing_ex" => $record['marketing_ex'],
                    "num_schedules" => $record['num_schedules'],
                    "daily_budget" => $record['daily_budget'],
                    "total_budget" => $record['total_budget']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getAllUsers()
    {
        if ($this->request->isAjax()) {
            $usersModel = new IDUserModel();

            $request = service('request');
            $postData = $request->getPost();

            $dtPostData = $postData['data'];
            $reponse = array();

            // Read Values
            $draw = (int) $dtPostData['draw'];
            $start = (int) $dtPostData['start'];
            $rowsPerPage = (int) $dtPostData['length']; // Rows Display Per Page
            $columnIndex = $dtPostData['order'][0]['column'];
            $columnName = $dtPostData['columns'][$columnIndex]['data'];
            $columnSortOrder = $dtPostData['order'][0]['dir']; // asc or desc
            $searchValue = $dtPostData['search']['value']; // Search value

            $returnFields = 'users.id as id, first_name, last_name, last_active';

            $results = $usersModel->getUsers($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue);

            // Total number of records without filtering
            $totalRecords = $results['totalRecords'];

            // Total number of records with filtering
            $totalRecordwithFilter = $results['totalRecordwithFilter'];

            // Fetch Records
            $records = $results['records'];

            // Prepare Data
            $data = array();

            // Get default groups
            $groups = setting('AuthGroups.groups');
            asort($groups);

            foreach ($records as $record) {
                $_userGroups = $usersModel->find($record['id'])->getGroups();

                $userGroups = array();

                foreach ($groups as $slug => $group) {
                    // Check if the slug is in the array and append the title if true
                    if (in_array($slug, $_userGroups)) {
                        $userGroups[] = $group['title'];
                    }
                }

                $data[] = array(
                    "id" => $record['id'],
                    "first_name" => $record['first_name'],
                    "last_name" => $record['last_name'],
                    "groups" => implode(",", $userGroups),
                    "last_active" => $record['last_active']
                );
            }

            // Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function getSelectOptions()
    {
        if ($this->request->isAjax()) {
            $response = array();

            $request = service('request');
            $postData = $request->getPost();

            if (isset($postData['optionsType'])) {
                $optionsType = $postData['optionsType'];
            }

            if (isset($postData['searchTerm'])) {
                $searchTerm = $postData['searchTerm'];
            }

            // Number of records fetch
            $numberofrecords = 10;

            if ($optionsType === 'commercial') {
                $commercialsModel = new CommercialsModel();

                $searchQuery = $commercialsModel->select('com_id, ucom_id, , name');
                if (!isset($searchTerm)) {
                    // Fetch records
                    $options = $searchQuery->orderBy('com_id', 'desc')->findAll($numberofrecords, 0);
                } else {
                    // Fetch records with filter
                    $options = $searchQuery->orLike('name', $searchTerm)->orLike('ucom_id', $searchTerm)->orderBy('com_id', 'desc')->findAll($numberofrecords, 0);
                }

                // Read Data
                foreach ($options as $option) {
                    $response[] = array(
                        "id" => $option['com_id'],
                        "text" => $option['ucom_id'] . ' - ' . $option['name']
                    );
                }
            }

            if ($optionsType === 'program') {
                $programsModel = new ProgramsModel();

                $searchQuery = $programsModel->select('prog_id, name');
                if (!isset($searchTerm)) {
                    // Fetch records
                    $options = $searchQuery->orderBy('prog_id', 'desc')->findAll($numberofrecords, 0);
                } else {
                    // Fetch records with filter
                    $options = $searchQuery->orLike('name', $searchTerm)->orderBy('prog_id', 'desc')->findAll($numberofrecords, 0);
                }

                // Read Data
                foreach ($options as $option) {
                    $response[] = array(
                        "id" => $option['prog_id'],
                        "text" => $option['name']
                    );
                }
            }

            if ($optionsType === 'client') {
                $clientsModel = new ClientsModel();

                $searchQuery = $clientsModel->select('client_id, name');
                if (!isset($searchTerm)) {
                    // Fetch records
                    $options = $searchQuery->orderBy('client_id', 'asc')->findAll($numberofrecords, 0);
                } else {
                    // Fetch records with filter
                    $options = $searchQuery->orLike('name', $searchTerm)->orderBy('client_id', 'asc')->findAll($numberofrecords, 0);
                }

                // Read Data
                foreach ($options as $option) {
                    $response[] = array(
                        "id" => $option['client_id'],
                        "text" => $option['name']
                    );
                }
            }

            if ($optionsType === 'spots') {
                $spotsModel = new SpotsModel();

                $numberofrecords = 100;

                $searchQuery = $spotsModel->select('spot_id, name');
                if (!isset($searchTerm)) {
                    // Fetch records
                    $options = $searchQuery->orderBy('priority', 'asc')->findAll($numberofrecords, 0);
                } else {
                    // Fetch records with filter
                    $options = $searchQuery->orLike('name', $searchTerm)->orderBy('priority', 'asc')->findAll($numberofrecords, 0);
                }

                // Read Data
                foreach ($options as $option) {
                    $response[] = array(
                        "id" => $option['spot_id'],
                        "text" => $option['name']
                    );
                }
            }

            return $this->response->setJSON($response);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
