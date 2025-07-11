<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\CommercialModel;
use App\Models\FormatModel;

class Commercials extends BaseController
{
    protected $clientModel;
    protected $commercialModel;
    protected $formatModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->commercialModel = new CommercialModel();
        $this->formatModel = new FormatModel();
    }

    public function index()
    {
        if (!auth()->user()->can('commercials.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Commercials";
        $data['page_description'] = "Commercials that convey messages visually to captivate broad audiences.";

        return view('backend/commercials/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('commercials.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['formats'] = $this->formatModel->select('format_id as id, name')->findAll();
        $data['clients'] = $this->clientModel->select('client_id as id, name')->findAll();

        $data['page_title'] = "Create a Commercial";
        $data['page_description'] = "Commercials that convey messages visually to captivate broad audiences.";

        return view('backend/commercials/add_commercial', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('commercials.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

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
        if ($this->commercialModel->insert($data, false)) {
            $status = 'success';
            $message = 'The commercial was addded successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while adding the commercial!';
        }

        return redirect()->to('/commercials')->with($status, $message);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('commercials.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $commercial = $this->commercialModel->find($id);

        if (isset($commercial) && !empty($commercial)) {
            $data['formats'] = $this->formatModel->select('format_id as id, name')->findAll();
            $data['clients'] = $this->clientModel->select('client_id as id, name')->findAll();

            $data['commercial'] = $commercial;

            $itemName = $commercial['name'];

            $data['page_title'] = "Edit Commercial - " . $itemName;
            $data['page_description'] = "Commercials that convey messages visually to captivate broad audiences.";

            return view('backend/commercials/edit_commercial', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('commercials.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

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
        if ($this->commercialModel->update($id, $data, false)) {
            $status = 'success';
            $message = 'The commercial was updated successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while updating the commercial!';
        }

        return redirect()->to('/commercials')->with($status, $message);
    }

    public function destroy()
    {
        if (!auth()->user()->can('commercials.delete')) {
            $status = 'error';
            $message = 'You do not have permissions to delete this commercial!';
            return $this->response->setJSON(['status' => $status, 'message' => $message])->setStatusCode(403);
        }

        if ($this->request->isAjax()) {
            $id = $this->request->getPost('id');

            $query = $this->commercialModel->delete($id);

            if ($query) {
                $status = 'success';
                $message = 'The commercial was deleted successfully';
                return $this->response->setJSON(['status' => $status, 'message' => $message])->setStatusCode(200);
            } else {
                $status = 'error';
                $message = 'An error occurred while deleting the commercial';
                return $this->response->setJSON(['status' => $status, 'message' => $message])->setStatusCode(500);
            }
        }
    }

    // Generator function is used to Generate Key
    function generator($lenth)
    {
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

        $result = $this->commercialModel->commercialIdCheck($generatedID);

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

        $lastId = $this->commercialModel->getLastCommercialID();

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
