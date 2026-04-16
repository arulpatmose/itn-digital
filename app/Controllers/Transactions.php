<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ChipTransactionModel;
use App\Models\ParticipantModel;
use App\Models\IngestSessionModel;
use App\Services\TransactionService;

class Transactions extends BaseController
{
    protected ChipTransactionModel $txModel;
    protected ParticipantModel     $participantModel;
    protected IngestSessionModel   $sessionModel;
    protected TransactionService   $txService;

    public function __construct()
    {
        $this->txModel          = new ChipTransactionModel();
        $this->participantModel = new ParticipantModel();
        $this->sessionModel     = new IngestSessionModel();
        $this->txService        = new TransactionService();
    }

    /** Transaction log — all transactions. */
    public function index()
    {
        if (!auth()->user()->can('transactions.view')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        return view('backend/transactions/index', [
            'page_title'       => 'Transaction Log',
            'page_description' => 'All chip transactions.',
            'transactions'     => $this->txModel->getAllWithDetails(),
        ]);
    }

    // ── RECEIVE ──────────────────────────────────────────────────────────────

    public function receive()
    {
        if (!auth()->user()->can('transactions.receive')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processTransaction('receive');
        }

        return view('backend/transactions/receive', [
            'page_title'       => 'Receive Chips',
            'page_description' => 'Record chips being received from an external source.',
            'participants'     => $this->participantModel->getActive(),
        ]);
    }

    // ── TRANSFER ─────────────────────────────────────────────────────────────

    public function transfer()
    {
        if (!auth()->user()->can('transactions.transfer')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processTransaction('transfer');
        }

        return view('backend/transactions/transfer', [
            'page_title'       => 'Transfer Chips',
            'page_description' => 'Move chips from one participant to another.',
            'participants'     => $this->participantModel->getActive(),
        ]);
    }

    // ── HANDOVER ─────────────────────────────────────────────────────────────

    public function handover()
    {
        if (!auth()->user()->can('transactions.handover')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processTransaction('handover');
        }

        return view('backend/transactions/handover', [
            'page_title'       => 'Hand Over Chips',
            'page_description' => 'Hand chips over to a producer or library.',
            'participants'     => $this->participantModel->getActive(),
        ]);
    }

    // ── INGEST ────────────────────────────────────────────────────────────────

    public function ingest()
    {
        if (!auth()->user()->can('transactions.ingest')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processTransaction('ingest');
        }

        return view('backend/transactions/ingest', [
            'page_title'       => 'Ingest Chips',
            'page_description' => 'Log chips as ingested into a session.',
            'participants'     => $this->participantModel->getActive(),
            'sessions'         => $this->sessionModel->where('status', 'open')->findAll(),
        ]);
    }

    // ── RETURN ────────────────────────────────────────────────────────────────

    public function returnChips()
    {
        if (!auth()->user()->can('transactions.return')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processTransaction('return');
        }

        return view('backend/transactions/return', [
            'page_title'       => 'Return Chips',
            'page_description' => 'Record chips being returned.',
            'participants'     => $this->participantModel->getActive(),
        ]);
    }

    // ── Shared process logic ──────────────────────────────────────────────────

    protected function processTransaction(string $type): \CodeIgniter\HTTP\RedirectResponse
    {
        $chipIds   = array_filter(array_map('intval', (array) $this->request->getPost('chip_ids')));
        $remarks   = trim($this->request->getPost('remarks') ?? '');
        $handledBy = auth()->id();

        if (empty($chipIds)) {
            return redirect()->back()->withInput()->with('error', 'Please select at least one chip.');
        }

        switch ($type) {
            case 'receive':
                $toId   = (int) $this->request->getPost('to_participant_id');
                $result = $this->txService->receive($chipIds, $toId, $handledBy, $remarks);
                break;

            case 'transfer':
                $fromId = (int) $this->request->getPost('from_participant_id') ?: null;
                $toId   = (int) $this->request->getPost('to_participant_id');
                $result = $this->txService->transfer($chipIds, $fromId, $toId, $handledBy, $remarks);
                break;

            case 'handover':
                $fromId = (int) $this->request->getPost('from_participant_id') ?: null;
                $toId   = (int) $this->request->getPost('to_participant_id');
                $result = $this->txService->handover($chipIds, $fromId, $toId, $handledBy, $remarks);
                break;

            case 'ingest':
                $fromId    = (int) $this->request->getPost('from_participant_id') ?: null;
                $sessionId = (int) $this->request->getPost('ingest_session_id');
                if (!$sessionId) {
                    return redirect()->back()->withInput()->with('error', 'An ingest session is required.');
                }
                $result = $this->txService->ingest($chipIds, $fromId, $handledBy, $sessionId, $remarks);
                break;

            case 'return':
                $fromId = (int) $this->request->getPost('from_participant_id') ?: null;
                $toId   = (int) $this->request->getPost('to_participant_id');
                $result = $this->txService->returnChips($chipIds, $fromId, $toId, $handledBy, $remarks);
                break;

            default:
                return redirect()->back()->with('error', 'Unknown transaction type.');
        }

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', implode(' ', $result['warnings']));
        }

        $msg = ucfirst($type) . ' recorded for ' . count($chipIds) . ' chip(s).';
        if (!empty($result['warnings'])) {
            $msg .= ' Warning: ' . implode(' ', $result['warnings']);
        }

        return redirect()->to('/transactions')->with('success', $msg);
    }
}
