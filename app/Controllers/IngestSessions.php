<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IngestSessionModel;
use App\Models\ChipModel;
use App\Models\ParticipantModel;
use App\Models\TransactionItemModel;
use App\Services\IngestSessionService;
use App\Services\TransactionService;

class IngestSessions extends BaseController
{
    protected IngestSessionModel   $sessionModel;
    protected ChipModel            $chipModel;
    protected ParticipantModel     $participantModel;
    protected TransactionItemModel $itemModel;
    protected IngestSessionService $sessionService;
    protected TransactionService   $txService;

    protected $db;

    public function __construct()
    {
        $this->db               = \Config\Database::connect();
        $this->sessionModel     = new IngestSessionModel();
        $this->chipModel        = new ChipModel();
        $this->participantModel = new ParticipantModel();
        $this->itemModel        = new TransactionItemModel();
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
            'producers'        => $this->participantModel->getProducers(),
            'progress'         => $this->itemModel->getSessionProgress($id),
        ]);
    }

    /** Toggle copy_status on a single transaction item (AJAX). */
    public function updateChipStatus(int $sessionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }
        if (!auth()->user()->can('transactions.ingest')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $itemId    = (int) $this->request->getPost('item_id');
        $newStatus = $this->request->getPost('status') === 'done' ? 'done' : 'pending';

        // Verify the item belongs to this session
        $item = $this->db->query("
            SELECT ti.id FROM transaction_items ti
            JOIN chip_transactions ct ON ct.id = ti.transaction_id
            WHERE ti.id = ? AND ct.ingest_session_id = ? AND ct.transaction_type = 'INGEST'
        ", [$itemId, $sessionId])->getRowArray();

        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Item not found.']);
        }

        $this->itemModel->setCopyStatus($itemId, $newStatus, auth()->id());

        $progress = $this->itemModel->getSessionProgress($sessionId);
        $allDone  = $progress['total'] > 0 && $progress['done'] === $progress['total'];

        return $this->response->setJSON([
            'status'   => 'success',
            'new_status' => $newStatus,
            'progress' => $progress,
            'all_done' => $allDone,
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

    /** Reopen a closed or partial session (AJAX). */
    public function resume(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }
        if (!auth()->user()->can('ingest_sessions.close')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $session = $this->sessionModel->find($id);
        if (!$session || $session['status'] === 'open') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Session is already open or not found.']);
        }

        $this->sessionModel->update($id, ['status' => 'open', 'closed_at' => null]);
        log_activity('ingest_session.resumed', 'ingest_session', $id, 'Session reopened');

        return $this->response->setJSON(['status' => 'success', 'message' => 'Session resumed and set back to open.']);
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
