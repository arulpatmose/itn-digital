<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PlatformModel;
use App\Models\ScheduleItemModel;
use App\Models\ScheduleModel;
use CodeIgniter\HTTP\ResponseInterface;

class DailySchedule extends BaseController
{
    protected $platformModel;
    protected $scheduleModel;
    protected $scheduleItemModel;

    public function __construct()
    {
        $this->platformModel = new PlatformModel();
        $this->scheduleModel = new ScheduleModel();
        $this->scheduleItemModel = new ScheduleItemModel();
    }

    public function index($date = null)
    {
        if (!auth()->user()->can('dailyschedule.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $filterData = [];

        $request = service('request');
        $platform = $request->getGet('platform');

        if ($platform !== null && is_numeric($platform) && $platform > 0) {
            $filterData['platform'] = $platform;
            $selected_platform = $platform;
        } else {
            $filterData['platform'] = null;
            $selected_platform = null;
        }

        if (isset($date) && !is_null($date) && validateDate($date, 'Y-m-d')) {
            $filterData['date'] = $date;
        } else {
            $filterData['date'] = date('Y-m-d');
        }

        $pageData = [
            'selected_platform'  => $selected_platform,
            'date'              => $filterData['date'],
            'schedule_date'     => date("l jS \of F Y", strtotime($filterData['date'])),
            'schedules'         => $this->scheduleModel->getDailySchedule($filterData),
            'platforms'         => $this->platformModel->select('pfm_id as id, name, channel')->findAll(),
            'page_title'        => "Daily Commercial Schedule",
            'page_description'  => "Timed placements for diverse viewer engagement."
        ];

        $data = array_merge($data ?? [], $pageData);

        return view('backend/daily-schedule/index', $data);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('dailyschedule.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access this page!';

            return $this->response->setStatusCode(400)
                ->setJSON([
                    'status' => $status,
                    'message' => $message
                ]);
        }

        if ($this->request->isAjax()) {
            if (isset($id) && !is_null($id)) {
                $schedule = $this->scheduleItemModel->find($id);

                $data = [
                    "link" => $schedule['link'],
                    "remarks" => $schedule['remarks']
                ];

                if (isset($data)) {
                    return $this->response->setJSON($data);
                }
            }
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('dailyschedule.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access this page!';

            return $this->response->setStatusCode(400)
                ->setJSON([
                    'status' => $status,
                    'message' => $message
                ]);
        }

        if ($this->request->isAjax()) {
            $currentItem = $this->scheduleItemModel->find($id);

            $link = trim($this->request->getVar('schedule-link'));
            $remarks = trim($this->request->getVar('schedule-remarks'));
            $published = !empty($link) ? 1 : 0;
            $updatedBy = !empty($link) ? auth()->id() : NULL;

            // Prepare data to update
            $data = [
                "link" => $link,
                "remarks" => $remarks,
                "published" => $published,
                "updated_by" => $updatedBy
            ];

            // Insert into database
            if ($this->scheduleItemModel->update($id, $data, false)) {
                $data = array(
                    'status' => 'success'
                );

                // Check if all items have been published and update the Schedule as Published or Unpublished
                $items = $this->scheduleItemModel->getScheduleItems($currentItem['sched_id']);

                $uniqueIDColumn = 'sched_id';
                $valueColumn = 'published';

                if (areAllItemsPublished($items, $uniqueIDColumn, $valueColumn)) {
                    $scheduleStatus = [
                        "published" => 1
                    ];

                    $this->scheduleModel->update($currentItem['sched_id'], $scheduleStatus, false);
                } else {
                    $scheduleStatus = [
                        "published" => 0
                    ];

                    $this->scheduleModel->update($currentItem['sched_id'], $scheduleStatus, false);
                }
            } else {
                $data = array(
                    'status' => 'error'
                );
            }

            return $this->response->setJSON($data);
        }
    }

    public function updateBulk()
    {
        if (!auth()->user()->can('dailyschedule.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access this page!';

            return $this->response->setStatusCode(400)
                ->setJSON([
                    'status' => $status,
                    'message' => $message
                ]);
        }

        if ($this->request->isAjax()) {
            $ids = $this->request->getPost('ids'); // Get array of IDs from POST request
            $link = trim($this->request->getVar('schedule-link'));
            $remarks = trim($this->request->getVar('schedule-remarks'));
            $published = !empty($link) ? 1 : 0;
            $updatedBy = !empty($link) ? auth()->id() : 0;

            // Prepare data to update
            $data = [
                "link" => $link,
                "remarks" => $remarks,
                "published" => $published,
                "updated_by" => $updatedBy
            ];

            $updateSuccess = true;
            $scheduleIds = [];

            foreach ($ids as $id) {
                if ($this->scheduleItemModel->update($id, $data, false)) {
                    $currentItem = $this->scheduleItemModel->find($id);
                    $schedId = $currentItem['sched_id'];
                    $scheduleIds[$schedId] = true; // Keep track of successfully updated schedule IDs
                } else {
                    $updateSuccess = false;
                    break;
                }
            }

            if ($updateSuccess) {
                foreach (array_keys($scheduleIds) as $schedId) {
                    // Check if all items have been published and update the Schedule as Published or Unpublished
                    $items = $this->scheduleItemModel->getScheduleItems($schedId);

                    $uniqueIDColumn = 'sched_id';
                    $valueColumn = 'published';

                    if (areAllItemsPublished($items, $uniqueIDColumn, $valueColumn)) {
                        $scheduleStatus = ["published" => 1];
                    } else {
                        $scheduleStatus = ["published" => 0];
                    }

                    $this->scheduleModel->update($schedId, $scheduleStatus, false);
                }
                $response = ['status' => 'success'];
            } else {
                $response = ['status' => 'error'];
            }

            return $this->response->setJSON($response);
        }
    }

    public function fetchComments()
    {
        if (!auth()->user()->can('dailyschedule.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access this page!';

            return $this->response->setStatusCode(400)
                ->setJSON([
                    'status' => $status,
                    'message' => $message
                ]);
        }

        // Check if the request is an AJAX request
        if ($this->request->isAJAX()) {
            $scheduleId = $this->request->getPost('schedule_id');
            $itemId     = $this->request->getPost('item_id');

            $response = [
                'schedule_remarks' => '',
                'item_comments'    => ''
            ];

            // Get schedule-level remarks
            $scheduleRemarks = $this->scheduleModel->getScheduleRemarks($scheduleId);
            if (!empty($scheduleRemarks['remarks'])) {
                $response['schedule_remarks'] = esc($scheduleRemarks['remarks']);
            }

            // Get item-level comments
            $itemComment = $this->scheduleItemModel->getCommentsByScheduleItem($itemId);
            if (!empty($itemComment['remarks'])) {
                $response['item_comments'] = esc($itemComment['remarks']);
            }

            return $this->response->setJSON($response);
        }
    }
}
