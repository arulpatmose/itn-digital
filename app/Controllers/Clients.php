<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientsModel;

class Clients extends BaseController
{
    public function __construct()
    {
        helper('format_company_name'); // Load the format_company_name helper
    }

    public function index()
    {
        if (!auth()->user()->can('clients.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Clients";
            $data['page_description'] = "Advertisers or brands supply TV commercials for broadcasting.";

            return view('backend/clients/index', $data);
        }
    }

    public function create()
    {
        if (!auth()->user()->can('clients.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Create a Client";
            $data['page_description'] = "Advertisers or brands supply TV commercials for broadcasting.";

            return view('backend/clients/add_client', $data);
        }
    }

    public function store()
    {
        if (!auth()->user()->can('clients.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $clientsModel = new ClientsModel();

            // Prepare data to insert
            $data = [
                "name" => formatCompanyName($this->request->getVar('client-name')),
                "address" => trim(ucwords($this->request->getVar('client-address')))
            ];

            // Insert into database
            if ($clientsModel->insert($data, false)) {
                $status = 'success';
                $message = 'The client was addded successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while adding the client!';
            }

            return redirect()->to('/clients')->with($status, $message);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('clients.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $clientsModel = new ClientsModel();

            $client = $clientsModel->find($id);

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
    }

    public function update($id)
    {
        if (!auth()->user()->can('clients.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $clientsModel = new ClientsModel();

            // Prepare data to update
            $data = [
                "name" => formatCompanyName($this->request->getVar('client-name')),
                "address" => trim(ucwords($this->request->getVar('client-address')))
            ];

            // Insert into database
            if ($clientsModel->update($id, $data, false)) {
                $status = 'success';
                $message = 'The client was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the client!';
            }

            return redirect()->to('/clients')->with($status, $message);
        }
    }

    public function destroy()
    {
        if (!auth()->user()->can('clients.delete')) {
            $code = 0;
            $message = 'You do not have permissions to delele this client!';

            echo json_encode([$code => 0, 'message' => $message]);
        } else {
            if ($this->request->isAjax()) {
                $clientsModel = new ClientsModel();
                $id = $this->request->getPost('id');

                $query = $clientsModel->delete($id);

                if ($query) {
                    echo json_encode(['code' => 1, 'message' => 'The client was deleted successfully']);
                } else {
                    echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the client']);
                }
            }
        }
    }
}
