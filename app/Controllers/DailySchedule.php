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

        // Prepare Data for Filter
        $filterData = array();

        // Access the request service
        $request = service('request');

        // Retrieve a URL parameter named 'platform'
        $platform = $request->getGet('platform');

        // Check if the 'platform' parameter exists and is valid
        if ($platform !== null && is_numeric($platform) && $platform > 0) {
            $filterData['platform'] = $platform;
            $data['selectedPlatform'] = $platform;
        } else {
            $filterData['platform'] = NULL;
            $data['selectedPlatform'] = NULL;
        }

        $router = service('router');
        $data['controller'] = class_basename($router->controllerName());
        $data['method'] = $router->methodName();

        if (isset($date) && !is_null($date) && $this->validateDate($date, 'Y-m-d')) {
            $filterData['date'] = $date;
        } else {
            $filterData['date'] = date('Y-m-d');
        }

        $data['date'] = $filterData['date'];
        $data['schedule_date'] = date("l jS \of F Y", strtotime($filterData['date']));

        $data['schedules'] = $this->scheduleModel->getDailySchedule($filterData);
        $data['platforms'] = $this->platformModel->select('pfm_id as id, name, channel')->findAll();

        $data['page_title'] = "Daily Commercial Schedule";
        $data['page_description'] = "Timed placements for diverse viewer engagement.";

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
            $updatedBy = !empty($link) ? auth()->id() : 0;

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

                if ($this->areAllItemsPublished($items, $uniqueIDColumn, $valueColumn)) {
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

                    if ($this->areAllItemsPublished($items, $uniqueIDColumn, $valueColumn)) {
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

    /**
     * Validate a date string against a specific format.
     *
     * @param string $date The date string to validate.
     * @param string $format The format to validate against (default is 'Y-m-d').
     * @return bool True if the date is valid, false otherwise.
     */
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
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
