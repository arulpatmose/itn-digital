<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PlatformModel;
use CodeIgniter\HTTP\ResponseInterface;

class Platforms extends BaseController
{
    protected $platformModel;

    public function __construct()
    {
        $this->platformModel = new PlatformModel();
    }

    public function index()
    {
        if (!auth()->user()->can('platforms.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Platforms";
        $data['page_description'] = "Platforms like YouTube and Facebook, showcase ads to targeted audiences.";

        return view('backend/platforms/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('platforms.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Create a Platform";
        $data['page_description'] = "Platforms like YouTube and Facebook, showcase ads to targeted audiences.";

        return view('backend/platforms/add_platform', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('platforms.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Prepare data to insert
        $data = [
            "name" => trim(ucwords($this->request->getVar('platform-name'))),
            "channel" => trim(ucwords($this->request->getVar('platform-channel')))
        ];

        // Insert into database
        if ($this->platformModel->insert($data, false)) {
            $status = 'success';
            $message = 'The platform was addded successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while adding the platform!';
        }

        return redirect()->to('/platforms')->with($status, $message);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('platforms.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $platform = $this->platformModel->find($id);

        if (isset($platform) && !empty($platform)) {
            $data['platform'] = $platform;
            $itemName = $platform['name'];

            $data['page_title'] = "Edit Platform - " . $itemName;
            $data['page_description'] = "Platforms like YouTube and Facebook, showcase ads to targeted audiences.";

            return view('backend/platforms/edit_platform', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('platforms.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Prepare data to update
        $data = [
            "name" => trim(ucwords($this->request->getVar('platform-name'))),
            "channel" => trim(ucwords($this->request->getVar('platform-channel')))
        ];

        // Insert into database
        if ($this->platformModel->update($id, $data, false)) {
            $status = 'success';
            $message = 'The platform was updated successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while updating the platform!';
        }

        return redirect()->to('/platforms')->with($status, $message);
    }

    public function destroy()
    {
        if (!auth()->user()->can('platforms.delete')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You do not have permissions to delete this platform!'
            ])->setStatusCode(403);
        }

        if ($this->request->isAjax()) {
            $id = $this->request->getPost('id');
            $query = $this->platformModel->delete($id);

            if ($query) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'The platform was deleted successfully'
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An error occurred while deleting the platform'
                ])->setStatusCode(500);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid request method. AJAX request required.'
        ])->setStatusCode(400);
    }
}
