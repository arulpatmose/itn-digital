<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingPurposeModel;
use App\Models\BookingPurposeGroupModel;

class BookingPurposes extends BaseController
{
    protected $bookingPurposeModel;
    protected $bookingPurposeGroupModel;

    public function __construct()
    {
        $this->bookingPurposeModel = new BookingPurposeModel();
        $this->bookingPurposeGroupModel = new BookingPurposeGroupModel();
    }

    public function index()
    {
        if (!auth()->user()->can('booking_purpose.access')) {
            return redirect()->back()->with('error', 'You do not have permission to access that page!');
        }

        $data = [
            'page_title'       => 'Booking Purposes',
            'page_description' => 'Manage the allowed purposes for booking requests.',
            'booking_purposes' => $this->bookingPurposeModel->getWithGroup(),
            'purpose_groups'   => $this->bookingPurposeGroupModel
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('name', 'ASC')
                ->findAll(),
        ];

        return view('backend/booking_purposes/index', $data);
    }

    public function store()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking_purpose.create')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $groupId = (int) ($this->request->getPost('group_id') ?? 0);
        $name = trim($this->request->getPost('name') ?? '');
        $description = trim($this->request->getPost('description') ?? '');
        $isActive = (int) ($this->request->getPost('is_active') ?? 1);

        if ($groupId <= 0 || $name === '') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Group and name are required.']);
        }

        if (! $this->bookingPurposeGroupModel->find($groupId)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Selected purpose group is invalid.']);
        }

        $id = $this->bookingPurposeModel->insert([
            'group_id'    => $groupId,
            'name'        => $name,
            'description' => $description ?: null,
            'is_active'   => $isActive === 1 ? 1 : 0,
        ], true);

        if ($id) {
            log_activity('booking_purpose.created', 'booking_purpose', $id, "Created booking purpose '{$name}'");
            $purpose = $this->bookingPurposeModel->getWithGroup((int) $id);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Booking purpose '{$name}' added.",
                'data'    => $purpose,
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not add booking purpose.']);
    }

    public function update($id)
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking_purpose.edit')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $groupId = (int) ($this->request->getPost('group_id') ?? 0);
        $name = trim($this->request->getPost('name') ?? '');
        $description = trim($this->request->getPost('description') ?? '');
        $isActive = (int) ($this->request->getPost('is_active') ?? 1);

        if ($groupId <= 0 || $name === '') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Group and name are required.']);
        }

        if (! $this->bookingPurposeGroupModel->find($groupId)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Selected purpose group is invalid.']);
        }

        if ($this->bookingPurposeModel->update($id, [
            'group_id'    => $groupId,
            'name'        => $name,
            'description' => $description ?: null,
            'is_active'   => $isActive === 1 ? 1 : 0,
        ])) {
            log_activity('booking_purpose.updated', 'booking_purpose', (int) $id, "Updated booking purpose '{$name}'");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Booking purpose updated successfully.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not update booking purpose.']);
    }

    public function destroy()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking_purpose.delete')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id = (int) $this->request->getPost('id');
        $purpose = $this->bookingPurposeModel->find($id);

        if (!$purpose) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Booking purpose not found.']);
        }

        if ($this->bookingPurposeModel->delete($id)) {
            log_activity('booking_purpose.deleted', 'booking_purpose', $id, "Deleted booking purpose '{$purpose['name']}'");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Booking purpose deleted.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not delete booking purpose.']);
    }

    public function toggleStatus()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking_purpose.edit')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id = (int) $this->request->getPost('id');
        $purpose = $this->bookingPurposeModel->find($id);

        if (!$purpose) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Booking purpose not found.']);
        }

        $newStatus = (int) $purpose['is_active'] === 1 ? 0 : 1;

        if ($this->bookingPurposeModel->update($id, ['is_active' => $newStatus])) {
            $statusLabel = $newStatus === 1 ? 'active' : 'inactive';
            log_activity('booking_purpose.status_changed', 'booking_purpose', $id, "Changed booking purpose '{$purpose['name']}' status to {$statusLabel}");

            return $this->response->setJSON([
                'status'     => 'success',
                'new_status' => $newStatus,
                'message'    => "Booking purpose is now {$statusLabel}.",
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not update booking purpose status.']);
    }
}
