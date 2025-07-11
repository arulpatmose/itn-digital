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

        $data['page_title'] = "Clients";
        $data['page_description'] = "Advertisers or brands supply TV commercials for broadcasting.";

        return view('backend/clients/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('clients.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Create a Client";
        $data['page_description'] = "Advertisers or brands supply TV commercials for broadcasting.";

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
            $data['client'] = $client;

            $itemName = $client['name'];

            $data['page_title'] = "Edit Client - " . $itemName;
            $data['page_description'] = "Advertisers or brands supply TV commercials for broadcasting.";

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
        if (!auth()->user()->can('clients.delete')) {
            $status = 'error';
            $message = 'You do not have permissions to delete this client!';
            return $this->response->setJSON(['status' => $status, 'message' => $message])->setStatusCode(403);
        }

        if ($this->request->isAjax()) {
            $id = $this->request->getPost('id');
            $query = $this->clientModel->delete($id);

            if ($query) {
                $status = 'success';
                $message = 'The client was deleted successfully';
                return $this->response->setJSON(['status' => $status, 'message' => $message])->setStatusCode(200);
            } else {
                $status = 'error';
                $message = 'An error occurred while deleting the client';
                return $this->response->setJSON(['status' => $status, 'message' => $message])->setStatusCode(500);
            }
        }
    }
}
