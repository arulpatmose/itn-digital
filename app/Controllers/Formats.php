<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FormatsModel;

class Formats extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('formats.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Ad Formats";
            $data['page_description'] = "Commercials, sponsorships, product placement, infomercials, overlays, and more.";

            return view('backend/formats/index', $data);
        }
    }

    public function create()
    {
        if (!auth()->user()->can('formats.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Create an Ad Format";
            $data['page_description'] = "Commercials, sponsorships, product placement, infomercials, overlays, and more.";

            return view('backend/formats/add_format', $data);
        }
    }

    public function store()
    {
        if (!auth()->user()->can('formats.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $formatsModel = new FormatsModel();

            // Prepare data to insert
            $data = [
                "code" => trim(strtoupper($this->request->getVar('format-code'))),
                "name" => trim(ucwords($this->request->getVar('format-name')))
            ];

            // Insert into database
            if ($formatsModel->insert($data, false)) {
                $status = 'success';
                $message = 'The format was addded successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while adding the format!';
            }

            return redirect()->to('/formats')->with($status, $message);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('formats.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $formatsModel = new FormatsModel();

            $format = $formatsModel->find($id);

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
    }

    public function update($id)
    {
        if (!auth()->user()->can('formats.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $formatsModel = new FormatsModel();

            // Prepare data to update
            $data = [
                "code" => trim(strtoupper($this->request->getVar('format-code'))),
                "name" => trim(ucwords($this->request->getVar('format-name')))
            ];

            // Insert into database
            if ($formatsModel->update($id, $data, false)) {
                $status = 'success';
                $message = 'The format was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the format!';
            }

            return redirect()->to('/formats')->with($status, $message);
        }
    }

    public function destroy()
    {
        if (!auth()->user()->can('formats.delete')) {
            $code = 0;
            $message = 'You do not have permissions to delele this format!';

            echo json_encode([$code => 0, 'message' => $message]);
        } else {
            if ($this->request->isAjax()) {
                $formatsModel = new FormatsModel();
                $id = $this->request->getPost('id');

                $query = $formatsModel->delete($id);

                if ($query) {
                    echo json_encode(['code' => 1, 'message' => 'The format was deleted successfully']);
                } else {
                    echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the format']);
                }
            }
        }
    }
}
