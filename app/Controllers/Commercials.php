<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientsModel;
use App\Models\CommercialsModel;
use App\Models\FormatsModel;

class Commercials extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('commercials.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Commercials";
            $data['page_description'] = "Commercials that convey messages visually to captivate broad audiences.";

            return view('backend/commercials/index', $data);
        }
    }

    public function create()
    {
        if (!auth()->user()->can('commercials.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $formatModel = new FormatsModel();
            $clientsModel = new ClientsModel();

            $data['formats'] = $formatModel->select('format_id as id, name')->findAll();
            $data['clients'] = $clientsModel->select('client_id as id, name')->findAll();

            $data['page_title'] = "Create a Commercial";
            $data['page_description'] = "Commercials that convey messages visually to captivate broad audiences.";

            return view('backend/commercials/add_commercial', $data);
        }
    }

    public function store()
    {
        if (!auth()->user()->can('commercials.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $commercialModel = new CommercialsModel();

            $userID = auth()->id();

            // Prepare data to insert
            $data = [
                // "ucom_id" => $this->generator(3),
                "ucom_id" => $this->generateUniqueID(),
                "name" => trim(ucwords($this->request->getVar('commercial-name'))),
                "duration" => $this->request->getVar('commercial-duration'),
                "format" => $this->request->getVar('commercial-format'),
                "category" => trim(strtoupper($this->request->getVar('commercial-category'))),
                "sub_category" => trim(strtoupper($this->request->getVar('commercial-sub-category'))),
                "client" => $this->request->getVar('commercial-client'),
                "remarks" => $this->request->getVar('commercial-remarks'),
                "link" => $this->request->getVar('commercial-link'),
                "added_by" => $userID
            ];

            // Insert into database
            if ($commercialModel->insert($data, false)) {
                $status = 'success';
                $message = 'The commercial was addded successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while adding the commercial!';
            }

            return redirect()->to('/commercials')->with($status, $message);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('commercials.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $commercialModel = new CommercialsModel();

            $commercial = $commercialModel->find($id);

            if (isset($commercial) && !empty($commercial)) {
                $formatModel = new FormatsModel();
                $clientsModel = new ClientsModel();

                $data['formats'] = $formatModel->select('format_id as id, name')->findAll();
                $data['clients'] = $clientsModel->select('client_id as id, name')->findAll();

                $data['commercial'] = $commercial;

                $itemName = $commercial['name'];

                $data['page_title'] = "Edit Commercial - " . $itemName;
                $data['page_description'] = "Commercials that convey messages visually to captivate broad audiences.";

                return view('backend/commercials/edit_commercial', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('commercials.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $commercialModel = new CommercialsModel();

            // Prepare data to update
            $data = [
                "name" => trim(ucwords($this->request->getVar('commercial-name'))),
                "duration" => $this->request->getVar('commercial-duration'),
                "format" => $this->request->getVar('commercial-format'),
                "category" => trim(strtoupper($this->request->getVar('commercial-category'))),
                "sub_category" => trim(strtoupper($this->request->getVar('commercial-sub-category'))),
                "client" => $this->request->getVar('commercial-client'),
                "remarks" => $this->request->getVar('commercial-remarks'),
                "link" => $this->request->getVar('commercial-link'),
            ];

            // Insert into database
            if ($commercialModel->update($id, $data, false)) {
                $status = 'success';
                $message = 'The commercial was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the commercial!';
            }

            return redirect()->to('/commercials')->with($status, $message);
        }
    }

    public function destroy()
    {
        if (!auth()->user()->can('commercials.delete')) {
            $code = 0;
            $message = 'You do not have permissions to delele this commercial!';

            echo json_encode([$code => 0, 'message' => $message]);
        } else {
            if ($this->request->isAjax()) {
                $commercialModel = new CommercialsModel();
                $id = $this->request->getPost('id');

                $query = $commercialModel->delete($id);

                if ($query) {
                    echo json_encode(['code' => 1, 'message' => 'The commercial was deleted successfully']);
                } else {
                    echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the commercial']);
                }
            }
        }
    }

    // Generator function is used to Generate Key

    function generator($lenth)
    {
        $commercialModel = new CommercialsModel();

        $number = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 8);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }

        $generatedID = 'IDCOM' . date('Ymd') . $con;

        $result = $commercialModel->commercialIdCheck($generatedID);

        if ($result === true) {
            $this->generator($lenth);
        } else {
            return $generatedID;
        }
    }

    // Function to generate unique ID based on date and auto-incremented ID
    public function generateUniqueID()
    {
        // Get today's date
        $today = date('Ymd');

        $commercialModel = new CommercialsModel();

        $lastId = $commercialModel->getLastCommercialID();

        // Extract the date from the last inserted record
        $lastDate = $lastId ? date('Ymd', strtotime($lastId->created_at)) : null;

        // Initialize the lastIdValue to start the count for the day
        $lastIdValue = '001';

        // If it's the same day, increment the count
        if ($lastDate === $today) {
            // Extract the ID value and increment the count
            $lastIdValue = $lastId ? str_pad(substr($lastId->ucom_id, -3) + 1, 3, '0', STR_PAD_LEFT) : '001';
        }

        // Construct the unique ID by combining fixed text, date, and auto-incremented value
        $uniqueId = 'IDCOM' . date('ymd') . $lastIdValue;

        return $uniqueId;
    }
}
