<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ParticipantModel;

class Participants extends BaseController
{
    protected ParticipantModel $participantModel;

    public function __construct()
    {
        $this->participantModel = new ParticipantModel();
    }

    public function index()
    {
        if (!auth()->user()->can('participants.view')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        return view('backend/participants/index', [
            'page_title'       => 'Participants',
            'page_description' => 'Staff, producers, and library participants in the chip tracking system.',
            'participants'     => $this->participantModel->getAllWithUser(),
        ]);
    }

    public function create()
    {
        if (!auth()->user()->can('participants.create')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $users = \Config\Database::connect()
            ->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users ORDER BY first_name")
            ->getResultArray();

        return view('backend/participants/create', [
            'page_title'       => 'Add Participant',
            'page_description' => 'Register a new participant in the chip tracking system.',
            'users'            => $users,
        ]);
    }

    public function store()
    {
        if (!auth()->user()->can('participants.create')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $type   = $this->request->getPost('type');
        $name   = trim($this->request->getPost('name'));
        $userId = (int) ($this->request->getPost('user_id') ?: 0);

        if (!in_array($type, ['staff', 'producer', 'library'], true) || !$name) {
            return redirect()->back()->withInput()->with('error', 'Name and type are required.');
        }

        $id = $this->participantModel->insert([
            'name'    => $name,
            'type'    => $type,
            'user_id' => $userId ?: null,
            'notes'   => trim($this->request->getPost('notes') ?? ''),
        ], true);

        log_activity('participant.created', 'participant', $id, "Created participant {$name}");
        return redirect()->to('/participants')->with('success', "Participant {$name} added.");
    }

    public function edit(int $id)
    {
        if (!auth()->user()->can('participants.edit')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $participant = $this->participantModel->find($id);
        if (!$participant) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $users = \Config\Database::connect()
            ->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users ORDER BY first_name")
            ->getResultArray();

        return view('backend/participants/edit', [
            'page_title'   => "Edit Participant — {$participant['name']}",
            'participant'  => $participant,
            'users'        => $users,
        ]);
    }

    public function update(int $id)
    {
        if (!auth()->user()->can('participants.edit')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $participant = $this->participantModel->find($id);
        if (!$participant) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $userId = (int) ($this->request->getPost('user_id') ?: 0);
        $name   = trim($this->request->getPost('name'));

        $this->participantModel->update($id, [
            'name'      => $name,
            'type'      => $this->request->getPost('type'),
            'user_id'   => $userId ?: null,
            'notes'     => trim($this->request->getPost('notes') ?? ''),
            'is_active' => (int) ($this->request->getPost('is_active') ?? 1),
        ]);

        log_activity('participant.updated', 'participant', $id, "Updated participant {$name}");
        return redirect()->to('/participants')->with('success', "Participant {$name} updated.");
    }

    public function destroy()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }
        if (!auth()->user()->can('participants.delete')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id          = (int) $this->request->getPost('id');
        $participant = $this->participantModel->find($id);
        if (!$participant) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Not found.']);
        }

        $this->participantModel->delete($id);
        log_activity('participant.deleted', 'participant', $id, "Deleted {$participant['name']}");
        return $this->response->setJSON(['status' => 'success', 'message' => 'Participant deleted.']);
    }

    /** AJAX — for Select2 dropdowns. */
    public function apiList()
    {
        if (!auth()->user()->can('participants.view')) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }
        $type   = $this->request->getGet('type');
        $search = strtolower($this->request->getGet('q') ?? '');

        $all = $type
            ? $this->participantModel->getByType($type)
            : $this->participantModel->getActive();

        if ($search) {
            $all = array_filter($all, fn($p) => str_contains(strtolower($p['name']), $search));
        }

        $data = array_values(array_map(fn($p) => [
            'id'   => $p['id'],
            'text' => "[{$p['type']}] {$p['name']}",
        ], $all));

        return $this->response->setJSON($data);
    }
}
