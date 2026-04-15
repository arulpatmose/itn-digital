<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingPurposeGroupModel;
use App\Models\BookingPurposeModel;

class BookingPurposeGroups extends BaseController
{
    protected $groupModel;
    protected $purposeModel;

    public function __construct()
    {
        $this->groupModel   = new BookingPurposeGroupModel();
        $this->purposeModel = new BookingPurposeModel();
    }

    public function index()
    {
        if (!auth()->user()->can('booking_purpose_group.access')) {
            return redirect()->back()->with('error', 'You do not have permission to access that page!');
        }

        $data = [
            'page_title'       => 'Booking Purpose Groups',
            'page_description' => 'Manage the groups used to organise booking purposes.',
            'purpose_groups'   => $this->groupModel
                ->orderBy('sort_order', 'ASC')
                ->orderBy('name', 'ASC')
                ->findAll(),
        ];

        return view('backend/booking_purpose_groups/index', $data);
    }

    public function store()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking_purpose_group.create')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $name        = trim($this->request->getPost('name') ?? '');
        $description = trim($this->request->getPost('description') ?? '');
        $sortOrder   = (int) ($this->request->getPost('sort_order') ?? 0);
        $isActive    = (int) ($this->request->getPost('is_active') ?? 1);

        if ($name === '') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Group name is required.']);
        }

        $slug = $this->generateSlug($name);

        $id = $this->groupModel->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description ?: null,
            'sort_order'  => $sortOrder,
            'is_active'   => $isActive === 1 ? 1 : 0,
        ], true);

        if ($id) {
            log_activity('booking_purpose_group.created', 'booking_purpose_group', $id, "Created booking purpose group '{$name}'");
            $group = $this->groupModel->find((int) $id);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Purpose group '{$name}' added.",
                'data'    => $group,
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not add purpose group.']);
    }

    public function update($id)
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking_purpose_group.edit')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $group = $this->groupModel->find((int) $id);

        if (!$group) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Purpose group not found.']);
        }

        $name        = trim($this->request->getPost('name') ?? '');
        $description = trim($this->request->getPost('description') ?? '');
        $sortOrder   = (int) ($this->request->getPost('sort_order') ?? 0);
        $isActive    = (int) ($this->request->getPost('is_active') ?? 1);

        if ($name === '') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Group name is required.']);
        }

        $slug = $this->generateSlug($name, (int) $id);

        if ($this->groupModel->update((int) $id, [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description ?: null,
            'sort_order'  => $sortOrder,
            'is_active'   => $isActive === 1 ? 1 : 0,
        ])) {
            log_activity('booking_purpose_group.updated', 'booking_purpose_group', (int) $id, "Updated booking purpose group '{$name}'");
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Purpose group updated successfully.',
                'data'    => array_merge($group, [
                    'name'        => $name,
                    'slug'        => $slug,
                    'description' => $description ?: null,
                    'sort_order'  => $sortOrder,
                    'is_active'   => $isActive === 1 ? 1 : 0,
                ]),
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not update purpose group.']);
    }

    public function destroy()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking_purpose_group.delete')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id    = (int) $this->request->getPost('id');
        $group = $this->groupModel->find($id);

        if (!$group) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Purpose group not found.']);
        }

        $purposeCount = $this->purposeModel->where('group_id', $id)->countAllResults();

        if ($purposeCount > 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => "Cannot delete '{$group['name']}' — it has {$purposeCount} purpose(s) assigned. Reassign or delete them first.",
            ]);
        }

        if ($this->groupModel->delete($id)) {
            log_activity('booking_purpose_group.deleted', 'booking_purpose_group', $id, "Deleted booking purpose group '{$group['name']}'");
            return $this->response->setJSON(['status' => 'success', 'message' => "Purpose group '{$group['name']}' deleted."]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not delete purpose group.']);
    }

    public function toggleStatus()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking_purpose_group.edit')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id    = (int) $this->request->getPost('id');
        $group = $this->groupModel->find($id);

        if (!$group) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Purpose group not found.']);
        }

        $newStatus = (int) $group['is_active'] === 1 ? 0 : 1;

        if ($this->groupModel->update($id, ['is_active' => $newStatus])) {
            $statusLabel = $newStatus === 1 ? 'active' : 'inactive';
            log_activity('booking_purpose_group.status_changed', 'booking_purpose_group', $id, "Changed purpose group '{$group['name']}' status to {$statusLabel}");

            return $this->response->setJSON([
                'status'     => 'success',
                'new_status' => $newStatus,
                'message'    => "Purpose group is now {$statusLabel}.",
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not update purpose group status.']);
    }

    // -------------------------------------------------------------------------

    private function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug     = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        $original = $slug;
        $counter  = 1;

        while (true) {
            $query = $this->groupModel->where('slug', $slug);
            if ($excludeId !== null) {
                $query->where('id !=', $excludeId);
            }
            if ($query->countAllResults() === 0) {
                break;
            }
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}
