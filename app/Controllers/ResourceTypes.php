<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ResourceTypeModel;

class ResourceTypes extends BaseController
{
    protected $resourceTypeModel;

    public function __construct()
    {
        $this->resourceTypeModel = new ResourceTypeModel();
    }

    public function index()
    {
        if (!auth()->user()->can('resourcetype.access')) {
            return redirect()->back()->with('error', 'You do not have permission to access that page!');
        }

        $data = [
            'page_title'       => 'Resource Types',
            'page_description' => 'Manage categories for bookable resources.',
            'resource_types'   => $this->resourceTypeModel->findAll(),
        ];

        return view('backend/resource_types/index', $data);
    }

    /**
     * Store a new resource type (AJAX).
     */
    public function store()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('resourcetype.create')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $name        = trim($this->request->getPost('name') ?? '');
        $description = trim($this->request->getPost('description') ?? '');

        if ($name === '') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Name is required.']);
        }

        $id = $this->resourceTypeModel->insert(['name' => $name, 'description' => $description ?: null], true);

        if ($id) {
            log_activity('resourcetype.created', 'resource_type', $id, "Created resource type '{$name}'");
            $type = $this->resourceTypeModel->find($id);
            return $this->response->setJSON(['status' => 'success', 'message' => "Resource type '{$name}' added.", 'data' => $type]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not add resource type.']);
    }

    /**
     * Update a resource type (AJAX).
     */
    public function update($id)
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('resourcetype.edit')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $name        = trim($this->request->getPost('name') ?? '');
        $description = trim($this->request->getPost('description') ?? '');

        if ($name === '') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Name is required.']);
        }

        if ($this->resourceTypeModel->update($id, ['name' => $name, 'description' => $description ?: null])) {
            log_activity('resourcetype.updated', 'resource_type', (int) $id, "Updated resource type '{$name}'");
            return $this->response->setJSON(['status' => 'success', 'message' => "Resource type updated successfully."]);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not update resource type.']);
    }

    /**
     * Delete a resource type (AJAX).
     */
    public function destroy()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('resourcetype.delete')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id   = (int) $this->request->getPost('id');
        $type = $this->resourceTypeModel->find($id);

        if (!$type) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Resource type not found.']);
        }

        if ($this->resourceTypeModel->delete($id)) {
            log_activity('resourcetype.deleted', 'resource_type', $id, "Deleted resource type '{$type['name']}'");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Resource type deleted.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not delete resource type.']);
    }
}
