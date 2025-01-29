<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommercialsModel;
use App\Models\FormatsModel;
use App\Models\PlatformsModel;
use App\Models\ProgramsModel;
use App\Models\ScheduleItemsModel;
use App\Models\SchedulesModel;
use App\Models\SpotsModel;
use CodeIgniter\I18n\Time;

class Schedules extends BaseController
{
    public function __construct()
    {
        helper('format_name'); // Load the format_name helper
    }

    public function index()
    {
        if (!auth()->user()->can('schedules.access')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $platformsModel = new PlatformsModel();
            $formatsModel = new FormatsModel();

            $router = service('router');
            $data['controller'] = class_basename($router->controllerName());
            $data['method'] = $router->methodName();

            $data['platforms'] = $platformsModel->select('pfm_id as id, name, channel')->findAll();
            $data['formats'] = $formatsModel->select('format_id as id, name')->findAll();

            $data['page_title'] = "Schedules";
            $data['page_description'] = "Optimize ads. Maximize impact. Perfect timing, every time.";

            return view('backend/schedules/index', $data);
        }
    }

    public function create()
    {
        if (!auth()->user()->can('schedules.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $spotsModel = new SpotsModel();
            $platformsModel = new PlatformsModel();

            $data['spots'] = $spotsModel->select('spot_id as id, name')->findAll();
            $data['platforms'] = $platformsModel->select('pfm_id as id, name, channel')->findAll();

            $data['page_title'] = "Create Schedule";
            $data['page_description'] = "Optimize ads. Maximize impact. Perfect timing, every time.";

            return view('backend/schedules/add_schedule', $data);
        }
    }

    public function store()
    {
        if (!auth()->user()->can('schedules.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $scheduleslModel = new SchedulesModel();
            $scheduleItemsModel = new ScheduleItemsModel();
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

            $scheduleslModel->insert($scheduleData);

            $lastInsertID = $scheduleslModel->insertID();

            // Prepare data to insert by selected dates

            if (isset($dates)) {
                foreach ($dates as $date) {
                    $itemData = [
                        "sched_id" => $lastInsertID,
                        "sched_date" => $date,
                        "spot" => $spotID,
                        "added_by" => $userID
                    ];

                    $scheduleItemsModel->insert($itemData);
                }
            }

            // Insert into database
            if ($scheduleslModel->affectedRows() > 0 && $scheduleItemsModel->affectedRows() > 0) {
                $status = 'success';
                $message = 'The schedule was addded successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while adding the schedule!';
            }

            return redirect()->to('/schedules')->with($status, $message);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('schedules.edit')) {
            $status = 'error';
            $message = '<span class="my-3 d-block">You are not allowed to do this operation!</span><small>Please contact the Administrator.</small>';
            return redirect()->to('/schedules')->with($status, $message);
        } else {
            $schedulesModel = new SchedulesModel();

            $schedule = $schedulesModel->find($id);

            if (isset($schedule) && !empty($schedule)) {
                $spotsModel = new SpotsModel();
                $platformsMode = new PlatformsModel();
                $commercialModel = new CommercialsModel();
                $programsModel = new ProgramsModel();

                $data['schedule'] = $schedule;

                $data['platforms'] = $platformsMode->select('pfm_id as id, name, channel')->findAll();

                $data['commercial'] = $commercialModel->find($data['schedule']['commercial']);
                $data['program'] = $programsModel->find($data['schedule']['program']);

                $itemName = $data['commercial']['name'];

                $data['page_title'] = "Edit Schedule for " . $itemName;
                $data['page_description'] = "Optimize ads. Maximize impact. Perfect timing, every time.";

                return view('backend/schedules/edit_schedule', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('schedules.edit')) {
            $status = 'error';
            $message = '<span class="my-3 d-block">You are not allowed to do this operation!</span><small>Please contact the Administrator.</small>';
            return redirect()->to('/schedules')->with($status, $message);
        } else {
            $schedulesModel = new SchedulesModel();

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
            if ($schedulesModel->update($id, $data, false)) {
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
        if ($this->request->isAjax()) {
            $schedulesModel = new SchedulesModel();
            $scheduleItemsModel = new ScheduleItemsModel();
            $id = $this->request->getPost('id');
            $activeRecord = false;

            // Get Active Record by Schedule ID
            $schedule = $schedulesModel->find($id);

            // Get Unique Scheule ID
            $scheduleID = $schedule['sched_id'];
            $scheduleDate = $schedule['created_at'];

            // Get the yesterdat.
            $today = Time::today();

            // Check if schedule contatins already published commercial
            $allSchedules = $scheduleItemsModel->where('sched_id', $scheduleID)->findAll();

            foreach ($allSchedules as $item) {
                if ($item['published'] === '1') {
                    $activeRecord = true;
                    break;
                }
            }

            $user = auth()->user();

            // Authorized personnels are allowed to delete even though it's published or old
            if (auth()->user()->can('schedules.delete')) {
                $scheduleItemsModel->where('sched_id', $scheduleID)->delete();
                $query = $schedulesModel->where('sched_id', $scheduleID)->delete();

                if ($query) {
                    echo json_encode(['code' => 1, 'message' => 'The schedules were deleted successfully']);
                } else {
                    echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the schedules']);
                }
            } else {
                if ($activeRecord) {
                    // If an already published item found, return error
                    $message = '<span class="my-3 d-block">This schedule includes a commercial that has already been published, and therefore, deletion is not permitted.</span><small>Please contact the Administrator.</small>';

                    echo json_encode(['code' => 0, 'message' => $message]);
                } elseif ($scheduleDate < $today) {
                    // Check if the date is older than today
                    $message = '<span class="my-3 d-block">Sorry, only schedules created today or later can be deleted.</span><small>Please contact the Administrator.</small>';

                    echo json_encode(['code' => 0, 'message' => $message]);
                } else {
                    // Detele if all conditions are met
                    $scheduleItemsModel->where('sched_id', $scheduleID)->delete();
                    $query = $schedulesModel->where('sched_id', $scheduleID)->delete();

                    if ($query) {
                        echo json_encode(['code' => 1, 'message' => 'The schedules were deleted successfully']);
                    } else {
                        echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the schedules']);
                    }
                }
            }
        }
    }

    // Generator function is used to Generate Key

    function generator($lenth)
    {
        $scheduelsModel = new SchedulesModel();

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

        $result = $scheduelsModel->scheduleIdCheck($generatedID);

        if ($result === true) {
            $this->generator($lenth);
        } else {
            return $generatedID;
        }
    }

    // Function to generate unique ID based on date and auto-incremented ID
    public function generateUniqueID()
    {
        // Get today's date
        $today = date('Ymd');

        $scheduelsModel = new SchedulesModel();

        $lastId = $scheduelsModel->getLastScheduleID();

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
