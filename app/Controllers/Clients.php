<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientModel;

class Clients extends BaseController
{
    protected $clientModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
    }

    public function index()
    {
        if (!auth()->user()->can('clients.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data = [
            'page_title' => 'Clients',
            'page_description' => 'Advertisers or brands supply TV commercials for broadcasting.',
        ];

        return view('backend/clients/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('clients.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data = [
            'page_title' => 'Create a Client',
            'page_description' => 'Advertisers or brands supply TV commercials for broadcasting.',
        ];

        return view('backend/clients/add_client', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('clients.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Prepare data to insert
        $data = [
            "name" => formatCompanyName($this->request->getVar('client-name')),
            "address" => trim(ucwords($this->request->getVar('client-address')))
        ];

        // Insert into database
        if ($this->clientModel->insert($data, false)) {
            $status = 'success';
            $message = 'The client was addded successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while adding the client!';
        }

        return redirect()->to('/clients')->with($status, $message);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('clients.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $client = $this->clientModel->find($id);

        if (isset($client) && !empty($client)) {
            $itemName = $client['name'];

            $data = [
                'page_title' => "Edit Client - " . $itemName,
                'page_description' => "Advertisers or brands supply TV commercials for broadcasting.",
                'client' => $client
            ];

            return view('backend/clients/edit_client', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('clients.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Prepare data to update
        $data = [
            "name" => formatCompanyName($this->request->getVar('client-name')),
            "address" => trim(ucwords($this->request->getVar('client-address')))
        ];

        // Insert into database
        if ($this->clientModel->update($id, $data, false)) {
            $status = 'success';
            $message = 'The client was updated successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while updating the client!';
        }

        return redirect()->to('/clients')->with($status, $message);
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

        if (!auth()->user()->can('clients.delete')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You do not have permissions to delete this spot!'
            ])->setStatusCode(403);
        }

        $id = $this->request->getPost('id');
        $query = $this->clientModel->delete($id);

        if ($query) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'The client was deleted successfully'
            ])->setStatusCode(200);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while deleting the client'
            ])->setStatusCode(500);
        }
    }
}
