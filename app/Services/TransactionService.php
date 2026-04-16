<?php

namespace App\Services;

use App\Models\ChipModel;
use App\Models\ChipTransactionModel;
use App\Models\TransactionItemModel;

class TransactionService
{
    protected ChipModel            $chipModel;
    protected ChipTransactionModel $txModel;
    protected TransactionItemModel $itemModel;

    public function __construct()
    {
        $this->chipModel = new ChipModel();
        $this->txModel   = new ChipTransactionModel();
        $this->itemModel = new TransactionItemModel();
    }

    /**
     * Core method. Creates a transaction and its items.
     * Returns ['success' => bool, 'transaction_id' => int|null, 'warnings' => array].
     */
    public function createTransaction(
        string  $type,
        ?int    $fromParticipantId,
        ?int    $toParticipantId,
        array   $chipIds,
        int     $handledBy,
        ?int    $sessionId = null,
        ?string $remarks   = null
    ): array {
        if (empty($chipIds)) {
            return ['success' => false, 'transaction_id' => null, 'warnings' => ['No chips selected.']];
        }

        $warnings = $this->buildWarnings($chipIds, $sessionId);

        $db = \Config\Database::connect();
        $db->transStart();

        $txId = $this->txModel->insert([
            'transaction_type'    => $type,
            'from_participant_id' => $fromParticipantId,
            'to_participant_id'   => $toParticipantId,
            'ingest_session_id'   => $sessionId,
            'handled_by'          => $handledBy,
            'remarks'             => $remarks ?: null,
        ], true);

        if (!$txId) {
            $db->transRollback();
            return ['success' => false, 'transaction_id' => null, 'warnings' => ['Failed to create the transaction record. Check for database errors.']];
        }

        $this->itemModel->insertForTransaction($txId, $chipIds);

        $db->transComplete();

        if (!$db->transStatus()) {
            return ['success' => false, 'transaction_id' => null, 'warnings' => ['Database error — transaction rolled back.']];
        }

        log_activity("transaction.{$type}", 'chip_transaction', $txId,
            strtolower($type) . ' — ' . count($chipIds) . ' chip(s)');

        return ['success' => true, 'transaction_id' => $txId, 'warnings' => $warnings];
    }

    public function receive(array $chipIds, ?int $fromParticipantId, int $toParticipantId, int $handledBy, ?string $remarks = null): array
    {
        return $this->createTransaction('RECEIVE', $fromParticipantId, $toParticipantId, $chipIds, $handledBy, null, $remarks);
    }

    public function transfer(array $chipIds, ?int $fromParticipantId, int $toParticipantId, int $handledBy, ?string $remarks = null): array
    {
        return $this->createTransaction('TRANSFER', $fromParticipantId, $toParticipantId, $chipIds, $handledBy, null, $remarks);
    }

    public function handover(array $chipIds, ?int $fromParticipantId, int $toParticipantId, int $handledBy, ?string $remarks = null): array
    {
        return $this->createTransaction('HANDOVER', $fromParticipantId, $toParticipantId, $chipIds, $handledBy, null, $remarks);
    }

    public function ingest(array $chipIds, ?int $fromParticipantId, int $handledBy, int $sessionId, ?string $remarks = null): array
    {
        return $this->createTransaction('INGEST', $fromParticipantId, null, $chipIds, $handledBy, $sessionId, $remarks);
    }

    /**
     * Check each chip for active session conflicts.
     */
    protected function buildWarnings(array $chipIds, ?int $newSessionId): array
    {
        $warnings = [];
        foreach ($chipIds as $chipId) {
            $active = $this->chipModel->getActiveSession((int) $chipId);
            if ($active && $active['id'] !== $newSessionId) {
                $chip = $this->chipModel->find((int) $chipId);
                $warnings[] = "Chip {$chip['chip_code']} is already active in open session \"{$active['title']}\".";
            }
        }
        return $warnings;
    }
}
