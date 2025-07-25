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

                $scheduleStatus = [
                    "published" => 0 // Default: Unpublished
                ];

                if (!empty($items) && areAllItemsPublished($items, 'published')) {
                    $scheduleStatus['published'] = 1;
                }

                $this->schedulesModel->update($scheduleID, $scheduleStatus, false);

                $status = 'success';
                $message = 'The schedule was added successfully!';
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
        if (!$this->request->isAjax()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method. AJAX request required.'
            ]);
        }

        $user = auth()->user();

        if (!$user->can('schedule.delete')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '<span class="my-3 d-block">You do not have permission to delete schedules.</span><small>Please contact the Administrator.</small>'
            ]);
        }

        $id = $this->request->getPost('id');
        $scheduleItem = $this->scheduleItemModel->find($id);

        if (!$scheduleItem) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Schedule item not found.'
            ]);
        }

        $scheduleDate = $scheduleItem['sched_date'];
        $cutoffTime = Time::today()->setTime(16, 30); // Today 4:30 PM
        $isPublished = $scheduleItem['published'] === '1';

        $isPrivileged = $user->inGroup('superadmin', 'admin');

        if (!$isPrivileged) {
            if ($isPublished) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => '<span class="my-3 d-block">The scheduled commercial has already been published, so deletion is not permitted.</span><small>Please contact the Administrator.</small>'
                ]);
            }

            $scheduledDate = Time::parse($scheduleDate);

            if ($scheduledDate->isBefore($cutoffTime)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => '<span class="my-3 d-block">Only future schedules (after 4.30 PM today) can be deleted.</span><small>Please contact the Administrator.</small>'
                ]);
            }
        }

        $query = $this->scheduleItemModel->delete($id);

        $items = $this->scheduleItemModel->getScheduleItems($scheduleItem['sched_id']);
        $allPublished = areAllItemsPublished($items, 'published');

        $this->schedulesModel->update($scheduleItem['sched_id'], ['published' => $allPublished ? 1 : 0], false);

        if ($query) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'The schedule item was deleted successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while deleting the schedule item.'
            ]);
        }
    }
}
