<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ChipModel;
use App\Services\ChipService;

class Chips extends BaseController
{
    protected ChipModel   $chipModel;
    protected ChipService $chipService;

    public function __construct()
    {
        $this->chipModel   = new ChipModel();
        $this->chipService = new ChipService();
    }

    public function index()
    {
        if (!auth()->user()->can('chips.view')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        return view('backend/chips/index', [
            'page_title'       => 'Chips',
            'page_description' => 'All registered chips and their current holders.',
            'chips'            => $this->chipModel->getAllWithCurrentHolder(),
        ]);
    }

    public function create()
    {
        if (!auth()->user()->can('chips.create')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        return view('backend/chips/create', [
            'page_title'       => 'Register Chip',
            'page_description' => 'Add a new chip to the tracking system.',
        ]);
    }

    public function store()
    {
        if (!auth()->user()->can('chips.create')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $data = [
            'chip_type' => $this->request->getPost('chip_type'),
            'chip_code' => strtoupper(trim($this->request->getPost('chip_code'))),
            'notes'     => trim($this->request->getPost('notes') ?? ''),
        ];

        if (!in_array($data['chip_type'], ['SXS', 'SD', 'MicroSD', 'Other'], true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid chip type.');
        }
        if (empty($data['chip_code'])) {
            return redirect()->back()->withInput()->with('error', 'Chip code is required.');
        }
        if ($this->chipModel->where('chip_code', $data['chip_code'])->first()) {
            return redirect()->back()->withInput()->with('error', "Chip code {$data['chip_code']} already exists.");
        }

        $id = $this->chipModel->insert($data, true);
        log_activity('chip.created', 'chip', $id, "Registered chip {$data['chip_code']}");

        return redirect()->to('/chips')->with('success', "Chip {$data['chip_code']} registered successfully.");
    }

    public function edit(int $id)
    {
        if (!auth()->user()->can('chips.edit')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $chip = $this->chipModel->find($id);
        if (!$chip) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/chips/edit', [
            'page_title'       => "Edit Chip — {$chip['chip_code']}",
            'page_description' => 'Update chip details.',
            'chip'             => $chip,
        ]);
    }

    public function update(int $id)
    {
        if (!auth()->user()->can('chips.edit')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $chip = $this->chipModel->find($id);
        if (!$chip) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $newCode = strtoupper(trim($this->request->getPost('chip_code')));
        $existing = $this->chipModel->where('chip_code', $newCode)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Chip code {$newCode} is already in use.");
        }

        $this->chipModel->update($id, [
            'chip_type' => $this->request->getPost('chip_type'),
            'chip_code' => $newCode,
            'notes'     => trim($this->request->getPost('notes') ?? ''),
        ]);

        log_activity('chip.updated', 'chip', $id, "Updated chip {$newCode}");
        return redirect()->to('/chips')->with('success', "Chip {$newCode} updated.");
    }

    public function detail(int $id)
    {
        if (!auth()->user()->can('chips.view')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $chip = $this->chipModel->getWithCurrentHolder($id);
        if (!$chip) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/chips/detail', [
            'page_title'       => "Chip — {$chip['chip_code']}",
            'page_description' => 'Full transaction history for this chip.',
            'chip'             => $chip,
            'timeline'         => $this->chipModel->getTimeline($id),
        ]);
    }

    public function destroy()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }
        if (!auth()->user()->can('chips.delete')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id   = (int) $this->request->getPost('id');
        $chip = $this->chipModel->find($id);
        if (!$chip) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Chip not found.']);
        }

        $this->chipModel->delete($id);
        log_activity('chip.deleted', 'chip', $id, "Deleted chip {$chip['chip_code']}");
        return $this->response->setJSON(['status' => 'success', 'message' => 'Chip deleted.']);
    }

    /** AJAX — chip list for Select2. */
    public function apiList()
    {
        if (!auth()->user()->can('chips.view')) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }
        $search             = $this->request->getGet('q');
        $excludeOpenSession = (bool) $this->request->getGet('exclude_open_session');
        $excludeLocation    = $this->request->getGet('exclude_location') ?: null;
        $excludeSessionId   = ($v = $this->request->getGet('exclude_session_id')) ? (int) $v : null;
        $onlyLocation       = $this->request->getGet('only_location') ?: null;
        return $this->response->setJSON($this->chipService->getSelect2Data($search, $excludeOpenSession, $excludeLocation, $excludeSessionId, $onlyLocation));
    }
}
