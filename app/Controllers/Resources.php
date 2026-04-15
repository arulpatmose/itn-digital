<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ResourceModel;
use App\Models\ResourceTypeModel;

class Resources extends BaseController
{
    protected $resourceModel;
    protected $resourceTypeModel;

    public function __construct()
    {
        $this->resourceModel     = new ResourceModel();
        $this->resourceTypeModel = new ResourceTypeModel();
    }

    public function index()
    {
        if (!auth()->user()->can('resource.access')) {
            return redirect()->back()->with('error', 'You do not have permission to access that page!');
        }

        $data = [
            'page_title'       => 'Resources',
            'page_description' => 'Manage bookable resources such as studios, conference rooms, and equipment.',
            'resources'        => $this->resourceModel->getWithType(),
        ];

        return view('backend/resources/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('resource.create')) {
            return redirect()->back()->with('error', 'You do not have permission to create resources!');
        }

        $data = [
            'page_title'       => 'Add Resource',
            'page_description' => 'Add a new bookable resource.',
            'resource_types'   => $this->resourceTypeModel->findAll(),
        ];

        return view('backend/resources/add_resource', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('resource.create')) {
            return redirect()->back()->with('error', 'You do not have permission to create resources!');
        }

        $data = [
            'type_id'     => (int) $this->request->getPost('type_id'),
            'name'        => trim($this->request->getPost('name')),
            'description' => trim($this->request->getPost('description') ?? ''),
            'status'      => 1,
        ];

        if ($this->resourceModel->insert($data, false)) {
            $resourceId = $this->resourceModel->getInsertID();
            log_activity('resource.created', 'resource', $resourceId, "Created resource '{$data['name']}'");

            return redirect()->to('/resources')->with('success', "Resource '{$data['name']}' added successfully!");
        }

        return redirect()->back()->withInput()->with('error', 'There was an error adding the resource. Please try again.');
    }

    public function edit($id)
    {
        if (!auth()->user()->can('resource.edit')) {
            return redirect()->back()->with('error', 'You do not have permission to edit resources!');
        }

        $resource = $this->resourceModel->find($id);

        if (!$resource) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'page_title'       => "Edit Resource — {$resource['name']}",
            'page_description' => 'Update resource details.',
            'resource'         => $resource,
            'resource_types'   => $this->resourceTypeModel->findAll(),
        ];

        return view('backend/resources/edit_resource', $data);
    }

    public function update($id)
    {
        if (!auth()->user()->can('resource.edit')) {
            return redirect()->back()->with('error', 'You do not have permission to edit resources!');
        }

        $data = [
            'type_id'     => (int) $this->request->getPost('type_id'),
            'name'        => trim($this->request->getPost('name')),
            'description' => trim($this->request->getPost('description') ?? ''),
            'status'      => (int) ($this->request->getPost('status') ?? 1),
        ];

        if ($this->resourceModel->update($id, $data)) {
            log_activity('resource.updated', 'resource', (int) $id, "Updated resource '{$data['name']}'");

            return redirect()->to('/resources')->with('success', "Resource '{$data['name']}' updated successfully!");
        }

        return redirect()->back()->withInput()->with('error', 'There was an error updating the resource. Please try again.');
    }

    public function destroy()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request method.']);
        }

        if (!auth()->user()->can('resource.delete')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id       = (int) $this->request->getPost('id');
        $resource = $this->resourceModel->find($id);

        if (!$resource) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Resource not found.']);
        }

        if ($this->resourceModel->delete($id)) {
            log_activity('resource.deleted', 'resource', $id, "Deleted resource '{$resource['name']}'");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Resource deleted successfully.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not delete the resource.']);
    }

    /**
     * Toggle a resource's available/unavailable status (AJAX).
     */
    public function toggleStatus()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request method.']);
        }

        if (!auth()->user()->can('resource.edit')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id       = (int) $this->request->getPost('id');
        $resource = $this->resourceModel->find($id);

        if (!$resource) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Resource not found.']);
        }

        $newStatus = ((int) $resource['status'] === 1) ? 0 : 1;

        if ($this->resourceModel->update($id, ['status' => $newStatus])) {
            $statusLabel = $newStatus === 1 ? 'available' : 'unavailable';
            log_activity('resource.status_changed', 'resource', $id, "Changed resource '{$resource['name']}' status to {$statusLabel}");
            return $this->response->setJSON(['status' => 'success', 'new_status' => $newStatus, 'message' => "Resource is now {$statusLabel}."]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not update status.']);
    }
}
