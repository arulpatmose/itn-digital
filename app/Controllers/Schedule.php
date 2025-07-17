<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommercialModel;
use App\Models\PlatformModel;
use App\Models\ProgramModel;
use App\Models\ScheduleItemModel;
use App\Models\ScheduleModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class Schedule extends BaseController
{
    protected $schedulesModel;
    protected $scheduleItemModel;
    protected $commercialModel;
    protected $platformModel;
    protected $programModel;

    public function __construct()
    {
        $this->schedulesModel = new ScheduleModel();
        $this->scheduleItemModel = new ScheduleItemModel();
        $this->commercialModel = new CommercialModel();
        $this->platformModel = new PlatformModel();
        $this->programModel = new ProgramModel();
    }

    public function index($id)
    {
        if (!auth()->user()->can('schedule.access')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $schedule = $this->schedulesModel->find($id);

        if (isset($schedule) && !empty($schedule) && !is_null($schedule)) {

            $data['schedule_items'] = $this->scheduleItemModel->getScheduleItems($id);

            $data['schedule'] = $schedule;
            $data['commercial'] = $this->commercialModel->find($schedule['commercial']);
            $data['platform'] = $this->platformModel->find($schedule['platform']);
            $data['program'] = $this->programModel->find($schedule['program']);

            $data['system_settings'] = get_settings('system_settings', true);

            $data['page_title'] = "Scheduled Items";
            $data['page_description'] = "Optimize ads. Maximize impact. Perfect timing, every time.";

            return view('backend/schedule/index', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function create($id)
    {
        if (!auth()->user()->can('schedule.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $schedule = $this->schedulesModel->find($id);

        if (isset($schedule) && !empty($schedule) && !is_null($schedule)) {

            $data['schedule'] = $schedule;

            $scheduleItems = $this->scheduleItemModel->getScheduleItems($id);

            // Prepare dates to disable in calendar
            $disabledDates = array();

            foreach ($scheduleItems as $item) {
                array_push($disabledDates, $item['sched_date']);
            }

            $data['schedule'] = $schedule;
            $data['program'] = $this->programModel->find($schedule['program']);

            $data['disabledDates'] = implode(', ', $disabledDates);

            $data['page_title'] = "Add Schedule";
            $data['page_description'] = "Optimize ads. Maximize impact. Perfect timing, every time.";

            return view('backend/schedule/add_schedule', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function store($id)
    {
        if (!auth()->user()->can('schedule.create')) {
            $status = 'error';
            $message = 'You are not allowed to view this page!';
            return redirect()->back()->with($status, $message);
        }

        $schedule = $this->schedulesModel->find($id);

        if (isset($schedule) && !empty($schedule) && !is_null($schedule)) {
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

                    $this->scheduleItemModel->insert($itemData);
                }
            }

            // Insert into database
            if ($this->scheduleItemModel->affectedRows() > 0) {
                // Check if all items have been published and update the Schedule as Published or Unpublished
                $items = $this->scheduleItemModel->getScheduleItems($scheduleID);

                $uniqueIDColumn = 'sched_id';
                $valueColumn = 'published';

                if ($this->areAllItemsPublished($items, $uniqueIDColumn, $valueColumn)) {
                    $scheduleStatus = [
                        "published" => 1
                    ];

                    $this->schedulesModel->update($scheduleID, $scheduleStatus, false);
                } else {
                    $scheduleStatus = [
                        "published" => 0
                    ];

                    $this->schedulesModel->update($scheduleID, $scheduleStatus, false);
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

    public function destroy()
    {
        // Ensure AJAX request
        if (!$this->request->isAjax()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method. AJAX request required.'
            ])->setStatusCode(400);
        }

        $user = auth()->user();

        // Check base permission to delete schedule items
        if (!$user->can('schedule.delete')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '<span class="my-3 d-block">You do not have permission to delete schedules.</span><small>Please contact the Administrator.</small>'
            ])->setStatusCode(403);
        }

        $id = $this->request->getPost('id');

        // Find the schedule item
        $scheduleItem = $this->scheduleItemModel->find($id);
        if (!$scheduleItem) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Schedule item not found.'
            ])->setStatusCode(404);
        }

        $scheduleDate = $scheduleItem['sched_date'];
        $today = Time::today();
        $isPublished = $scheduleItem['published'] === '1';

        // Check user role privilege: admin/superadmin can delete any schedule item
        $isPrivileged = $user->inGroup('superadmin', 'admin');

        // Restrict non-privileged users from deleting published or past schedule items
        if (!$isPrivileged) {
            if ($isPublished) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => '<span class="my-3 d-block">The scheduled commercial has already been published, so deletion is not permitted.</span><small>Please contact the Administrator.</small>'
                ])->setStatusCode(403);
            }

            if ($scheduleDate < $today) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => '<span class="my-3 d-block">Only schedules created today or later can be deleted.</span><small>Please contact the Administrator.</small>'
                ])->setStatusCode(403);
            }
        }

        // Proceed with deletion of the schedule item
        $query = $this->scheduleItemModel->delete($id);

        // Update schedule published status after deletion
        $items = $this->scheduleItemModel->getScheduleItems($scheduleItem['sched_id']);
        $allPublished = $this->areAllItemsPublished($items, 'sched_id', 'published');

        $scheduleStatus = ['published' => $allPublished ? 1 : 0];
        $this->schedulesModel->update($scheduleItem['sched_id'], $scheduleStatus, false);

        if ($query) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'The schedule item was deleted successfully.'
            ])->setStatusCode(200);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while deleting the schedule item.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Determines whether all schedule items for a given schedule ID are published.
     *
     * @param array $data The array of schedule items to check.
     * @param string $uniqueIDColumn The column name representing the unique schedule ID (e.g., 'sched_id').
     * @param string $valueColumn The column name indicating publication status (e.g., 'published').
     * 
     * @return bool Returns true if all items for each schedule ID have the same published value of 1;
     *              false if any item is unpublished (0) or if no items exist.
     */
    function areAllItemsPublished($data, $uniqueIDColumn, $valueColumn)
    {
        if (empty($data)) {
            return false; // No items, so not published
        }

        $uniqueIDs = [];

        foreach ($data as $row) {
            $uniqueID = $row[$uniqueIDColumn];
            $value = $row[$valueColumn];

            if (array_key_exists($uniqueID, $uniqueIDs)) {
                if ($uniqueIDs[$uniqueID] !== $value) {
                    return false;
                }
            } else {
                $uniqueIDs[$uniqueID] = $value;
            }
        }

        return true;
    }
}
