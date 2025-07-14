<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FormatModel;
use CodeIgniter\HTTP\ResponseInterface;

class Formats extends BaseController
{
    protected $formatModel;

    public function __construct()
    {
        $this->formatModel = new FormatModel();
    }

    public function index()
    {
        if (!auth()->user()->can('formats.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Ad Formats";
        $data['page_description'] = "Commercials, sponsorships, product placement, infomercials, overlays, and more.";

        return view('backend/formats/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('formats.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Create an Ad Format";
        $data['page_description'] = "Commercials, sponsorships, product placement, infomercials, overlays, and more.";

        return view('backend/formats/add_format', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('formats.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Prepare data to insert
        $data = [
            "code" => trim(strtoupper($this->request->getVar('format-code'))),
            "name" => trim(ucwords($this->request->getVar('format-name')))
        ];

        // Insert into database
        if ($this->formatModel->insert($data, false)) {
            $status = 'success';
            $message = 'The format was addded successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while adding the format!';
        }

        return redirect()->to('/formats')->with($status, $message);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('formats.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $format = $this->formatModel->find($id);

        if (isset($format) && !empty($format)) {
            $data['format'] = $format;

            $itemName = $format['name'];

            $data['page_title'] = "Edit Ad Format - " . $itemName;
            $data['page_description'] = "Commercials, sponsorships, product placement, infomercials, overlays, and more.";

            return view('backend/formats/edit_format', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('formats.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Prepare data to update
        $data = [
            "code" => trim(strtoupper($this->request->getVar('format-code'))),
            "name" => trim(ucwords($this->request->getVar('format-name')))
        ];

        // Insert into database
        if ($this->formatModel->update($id, $data, false)) {
            $status = 'success';
            $message = 'The format was updated successfully!';
        } else {
            $status = 'error';
            $message = 'There was an error while updating the format!';
        }

        return redirect()->to('/formats')->with($status, $message);
    }

    public function destroy()
    {
        if (!auth()->user()->can('formats.delete')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You do not have permissions to delete this format!'
            ])->setStatusCode(403);
        }

        if ($this->request->isAjax()) {
            $id = $this->request->getPost('id');
            $query = $this->formatModel->delete($id);

            if ($query) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'The format was deleted successfully'
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An error occurred while deleting the format'
                ])->setStatusCode(500);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid request method. AJAX request required.'
        ])->setStatusCode(400);
    }
}
