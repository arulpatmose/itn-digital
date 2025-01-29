<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProgramsModel;

class Programs extends BaseController
{
    public function index()
    {
        if (!auth()->user()->can('programs.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Programs";
            $data['page_description'] = "Teledramas and TV Shows broadcast by ITN.";

            return view('backend/programs/index', $data);
        }
    }

    public function create()
    {
        if (!auth()->user()->can('programs.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data['page_title'] = "Create a Program";
            $data['page_description'] = "Teledramas and TV Shows broadcast by ITN.";

            return view('backend/programs/add_program', $data);
        }
    }

    public function store()
    {
        if (!auth()->user()->can('programs.create')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $programsModel = new ProgramsModel();

            // Get Thumbnail File
            $file = $this->request->getFile('program-thumbnail');

            // If thumbanil file is valid, do upload
            if ($file->isValid() && !$file->hasMoved()) {
                $thumbName = $file->getRandomName();

                $file->move('uploads/thumbnails', $thumbName);
            }

            // Prepare data to insert
            $data = [
                "name" => trim(ucwords($this->request->getVar('program-name'))),
                "type" => $this->request->getVar('program-type'),
                "thumbnail" => isset($thumbName) ? $thumbName : NULL
            ];

            // Insert into database
            if ($programsModel->insert($data, false)) {
                $status = 'success';
                $message = 'The program was addded successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while adding the program!';
            }

            return redirect()->to('/programs')->with($status, $message);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('programs.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $programsModel = new ProgramsModel();

            $program = $programsModel->find($id);

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
    }

    public function update($id)
    {
        if (!auth()->user()->can('programs.edit')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $programsModel = new ProgramsModel();

            $program = $programsModel->find($id);
            $oldThumbName = $program['thumbnail'];

            // Get Thumbnail File
            $file = $this->request->getFile('program-thumbnail');

            // If thumbanil file is valid, do upload
            if ($file->isValid() && !$file->hasMoved()) {
                // Delete old thumbnail file if exists
                if (isset($oldThumbName) && file_exists("uploads/thumbnails/" . $oldThumbName)) {
                    unlink("uploads/thumbnails/" . $oldThumbName);
                }
                // Upload new thumbnail
                $ThumbName = $file->getRandomName();
                $file->move('uploads/thumbnails', $ThumbName);
            } else {
                $ThumbName = $oldThumbName;
            }

            // Remove thumbnail if selected

            $removeThumb = $this->request->getVar('remove-thumbnail');

            if (isset($removeThumb) && $removeThumb == 1) {
                if (isset($oldThumbName) && file_exists("uploads/thumbnails/" . $oldThumbName)) {
                    unlink("uploads/thumbnails/" . $oldThumbName);
                }

                $ThumbName = NULL;
            }

            // Prepare data to update
            $data = [
                "name" => trim(ucwords($this->request->getVar('program-name'))),
                "type" => $this->request->getVar('program-type'),
                "thumbnail" => isset($ThumbName) ? $ThumbName : NULL
            ];

            // Insert into database
            if ($programsModel->update($id, $data, false)) {
                $status = 'success';
                $message = 'The program was updated successfully!';
            } else {
                $status = 'error';
                $message = 'There was an error while updating the program!';
            }

            return redirect()->to('/programs')->with($status, $message);
        }
    }

    public function destroy()
    {
        if (!auth()->user()->can('programs.delete')) {
            $code = 0;
            $message = 'You do not have permissions to delele this program!';

            echo json_encode([$code => 0, 'message' => $message]);
        } else {
            if ($this->request->isAjax()) {
                $programsModel = new ProgramsModel();
                $id = $this->request->getPost('id');

                $query = $programsModel->delete($id);

                if ($query) {
                    echo json_encode(['code' => 1, 'message' => 'The program was deleted successfully']);
                } else {
                    echo json_encode(['code' => 0, 'message' => 'An error occured while deleting the program']);
                }
            }
        }
    }
}
