<?php

namespace App\Models;

use CodeIgniter\Model;

class ChipModel extends Model
{
    protected $table            = 'chips';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['chip_type', 'chip_code', 'notes'];

    /**
     * All chips with their current holder resolved from the latest transaction.
     * Current holder = to_participant_id of the max transaction_id involving that chip.
     */
    public function getAllWithCurrentHolder(): array
    {
        return $this->db->query("
            SELECT
                c.*,
                p.id   AS holder_id,
                p.name AS holder_name,
                p.type AS holder_type,
                ct.transaction_type AS last_tx_type,
                ct.created_at       AS last_tx_at,
                s.id    AS ingest_session_id,
                s.title AS ingest_session_title
            FROM chips c
            LEFT JOIN (
                SELECT ti.chip_id, MAX(ct2.id) AS max_tx_id
                FROM transaction_items ti
                JOIN chip_transactions ct2 ON ct2.id = ti.transaction_id
                GROUP BY ti.chip_id
            ) latest ON latest.chip_id = c.id
            LEFT JOIN chip_transactions ct ON ct.id = latest.max_tx_id
            LEFT JOIN participants p ON p.id = ct.to_participant_id
            LEFT JOIN ingest_sessions s ON s.id = ct.ingest_session_id
            ORDER BY c.chip_type, c.chip_code
        ")->getResultArray();
    }

    /**
     * Single chip with current holder.
     */
    public function getWithCurrentHolder(int $chipId): ?array
    {
        return $this->db->query("
            SELECT
                c.*,
                p.id   AS holder_id,
                p.name AS holder_name,
                p.type AS holder_type,
                ct.transaction_type AS last_tx_type,
                ct.created_at       AS last_tx_at,
                s.id    AS ingest_session_id,
                s.title AS ingest_session_title
            FROM chips c
            LEFT JOIN (
                SELECT ti.chip_id, MAX(ct2.id) AS max_tx_id
                FROM transaction_items ti
                JOIN chip_transactions ct2 ON ct2.id = ti.transaction_id
                WHERE ti.chip_id = ?
                GROUP BY ti.chip_id
            ) latest ON latest.chip_id = c.id
            LEFT JOIN chip_transactions ct ON ct.id = latest.max_tx_id
            LEFT JOIN participants p ON p.id = ct.to_participant_id
            LEFT JOIN ingest_sessions s ON s.id = ct.ingest_session_id
            WHERE c.id = ?
        ", [$chipId, $chipId])->getRowArray() ?: null;
    }

    /**
     * Full transaction timeline for a chip, newest first.
     */
    public function getTimeline(int $chipId): array
    {
        return $this->db->query("
            SELECT
                ct.id,
                ct.transaction_type,
                ct.remarks,
                ct.created_at,
                fp.name AS from_name,
                fp.type AS from_type,
                tp.name AS to_name,
                tp.type AS to_type,
                CONCAT(u.first_name, ' ', u.last_name) AS handler_name,
                s.title AS session_title,
                s.id    AS session_id
            FROM transaction_items ti
            JOIN chip_transactions ct ON ct.id = ti.transaction_id
            LEFT JOIN participants fp ON fp.id = ct.from_participant_id
            LEFT JOIN participants tp ON tp.id = ct.to_participant_id
            LEFT JOIN users u         ON u.id  = ct.handled_by
            LEFT JOIN ingest_sessions s ON s.id = ct.ingest_session_id
            WHERE ti.chip_id = ?
            ORDER BY ct.id DESC
        ", [$chipId])->getResultArray();
    }

    /**
     * Chips currently held by a specific participant.
     */
    public function getByCurrentHolder(int $participantId): array
    {
        return $this->db->query("
            SELECT c.*
            FROM chips c
            JOIN (
                SELECT ti.chip_id, MAX(ct2.id) AS max_tx_id
                FROM transaction_items ti
                JOIN chip_transactions ct2 ON ct2.id = ti.transaction_id
                GROUP BY ti.chip_id
            ) latest ON latest.chip_id = c.id
            JOIN chip_transactions ct ON ct.id = latest.max_tx_id
            WHERE ct.to_participant_id = ?
            ORDER BY c.chip_type, c.chip_code
        ", [$participantId])->getResultArray();
    }

    /**
     * Chips linked to an ingest session (via INGEST transactions).
     */
    public function getBySession(int $sessionId): array
    {
        return $this->db->query("
            SELECT DISTINCT c.*, ct.created_at AS ingested_at,
                CONCAT(u.first_name, ' ', u.last_name) AS handler_name,
                fp.name AS from_name,
                fp.type AS from_type
            FROM chips c
            JOIN transaction_items ti ON ti.chip_id = c.id
            JOIN chip_transactions ct ON ct.id = ti.transaction_id
            LEFT JOIN users u         ON u.id  = ct.handled_by
            LEFT JOIN participants fp ON fp.id = ct.from_participant_id
            WHERE ct.ingest_session_id = ?
              AND ct.transaction_type = 'INGEST'
            ORDER BY ct.id DESC
        ", [$sessionId])->getResultArray();
    }

    /**
     * Check if a chip is currently active in an open ingest session.
     * Returns session info or null.
     */
    public function getActiveSession(int $chipId): ?array
    {
        return $this->db->query("
            SELECT s.id, s.title, s.status, ct.created_at AS ingested_at
            FROM transaction_items ti
            JOIN chip_transactions ct ON ct.id = ti.transaction_id
            JOIN ingest_sessions s ON s.id = ct.ingest_session_id
            WHERE ti.chip_id = ?
              AND ct.transaction_type = 'INGEST'
              AND s.status = 'open'
            ORDER BY ct.id DESC
            LIMIT 1
        ", [$chipId])->getRowArray() ?: null;
    }
}
