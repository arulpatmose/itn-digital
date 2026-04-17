<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ChipTransactionModel;
use App\Models\ChipModel;
use App\Models\ParticipantModel;
use App\Services\TransactionService;
use App\Services\ChipService;
use App\Services\IngestSessionService;

class Transactions extends BaseController
{
    protected ChipTransactionModel  $txModel;
    protected ChipModel             $chipModel;
    protected ParticipantModel      $participantModel;
    protected TransactionService    $txService;
    protected ChipService           $chipService;
    protected IngestSessionService  $sessionService;

    public function __construct()
    {
        $this->txModel          = new ChipTransactionModel();
        $this->chipModel        = new ChipModel();
        $this->participantModel = new ParticipantModel();
        $this->txService        = new TransactionService();
        $this->chipService      = new ChipService();
        $this->sessionService   = new IngestSessionService();
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

        if ($this->request->getMethod() === 'POST') {
            return $this->processTransaction('receive');
        }

        return view('backend/transactions/receive', [
            'page_title'       => 'Receive Chips',
            'page_description' => 'Record chips arriving at ITN Digital from a producer.',
            'producers'        => $this->participantModel->getProducers(),
        ]);
    }

    // ── TRANSFER ─────────────────────────────────────────────────────────────

    public function transfer()
    {
        if (!auth()->user()->can('transactions.transfer')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->processTransaction('transfer');
        }

        return view('backend/transactions/transfer', [
            'page_title'         => 'Transfer Chips',
            'page_description'   => 'Transfer chips from yourself to another producer.',
            'currentParticipant' => $this->participantModel->getByUserId(auth()->id()),
            'producers'          => $this->participantModel->getProducers(),
        ]);
    }

    // ── HANDOVER ─────────────────────────────────────────────────────────────

    public function handover()
    {
        if (!auth()->user()->can('transactions.handover')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->processTransaction('handover');
        }

        return view('backend/transactions/handover', [
            'page_title'       => 'Hand Over Chips',
            'page_description' => 'Return chips to the library — this closes the chip cycle.',
        ]);
    }

    // ── INGEST ────────────────────────────────────────────────────────────────

    public function ingest()
    {
        if (!auth()->user()->can('transactions.ingest')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->processTransaction('ingest');
        }

        $preloadIds       = (array) session()->getFlashdata('ingest_chip_ids');
        $preloadChips     = !empty($preloadIds)
            ? array_filter($this->chipService->getSelect2Data(), fn($c) => in_array($c['id'], $preloadIds))
            : [];

        return view('backend/transactions/ingest', [
            'page_title'       => 'Ingest Chips',
            'page_description' => 'Log chips as ingested into a new session.',
            'preloadChips'     => array_values($preloadChips),
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

        $currentParticipant = $this->participantModel->getByUserId(auth()->id());

        $ingestSessionId = null;
        switch ($type) {
            case 'receive':
                $fromId = (int) $this->request->getPost('from_participant_id') ?: null;
                $result = $this->txService->receive($chipIds, $fromId, $handledBy, $remarks);
                break;

            case 'transfer':
                $inLibrary = $this->chipModel->getChipsHeldByLibrarian($chipIds);
                if (!empty($inLibrary)) {
                    $list = implode(', ', array_map(fn($c) => "{$c['chip_code']} (held by {$c['holder_name']})", $inLibrary));
                    return redirect()->back()->withInput()->with('error', "Cannot transfer — these chips are in the library. Please receive them first: {$list}.");
                }
                $blocked = $this->chipModel->getChipsInOpenSessions($chipIds);
                if (!empty($blocked)) {
                    $list = implode(', ', array_map(fn($c) => "{$c['chip_code']} (in \"{$c['session_title']}\")", $blocked));
                    return redirect()->back()->withInput()->with('error', "Cannot transfer — these chips are in an open ingest session: {$list}.");
                }
                $fromId = (int) $this->request->getPost('from_participant_id') ?: null;
                $toId   = (int) $this->request->getPost('to_participant_id');
                $result = $this->txService->transfer($chipIds, $fromId, $toId, $handledBy, $remarks);
                break;

            case 'handover':
                $inLibrary = $this->chipModel->getChipsHeldByLibrarian($chipIds);
                if (!empty($inLibrary)) {
                    $list = implode(', ', array_map(fn($c) => "{$c['chip_code']}", $inLibrary));
                    return redirect()->back()->withInput()->with('error', "Cannot hand over — these chips are already in the library: {$list}.");
                }
                $blocked = $this->chipModel->getChipsInOpenSessions($chipIds);
                if (!empty($blocked)) {
                    $list = implode(', ', array_map(fn($c) => "{$c['chip_code']} (in \"{$c['session_title']}\")", $blocked));
                    return redirect()->back()->withInput()->with('error', "Cannot hand over — these chips are in an open ingest session: {$list}.");
                }
                $result = $this->txService->handover($chipIds, $handledBy, $remarks);
                break;

            case 'ingest':
                $sessionTitle = trim($this->request->getPost('session_title') ?? '');
                if (!$sessionTitle) {
                    return redirect()->back()->withInput()->with('error', 'A session title is required for ingest.');
                }
                $location = trim($this->request->getPost('ingest_location') ?? '');
                if (!$location) {
                    return redirect()->back()->withInput()->with('error', 'Ingest path is required.');
                }
                $ingestSessionId = $this->sessionService->create($sessionTitle, $handledBy, $location, $remarks ?: null);
                $result          = $this->txService->ingest($chipIds, null, $handledBy, $ingestSessionId, null);
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

        if ($type === 'receive') {
            return redirect()->to('/transactions/ingest')
                ->with('success', $msg)
                ->with('ingest_chip_ids', $chipIds);
        }

        if ($type === 'ingest' && $ingestSessionId) {
            return redirect()->to("/ingest-sessions/{$ingestSessionId}")->with('success', $msg);
        }

        return redirect()->to('/transactions')->with('success', $msg);
    }
}
