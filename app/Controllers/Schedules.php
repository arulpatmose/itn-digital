<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommercialModel;
use App\Models\FormatModel;
use App\Models\PlatformModel;
use App\Models\ProgramModel;
use App\Models\ScheduleItemModel;
use App\Models\ScheduleModel;
use App\Models\SpotModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class Schedules extends BaseController
{
    protected $schedulesModel;
    protected $scheduleItemModel;
    protected $commercialModel;
    protected $platformModel;
    protected $formatModel;
    protected $spotModel;
    protected $programModel;

    public function __construct()
    {
        $this->schedulesModel = new ScheduleModel();
        $this->scheduleItemModel = new ScheduleItemModel();
        $this->commercialModel = new CommercialModel();
        $this->platformModel = new PlatformModel();
        $this->formatModel = new FormatModel();
        $this->spotModel = new SpotModel();
        $this->programModel = new ProgramModel();

        helper('format_name');
    }

    public function index()
    {
        if (!auth()->user()->can('schedules.access')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $data = [
            'platforms'        => $this->platformModel->select('pfm_id as id, name, channel')->findAll(),
            'formats'          => $this->formatModel->select('format_id as id, name')->findAll(),
            'page_title'       => "Schedules",
            'page_description' => "Optimize ads. Maximize impact. Perfect timing, every time."
        ];

        return view('backend/schedules/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('schedules.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $data = [
            'spots'             => $this->spotModel->select('spot_id as id, name')->findAll(),
            'platforms'         => $this->platformModel->select('pfm_id as id, name, channel')->findAll(),
            'page_title'        => "Create Schedule",
            'page_description'  => "Optimize ads. Maximize impact. Perfect timing, every time."
        ];

        return view('backend/schedules/add_schedule', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('schedules.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }


        $userID = auth()->id();

        $data = array();

        $selectedDates = $this->request->getVar('schedule-dates');
        $totalBudget = $this->request->getVar('schedule-budget');

        $delemeter = ', ';

        if (strpos($selectedDates, $delemeter)) {
            $dates = explode($delemeter, $selectedDates);
        } else {
            $dates = array($selectedDates);
        }

        $dailyBudget = $totalBudget / count($dates);

        $scheduleID = $this->generateUniqueID();

        $spotID = $this->request->getVar('schedule-spot');

        // Prepare data to insert for schedule

        $scheduleData = [
            "usched_id" => $scheduleID,
            "commercial" => $this->request->getVar('schedule-commercial'),
            "program" => $this->request->getVar('schedule-program'),
            // "spot" => $spotID ,
            "platform" => $this->request->getVar('schedule-platform'),
            "total_budget" => $this->request->getVar('schedule-budget'),
            "marketing_ex" => formatName($this->request->getVar('schedule-me')),
            "remarks" => $this->request->getVar('schedule-remarks'),
            "added_by" => $userID
        ];

        $this->schedulesModel->insert($scheduleData);

        $lastInsertID = $this->schedulesModel->insertID();

        // Prepare data to insert by selected dates

        if (isset($dates)) {
            foreach ($dates as $date) {
                $itemData = [
                    "sched_id" => $lastInsertID,
                    "sched_date" => $date,
                    "spot" => $spotID,
                    "added_by" => $userID
                ];

                $this->scheduleItemModel->insert($itemData);
            }
        }

        // Insert into database
        if ($this->schedulesModel->affectedRows() > 0 && $this->scheduleItemModel->affectedRows() > 0) {
            $status = 'success';
            $message = 'The schedule was addded successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while adding the schedule!';
        }

        return redirect()->to('/schedules')->with($status, $message);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('schedules.edit')) {
            $status = 'error';
            $message = '<span class="my-3 d-block">You are not allowed to do this operation!</span><small>Please contact the Administrator.</small>';
            return redirect()->to('/schedules')->with($status, $message);
        }

        $schedule = $this->schedulesModel->find($id);

        if (isset($schedule) && !empty($schedule)) {
            $data = array_merge($data ?? [], [
                'schedule'    => $schedule,
                'platforms'   => $this->platformModel->select('pfm_id as id, name, channel')->findAll(),
                'commercial'  => $this->commercialModel->find($schedule['commercial']),
                'program'     => $this->programModel->find($schedule['program']),
            ]);

            $itemName = $data['commercial']['name'] ?? 'Commercial';

            $data = array_merge($data, [
                'page_title'       => "Edit Schedule for " . $itemName,
                'page_description' => "Optimize ads. Maximize impact. Perfect timing, every time.",
            ]);

            return view('backend/schedules/edit_schedule', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('schedules.edit')) {
            $status = 'error';
            $message = '<span class="my-3 d-block">You are not allowed to do this operation!</span><small>Please contact the Administrator.</small>';
            return redirect()->to('/schedules')->with($status, $message);
        } else {
            // Prepare data to update
            $data = [
                "commercial" => $this->request->getVar('schedule-commercial'),
                "program" => $this->request->getVar('schedule-program'),
                "platform" => $this->request->getVar('schedule-platform'),
                "total_budget" => $this->request->getVar('schedule-budget'),
                "marketing_ex" => formatName($this->request->getVar('schedule-me')),
                "remarks" => $this->request->getVar('schedule-remarks'),
            ];

            // Insert into database
            if ($this->schedulesModel->update($id, $data, false)) {
                $status = 'success';
                $message = 'The schedule was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the schedule!';
            }

            return redirect()->to('/schedules')->with($status, $message);
        }
    }

    public function destroy()
    {
        // Ensure it's an AJAX request
        if (!$this->request->isAjax()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method. AJAX request required.'
            ]); // Bad Request
        }

        $user = auth()->user();

        // Check base permission to delete schedules
        if (!$user->can('schedules.delete')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '<span class="my-3 d-block">You do not have permission to delete schedules.</span><small>Please contact the Administrator.</small>'
            ]); // Forbidden
        }

        $id = $this->request->getPost('id');

        // Fetch the schedule
        $schedule = $this->schedulesModel->find($id);

        if (!$schedule) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Schedule not found.'
            ])->setStatusCode(404); // Not Found
        }

        $scheduleID = $schedule['sched_id'];
        $scheduleDate = $schedule['created_at'];
        $cutoffTime = Time::today(); // Midnight today

        // Check for published items
        $items = $this->scheduleItemModel->where('sched_id', $scheduleID)->findAll();
        $hasPublishedItems = false;

        foreach ($items as $item) {
            if ($item['published'] === '1') {
                $hasPublishedItems = true;
                break;
            }
        }

        // Check if user is in the admin/superadmin group
        $isPrivileged = $user->inGroup('superadmin', 'admin');

        // Only privileged users can delete published or past schedules
        if (!$isPrivileged) {
            if ($hasPublishedItems) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => '<span class="my-3 d-block">This schedule includes a commercial that has already been published, and therefore, deletion is not permitted.</span><small>Please contact the Administrator.</small>'
                ]); // Forbidden
            }

            $scheduledDate = Time::parse($scheduleDate);

            if ($scheduledDate->isBefore($cutoffTime)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => '<span class="my-3 d-block">Only schedules created today or later can be deleted.</span><small>Please contact the Administrator.</small>'
                ]); // Forbidden
            }
        }

        // Proceed with deletion
        $this->scheduleItemModel->where('sched_id', $scheduleID)->delete();
        $query = $this->schedulesModel->where('sched_id', $scheduleID)->delete();

        if ($query) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'The schedule and its items were deleted successfully.'
            ]); // OK
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'An error occurred while deleting the schedule.'
        ]); // Internal Server Error
    }

    public function fetchComments()
    {
        if (!auth()->user()->can('dailyschedule.edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'You do not have permissions to access this page!'
            ]);
        }

        if ($this->request->isAJAX()) {
            $scheduleId = $this->request->getPost('schedule_id');

            $response = [
                'schedule_remarks' => '',
            ];

            // Get schedule-level remarks only
            $scheduleRemarks = $this->schedulesModel->getCommentsBySchedule($scheduleId);

            if (!empty($scheduleRemarks['remarks'])) {
                $response['schedule_remarks'] = esc($scheduleRemarks['remarks']);
            }

            return $this->response->setJSON($response);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status' => 'error',
            'message' => 'Invalid request type'
        ]);
    }

    /**
     * Generates a unique schedule ID using random digits and the current date.
     *
     * The ID format is: IDS + YYMMDD + random digits.
     * It checks for uniqueness using the `scheduleIdCheck` method of the schedulesModel.
     * If a duplicate is found, the function recursively tries again.
     *
     * @param int $length Number of random digits to generate.
     * @return string The generated unique schedule ID.
     */
    function generator($lenth)
    {
        $number = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 8);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }

        $generatedID = 'IDS' . date('ymd') . $con;

        $result = $this->schedulesModel->scheduleIdCheck($generatedID);

        if ($result === true) {
            $this->generator($lenth);
        } else {
            return $generatedID;
        }
    }

    /**
     * Generates a unique schedule ID based on the current date and daily auto-incrementing number.
     *
     * Format: IDS + YYMMDD + 3-digit sequence (e.g., IDS250714001)
     * - If it's the first ID of the day, starts with '001'.
     * - If there are existing IDs for today, increments the last sequence number.
     *
     * @return string The newly generated unique schedule ID.
     */
    public function generateUniqueID()
    {
        // Get today's date
        $today = date('Ymd');

        $lastId = $this->schedulesModel->getLastScheduleID();

        // Extract the date from the last inserted record
        $lastDate = $lastId ? date('Ymd', strtotime($lastId->created_at)) : null;

        // Initialize the lastIdValue to start the count for the day
        $lastIdValue = '001';

        // If it's the same day, increment the count
        if ($lastDate === $today) {
            // Extract the ID value and increment the count
            $lastIdValue = $lastId ? str_pad(substr($lastId->usched_id, -3) + 1, 3, '0', STR_PAD_LEFT) : '001';
        }

        // Construct the unique ID by combining fixed text, date, and auto-incremented value
        $uniqueId = 'IDS' . date('ymd') . $lastIdValue;

        return $uniqueId;
    }
}
