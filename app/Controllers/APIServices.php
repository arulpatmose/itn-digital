<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProgramModel;
use App\Models\SpotModel;
use App\Models\FormatModel;
use App\Models\ClientModel;
use App\Models\CommercialModel;
use App\Models\UserModel;
use App\Models\PlatformModel;
use App\Models\ScheduleItemModel;
use App\Models\ScheduleModel;

class APIServices extends BaseController
{
    protected $clientModel;
    protected $programModel;
    protected $spotModel;
    protected $formatModel;
    protected $commercialModel;
    protected $userModel;
    protected $platformModel;
    protected $scheduleModel;
    protected $scheduleItemModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->programModel = new ProgramModel();
        $this->spotModel = new SpotModel();
        $this->formatModel = new FormatModel();
        $this->commercialModel = new CommercialModel();
        $this->userModel = new UserModel();
        $this->platformModel = new PlatformModel();
        $this->scheduleModel = new ScheduleModel();
        $this->scheduleItemModel = new ScheduleItemModel();
    }

    public function index()
    {
        // Silence is golden
    }

    public function getAllPrograms()
    {
        if ($this->request->isAjax()) {
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
            $totalRecords = $this->programModel->select('prog_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $this->programModel->select('prog_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $this->programModel->select('*');

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
            $totalRecords = $this->spotModel->select('spot_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $this->spotModel->select('spot_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $this->spotModel->select('*');

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
            $totalRecords = $this->formatModel->select('format_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery =  $this->formatModel->select('format_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $this->formatModel->select('*');

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
            $totalRecords = $this->clientModel->select('client_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $this->clientModel->select('client_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $this->clientModel->select('*');

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
            $totalRecords = $this->platformModel->select('pfm_id')->countAllResults();

            // Total number of records with filtering
            $searchQuery = $this->platformModel->select('pfm_id');

            if ($searchValue != '') {
                $searchQuery->orLike('name', $searchValue);
            }

            $totalRecordwithFilter = $searchQuery->countAllResults();

            // Fetch Records
            $searchQuery = $this->platformModel->select('*');

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

            $results = $this->commercialModel->getCommercials($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue);

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

                $schedulesByDate = $this->scheduleItemModel->schedulesDyDate($_dateRange);

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
                $commercialsByClient = $this->commercialModel->commercialsByClient($client);

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
                $commercialsByFormat = $this->commercialModel->commercialsByFormat($format);

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

            $results = $this->scheduleModel->getSchedules($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue, $filters);

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

    public function getAllUsers()
    {
        if ($this->request->isAjax()) {
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

            $results = $this->userModel->getUsers($returnFields, $columnName, $columnSortOrder, $rowsPerPage, $start, $searchValue);

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
                $_userGroups = $this->userModel->find($record['id'])->getGroups();

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
                $searchQuery = $this->commercialModel->select('com_id, ucom_id, , name');
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
                $searchQuery = $this->programModel->select('prog_id, name');
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
                $searchQuery = $this->clientModel->select('client_id, name');
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
                $numberofrecords = 100;

                $searchQuery = $this->spotModel->select('spot_id, name');
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
