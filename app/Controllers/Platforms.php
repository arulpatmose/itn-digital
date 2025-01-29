<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PlatformsModel;

class Platforms extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('platforms.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Platforms";
            $data['page_description'] = "Platforms like YouTube and Facebook, showcase ads to targeted audiences.";

            return view('backend/platforms/index', $data);
        }
    }

    public function create()
    {
        if (!auth()->user()->can('platforms.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Create a Platform";
            $data['page_description'] = "Platforms like YouTube and Facebook, showcase ads to targeted audiences.";

            return view('backend/platforms/add_platform', $data);
        }
    }

    public function store()
    {
        if (!auth()->user()->can('platforms.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $platformsModel = new PlatformsModel();

            // Prepare data to insert
            $data = [
                "name" => trim(ucwords($this->request->getVar('platform-name'))),
                "channel" => trim(ucwords($this->request->getVar('platform-channel')))
            ];

            // Insert into database
            if ($platformsModel->insert($data, false)) {
                $status = 'success';
                $message = 'The platform was addded successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while adding the platform!';
            }

            return redirect()->to('/platforms')->with($status, $message);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('platforms.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $platformsModel = new PlatformsModel();

            $platform = $platformsModel->find($id);

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
    }

    public function update($id)
    {
        if (!auth()->user()->can('platforms.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $platformsModel = new PlatformsModel();

            // Prepare data to update
            $data = [
                "name" => trim(ucwords($this->request->getVar('platform-name'))),
                "channel" => trim(ucwords($this->request->getVar('platform-channel')))
            ];

            // Insert into database
            if ($platformsModel->update($id, $data, false)) {
                $status = 'success';
                $message = 'The platform was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the platform!';
            }

            return redirect()->to('/platforms')->with($status, $message);
        }
    }

    public function destroy()
    {
        if (!auth()->user()->can('platforms.delete')) {
            $code = 0;
            $message = 'You do not have permissions to delele this platform!';

            echo json_encode([$code => 0, 'message' => $message]);
        } else {
            if ($this->request->isAjax()) {
                $platformsModel = new PlatformsModel();
                $id = $this->request->getPost('id');

                $query = $platformsModel->delete($id);

                if ($query) {
                    echo json_encode(['code' => 1, 'message' => 'The platform was deleted successfully']);
                } else {
                    echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the platform']);
                }
            }
        }
    }
}
