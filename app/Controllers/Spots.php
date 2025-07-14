<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SpotModel;
use CodeIgniter\HTTP\ResponseInterface;

class Spots extends BaseController
{
    protected $spotModel;

    public function __construct()
    {
        $this->spotModel = new SpotModel();
    }

    public function index()
    {
        if (!auth()->user()->can('spots.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Ad Spots";
        $data['page_description'] = "Commercial spots which are seamlessly placed within the streaming content.";

        return view('backend/spots/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('spots.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Create an Ad Spot";
        $data['page_description'] = "Commercial spots which are seamlessly placed within the streaming content.";

        return view('backend/spots/add_spot', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('spots.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Prepare data to insert
        $data = [
            "name" => trim(ucwords($this->request->getVar('spot-name'))),
            "priority" => $this->request->getVar('spot-priority')
        ];

        // Insert into database
        if ($this->spotModel->insert($data, false)) {
            $status = 'success';
            $message = 'The spot was addded successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while adding the spot!';
        }

        return redirect()->to('/spots')->with($status, $message);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('spots.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $spot = $this->spotModel->find($id);

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

    public function update($id)
    {
        if (!auth()->user()->can('spots.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Prepare data to update
        $data = [
            "name" => trim(ucwords($this->request->getVar('spot-name'))),
            "priority" => $this->request->getVar('spot-priority')
        ];

        // Insert into database
        if ($this->spotModel->update($id, $data, false)) {
            $status = 'success';
            $message = 'The spot was updated successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while updating the spot!';
        }

        return redirect()->to('/spots')->with($status, $message);
    }

    public function destroy()
    {
        // Ensure it's an AJAX request
        if (!$this->request->isAjax()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method. AJAX request required.'
            ])->setStatusCode(400); // Bad Request
        }

        if (!auth()->user()->can('spots.delete')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You do not have permissions to delete this spot!'
            ])->setStatusCode(403);
        }

        $id = $this->request->getPost('id');
        $query = $this->spotModel->delete($id);

        if ($query) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'The spot was deleted successfully'
            ])->setStatusCode(200);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while deleting the spot'
            ])->setStatusCode(500);
        }
    }
}
