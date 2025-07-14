<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProgramModel;
use CodeIgniter\HTTP\ResponseInterface;

class Programs extends BaseController
{
    protected $programModel;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
        helper('file_upload');
    }

    public function index()
    {
        if (!auth()->user()->can('programs.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Programs";
        $data['page_description'] = "Teledramas and TV Shows broadcast by ITN.";

        return view('backend/programs/index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('programs.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $data['page_title'] = "Create a Program";
        $data['page_description'] = "Teledramas and TV Shows broadcast by ITN.";

        return view('backend/programs/add_program', $data);
    }

    public function store()
    {
        if (!auth()->user()->can('programs.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        // Get Thumbnail File
        $thumbFile = $this->request->getFile('program-thumbnail');
        $thumbName = handleThumbnailUpload($thumbFile);

        // Prepare data to insert
        $data = [
            "name" => trim(ucwords($this->request->getVar('program-name'))),
            "type" => $this->request->getVar('program-type'),
            "thumbnail" => $thumbName
        ];

        // Insert into database
        if ($this->programModel->insert($data, false)) {
            return redirect()->to('/programs')->with('success', 'The program was added successfully!');
        }

        return redirect()->back()->with('error', 'There was an error while adding the program!');
    }

    public function edit($id)
    {
        if (!auth()->user()->can('programs.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        }

        $program = $this->programModel->find($id);

        if (isset($program) && !empty($program)) {
            $data['program'] = $program;

            $itemName = $program['name'];

            $data['thumbImage'] = $data['program']['thumbnail'] ?? 'No-Image-Placeholder.svg';

            $data['page_title'] = "Edit Program - " . $itemName;
            $data['page_description'] = "Teledramas and TV Shows broadcast by ITN.";

            return view('backend/programs/edit_program', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        if (!auth()->user()->can('programs.edit')) {
            return redirect()->back()->with('error', 'You do not have permissions to access that page!');
        }

        helper('file');

        $program = $this->programModel->find($id);
        if (!$program) {
            return redirect()->back()->with('error', 'Program not found!');
        }

        $thumbFile = $this->request->getFile('program-thumbnail');
        $removeThumb = $this->request->getVar('remove-thumbnail');
        $thumbName = handleThumbnailUpload($thumbFile, $program['thumbnail'], $removeThumb);

        $data = [
            "name" => trim(ucwords($this->request->getVar('program-name'))),
            "type" => $this->request->getVar('program-type'),
            "thumbnail" => $thumbName
        ];

        if ($this->programModel->update($id, $data, false)) {
            return redirect()->to('/programs')->with('success', 'The program was updated successfully!');
        }

        return redirect()->back()->with('error', 'There was an error while updating the program!');
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

        if (!auth()->user()->can('programs.delete')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You do not have permissions to delete this program!'
            ])->setStatusCode(403);
        }

        $id = $this->request->getPost('id');
        $program = $this->programModel->find($id);

        // Remove thumbnail if it exists
        if ($program) {
            removeThumbnailFile($program['thumbnail']);
        }

        if ($this->programModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'The program was deleted successfully'
            ])->setStatusCode(200);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'An error occurred while deleting the program'
        ])->setStatusCode(500);
    }
}
