<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TimeSlotModel;

class TimeSlots extends BaseController
{
    protected $timeSlotModel;

    public function __construct()
    {
        $this->timeSlotModel = new TimeSlotModel();
    }

    public function index()
    {
        if (!auth()->user()->can('time_slot.access')) {
            return redirect()->back()->with('error', 'You do not have permission to access that page!');
        }

        $data = [
            'page_title'       => 'Time Slots',
            'page_description' => 'Manage available booking time slots.',
            'time_slots'       => $this->timeSlotModel->orderBy('start_time')->findAll(),
        ];

        return view('backend/time_slots/index', $data);
    }

    /**
     * Store a new time slot (AJAX).
     */
    public function store()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('time_slot.create')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $label     = trim($this->request->getPost('label') ?? '');
        $startTime = $this->request->getPost('start_time');
        $endTime   = $this->request->getPost('end_time');

        if ($label === '' || !$startTime || !$endTime) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Label, start time, and end time are required.']);
        }

        $id = $this->timeSlotModel->insert([
            'label'      => $label,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ], true);

        if ($id) {
            log_activity('time_slot.created', 'time_slot', $id, "Created time slot '{$label}'");
            $slot = $this->timeSlotModel->find($id);
            return $this->response->setJSON(['status' => 'success', 'message' => "Time slot '{$label}' added.", 'data' => $slot]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not add time slot.']);
    }

    /**
     * Update a time slot (AJAX).
     */
    public function update($id)
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('time_slot.edit')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $label     = trim($this->request->getPost('label') ?? '');
        $startTime = $this->request->getPost('start_time');
        $endTime   = $this->request->getPost('end_time');

        if ($label === '' || !$startTime || !$endTime) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Label, start time, and end time are required.']);
        }

        if ($this->timeSlotModel->update($id, ['label' => $label, 'start_time' => $startTime, 'end_time' => $endTime])) {
            log_activity('time_slot.updated', 'time_slot', (int) $id, "Updated time slot '{$label}'");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Time slot updated successfully.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not update time slot.']);
    }

    /**
     * Delete a time slot (AJAX).
     */
    public function destroy()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('time_slot.delete')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id   = (int) $this->request->getPost('id');
        $slot = $this->timeSlotModel->find($id);

        if (!$slot) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Time slot not found.']);
        }

        if ($this->timeSlotModel->delete($id)) {
            log_activity('time_slot.deleted', 'time_slot', $id, "Deleted time slot '{$slot['label']}'");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Time slot deleted.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not delete time slot.']);
    }
}
