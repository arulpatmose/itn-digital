<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SpotsModel;

class Spots extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('spots.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Ad Spots";
            $data['page_description'] = "Commercial spots which are seamlessly placed within the streaming content.";

            return view('backend/spots/index', $data);
        }
    }

    public function create()
    {
        if (!auth()->user()->can('spots.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Create an Ad Spot";
            $data['page_description'] = "Commercial spots which are seamlessly placed within the streaming content.";

            return view('backend/spots/add_spot', $data);
        }
    }

    public function store()
    {
        if (!auth()->user()->can('spots.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $spotsModel = new SpotsModel();

            // Prepare data to insert
            $data = [
                "name" => trim(ucwords($this->request->getVar('spot-name'))),
                "priority" => $this->request->getVar('spot-priority')
            ];

            // Insert into database
            if ($spotsModel->insert($data, false)) {
                $status = 'success';
                $message = 'The spot was addded successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while adding the spot!';
            }

            return redirect()->to('/spots')->with($status, $message);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('spots.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $spotsModel = new SpotsModel();

            $spot = $spotsModel->find($id);

            if (isset($spot) && !empty($spot)) {
                $data['spot'] = $spot;

                $itemName = $spot['name'];

                $data['page_title'] = "Edit Ad Spot - " . $itemName;
                $data['page_description'] = "Commercial spots which are seamlessly placed within the streaming content.";

                return view('backend/spots/edit_spot', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('spots.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $spotsModel = new SpotsModel();

            // Prepare data to update
            $data = [
                "name" => trim(ucwords($this->request->getVar('spot-name'))),
                "priority" => $this->request->getVar('spot-priority')
            ];

            // Insert into database
            if ($spotsModel->update($id, $data, false)) {
                $status = 'success';
                $message = 'The spot was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the spot!';
            }

            return redirect()->to('/spots')->with($status, $message);
        }
    }

    public function destroy()
    {
        if (!auth()->user()->can('spots.delete')) {
            $code = 0;
            $message = 'You do not have permissions to delele this spot!';

            echo json_encode([$code => 0, 'message' => $message]);
        } else {
            if ($this->request->isAjax()) {
                $spotsModel = new SpotsModel();
                $id = $this->request->getPost('id');

                $query = $spotsModel->delete($id);

                if ($query) {
                    echo json_encode(['code' => 1, 'message' => 'The spot was deleted successfully']);
                } else {
                    echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the spot']);
                }
            }
        }
    }
}
