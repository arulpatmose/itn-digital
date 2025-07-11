<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommercialsModel;
use App\Models\PlatformsModel;
use App\Models\ProgramsModel;
use App\Models\ScheduleItemsModel;
use App\Models\SchedulesModel;
use App\Models\SpotsModel;
use CodeIgniter\I18n\Time;

class Schedule extends BaseController
{
    public function index($id)
    {
        if (!auth()->user()->can('schedule.access')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $schedulesModel = new SchedulesModel();

            $schedule = $schedulesModel->find($id);

            if (isset($schedule) && !empty($schedule) && !is_null($schedule)) {

                $scheduleItemsModel = new ScheduleItemsModel();
                $commercialsModel = new CommercialsModel();
                $platformsModel = new PlatformsModel();
                $programsModel = new ProgramsModel();

                $data['schedule_items'] = $scheduleItemsModel->getScheduleItems($id);

                $data['schedule'] = $schedule;
                $data['commercial'] = $commercialsModel->find($schedule['commercial']);
                $data['platform'] = $platformsModel->find($schedule['platform']);
                $data['program'] = $programsModel->find($schedule['program']);

                $router = service('router');
                $data['controller'] = class_basename($router->controllerName());
                $data['method'] = $router->methodName();

                $data['page_title'] = "Scheduled Items";
                $data['page_description'] = "Optimize ads. Maximize impact. Perfect timing, every time.";

                return view('backend/schedule/index', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function create($id)
    {
        if (!auth()->user()->can('schedule.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $schedulesModel = new SchedulesModel();

            $schedule = $schedulesModel->find($id);

            if (isset($schedule) && !empty($schedule) && !is_null($schedule)) {

                $data['schedule'] = $schedule;

                $scheduleItemsModel = new ScheduleItemsModel();
                $programsModel = new ProgramsModel();
                $scheduleItems = $scheduleItemsModel->getScheduleItems($id);

                // Prepare dates to disable in calendar
                $disabledDates = array();

                foreach ($scheduleItems as $item) {
                    array_push($disabledDates, $item['sched_date']);
                }

                $data['schedule'] = $schedule;
                $data['program'] = $programsModel->find($schedule['program']);

                $data['disabledDates'] = implode(', ', $disabledDates);

                $data['page_title'] = "Add Schedule";
                $data['page_description'] = "Optimize ads. Maximize impact. Perfect timing, every time.";

                return view('backend/schedule/add_schedule', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function store($id)
    {
        if (!auth()->user()->can('schedule.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        } else {
            $schedulesModel = new SchedulesModel();

            $schedule = $schedulesModel->find($id);

            if (isset($schedule) && !empty($schedule) && !is_null($schedule)) {
                $scheduleItemsModel = new ScheduleItemsModel();
                $userID = auth()->id();

                $scheduleSpot = $this->request->getVar('schedule-spot');
                $selectedDates = $this->request->getVar('schedule-dates');
                $scheduleRemarks = $this->request->getVar('schedule-remarks');

                $delemeter = ', ';

                if (strpos($selectedDates, $delemeter)) {
                    $dates = explode($delemeter, $selectedDates);
                } else {
                    $dates = array($selectedDates);
                }

                $scheduleID = $schedule['sched_id'];

                // Prepare data to insert by selected dates
                if (isset($dates)) {
                    foreach ($dates as $date) {
                        $itemData = [
                            "sched_id" => $scheduleID,
                            "sched_date" => $date,
                            "spot" => $scheduleSpot,
                            "remarks" => $scheduleRemarks,
                            "added_by" => $userID
                        ];

                        $scheduleItemsModel->insert($itemData);
                    }
                }

                // Insert into database
                if ($scheduleItemsModel->affectedRows() > 0) {
                    // Check if all items have been published and update the Schedule as Published or Unpublished
                    $items = $scheduleItemsModel->getScheduleItems($scheduleID);

                    $uniqueIDColumn = 'sched_id';
                    $valueColumn = 'published';

                    if ($this->areAllItemsPublished($items, $uniqueIDColumn, $valueColumn)) {
                        $scheduleStatus = [
                            "published" => 1
                        ];

                        $schedulesModel->update($scheduleID, $scheduleStatus, false);
                    } else {
                        $scheduleStatus = [
                            "published" => 0
                        ];

                        $schedulesModel->update($scheduleID, $scheduleStatus, false);
                    }

                    $status = 'success';
                    $message = 'The schedule was addded successfully!';
                } else {
                    $status = 'error';
                    $message = 'There was an error while adding the schedule!';
                }

                return redirect()->to('/schedule/' . $scheduleID)->with($status, $message);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function destroy()
    {
        if ($this->request->isAjax()) {
            $schedulesModel = new SchedulesModel();
            $scheduleItemsModel = new ScheduleItemsModel();
            $id = $this->request->getPost('id');
            $published = false;

            // Get Active Record by Schedule ID
            $scheduleItem = $scheduleItemsModel->find($id);

            // Get Scheule Date
            $scheduleDate = $scheduleItem['sched_date'];

            // Get the yesterdat.
            $today = Time::today();

            // Check if schedule item is already published
            if ($scheduleItem['published'] === '1') {
                $published = true;
            }

            $user = auth()->user();

            // Authorized personnels are allowed to delete even though it's published or old
            if (auth()->user()->can('schedule.delete')) {
                $query = $scheduleItemsModel->delete($id);

                // Check if all items have been published and update the Schedule as Published or Unpublished
                $items = $scheduleItemsModel->getScheduleItems($scheduleItem['sched_id']);

                $uniqueIDColumn = 'sched_id';
                $valueColumn = 'published';

                if ($this->areAllItemsPublished($items, $uniqueIDColumn, $valueColumn)) {
                    $scheduleStatus = [
                        "published" => 1
                    ];

                    $schedulesModel->update($scheduleItem['sched_id'], $scheduleStatus, false);
                } else {
                    $scheduleStatus = [
                        "published" => 0
                    ];

                    $schedulesModel->update($scheduleItem['sched_id'], $scheduleStatus, false);
                }

                if ($query) {
                    echo json_encode(['code' => 1, 'message' => 'The schedules were deleted successfully']);
                } else {
                    echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the schedules']);
                }
            } else {
                if ($published) {
                    // If an already published item found, return error
                    $message = '<span class="my-3 d-block">The scheduled commercial has been already published, and therefore, deletion is not permitted.</span><small>Please contact the Administrator.</small>';

                    echo json_encode(['code' => 0, 'message' => $message]);
                } elseif ($scheduleDate < $today) {
                    // Check if the date is older than today
                    $message = '<span class="my-3 d-block">Sorry, only schedules created today or later can be deleted.</span><small>Please contact the Administrator.</small>';

                    echo json_encode(['code' => 0, 'message' => $message]);
                } else {
                    // Detele if all conditions are met
                    $query = $scheduleItemsModel->delete($id);

                    // Check if all items have been published and update the Schedule as Published or Unpublished
                    $items = $scheduleItemsModel->getScheduleItems($scheduleItem['sched_id']);

                    $uniqueIDColumn = 'sched_id';
                    $valueColumn = 'published';

                    if ($this->areAllItemsPublished($items, $uniqueIDColumn, $valueColumn)) {
                        $scheduleStatus = [
                            "published" => 1
                        ];

                        $schedulesModel->update($scheduleItem['sched_id'], $scheduleStatus, false);
                    } else {
                        $scheduleStatus = [
                            "published" => 0
                        ];

                        $schedulesModel->update($scheduleItem['sched_id'], $scheduleStatus, false);
                    }

                    if ($query) {
                        echo json_encode(['code' => 1, 'message' => 'The schedules were deleted successfully']);
                    } else {
                        echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the schedules']);
                    }
                }
            }
        }
    }

    /**
     * Check if all rows with the same unique ID have the same value in a specific column.
     *
     * @param array $data The data to check.
     * @param string $uniqueIDColumn The column name for unique IDs.
     * @param string $valueColumn The column name for the value to compare.
     * @return bool True if all rows with the same unique ID have the same value, false otherwise.
     */
    function areAllItemsPublished($data, $uniqueIDColumn, $valueColumn)
    {
        $uniqueIDs = array(); // To store unique IDs and their associated values

        // Iterate through the data and populate the $uniqueIDs array
        foreach ($data as $row) {
            $uniqueID = $row[$uniqueIDColumn];
            $value = $row[$valueColumn];

            if (array_key_exists($uniqueID, $uniqueIDs)) {
                if ($uniqueIDs[$uniqueID] !== $value) {
                    return false; // Found a different value for the same unique ID
                }
            } else {
                $uniqueIDs[$uniqueID] = $value;
            }
        }

        return true; // All rows with the same unique ID have the same value
    }
}
