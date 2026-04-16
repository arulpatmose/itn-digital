<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IngestSessionModel;
use App\Models\ChipModel;
use App\Models\ParticipantModel;
use App\Services\IngestSessionService;
use App\Services\TransactionService;

class IngestSessions extends BaseController
{
    protected IngestSessionModel   $sessionModel;
    protected ChipModel            $chipModel;
    protected ParticipantModel     $participantModel;
    protected IngestSessionService $sessionService;
    protected TransactionService   $txService;

    public function __construct()
    {
        $this->sessionModel     = new IngestSessionModel();
        $this->chipModel        = new ChipModel();
        $this->participantModel = new ParticipantModel();
        $this->sessionService   = new IngestSessionService();
        $this->txService        = new TransactionService();
    }

    public function index()
    {
        if (!auth()->user()->can('ingest_sessions.view')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        return view('backend/ingest_sessions/index', [
            'page_title'       => 'Ingest Sessions',
            'page_description' => 'Manage ingest sessions and associated chips.',
            'sessions'         => $this->sessionModel->getAllWithCreator(),
        ]);
    }

    public function create()
    {
        if (!auth()->user()->can('ingest_sessions.create')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        return view('backend/ingest_sessions/create', [
            'page_title'       => 'New Ingest Session',
            'page_description' => 'Create a new ingest session.',
        ]);
    }

    public function store()
    {
        if (!auth()->user()->can('ingest_sessions.create')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $title    = trim($this->request->getPost('title'));
        $location = trim($this->request->getPost('ingest_location') ?? '');
        $desc     = trim($this->request->getPost('description') ?? '');

        if (!$title) {
            return redirect()->back()->withInput()->with('error', 'Session title is required.');
        }

        $id = $this->sessionService->create($title, auth()->id(), $location ?: null, $desc ?: null);
        return redirect()->to("/ingest-sessions/{$id}")->with('success', "Session \"{$title}\" created.");
    }

    public function view(int $id)
    {
        if (!auth()->user()->can('ingest_sessions.view')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $session = $this->sessionModel->getWithCreator($id);
        if (!$session) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/ingest_sessions/view', [
            'page_title'       => "Session — {$session['title']}",
            'page_description' => 'Ingest session detail and chip log.',
            'session'          => $session,
            'chips'            => $this->chipModel->getBySession($id),
            'participants'     => $this->participantModel->getActive(),
        ]);
    }

    /** Quick ingest chips into this session (AJAX). */
    public function ingestChips(int $id)
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }
        if (!auth()->user()->can('transactions.ingest')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $session = $this->sessionModel->find($id);
        if (!$session || $session['status'] === 'closed') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Session not found or already closed.']);
        }

        $chipIds = array_filter(array_map('intval', (array) $this->request->getPost('chip_ids')));
        if (empty($chipIds)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'No chips selected.']);
        }

        $fromId  = (int) $this->request->getPost('from_participant_id') ?: null;
        $remarks = trim($this->request->getPost('remarks') ?? '');
        $result  = $this->txService->ingest($chipIds, $fromId, auth()->id(), $id, $remarks);

        if (!$result['success']) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => implode(' ', $result['warnings'])]);
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => count($chipIds) . ' chip(s) ingested.',
            'warnings' => $result['warnings'],
        ]);
    }

    /** Close or mark partial (AJAX). */
    public function close(int $id)
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }
        if (!auth()->user()->can('ingest_sessions.close')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $status = $this->request->getPost('status') === 'partial' ? 'partial' : 'closed';
        $this->sessionService->close($id, $status);

        return $this->response->setJSON(['status' => 'success', 'message' => "Session marked as {$status}."]);
    }
}
