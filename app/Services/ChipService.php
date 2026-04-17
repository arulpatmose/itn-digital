<?php

namespace App\Services;

use App\Models\ChipModel;

class ChipService
{
    protected ChipModel $chipModel;

    public function __construct()
    {
        $this->chipModel = new ChipModel();
    }

    /**
     * Get the current location/holder of a chip.
     */
    public function getCurrentHolder(int $chipId): ?array
    {
        $chip = $this->chipModel->getWithCurrentHolder($chipId);
        if (!$chip) return null;

        return match($chip['to_location'] ?? null) {
            'producer' => $chip['holder_id'] ? ['id' => $chip['holder_id'], 'name' => $chip['holder_name'], 'type' => 'producer'] : null,
            'digital_unit' => ['id' => null, 'name' => 'ITN Digital', 'type' => 'digital_unit'],
            'library'  => ['id' => null, 'name' => 'Library',     'type' => 'library'],
            'ingest'   => ['id' => null, 'name' => 'At Ingest',   'type' => 'ingest'],
            default    => null,
        };
    }

    /**
     * Validate chip IDs — ensure they all exist.
     */
    public function validateChipIds(array $chipIds): array
    {
        $errors = [];
        foreach ($chipIds as $id) {
            if (!$this->chipModel->find((int) $id)) {
                $errors[] = "Chip ID {$id} does not exist.";
            }
        }
        return $errors;
    }

    /**
     * Get chip Select2 data: chip_code + type + current location.
     * $excludeLocation:  skip chips whose to_location matches (e.g. 'library').
     * $excludeSessionId: skip chips already in this ingest session.
     */
    public function getSelect2Data(?string $search = null, bool $excludeOpenSession = false, ?string $excludeLocation = null, ?int $excludeSessionId = null): array
    {
        $all = $this->chipModel->getAllWithCurrentHolder();

        if ($excludeOpenSession) {
            $all = array_filter($all, fn($c) => ($c['ingest_session_status'] ?? null) !== 'open');
        }

        if ($excludeLocation !== null) {
            $all = array_filter($all, fn($c) => ($c['to_location'] ?? null) !== $excludeLocation);
        }

        if ($excludeSessionId !== null) {
            $all = array_filter($all, fn($c) => ($c['ingest_session_id'] ?? null) != $excludeSessionId);
        }

        if ($search) {
            $search = strtolower($search);
            $all = array_filter($all, fn($c) =>
                str_contains(strtolower($c['chip_code']), $search) ||
                str_contains(strtolower($c['chip_type']), $search)
            );
        }

        return array_values(array_map(fn($c) => [
            'id'   => $c['id'],
            'text' => "[{$c['chip_type']}] {$c['chip_code']}" . match($c['to_location'] ?? null) {
                'digital_unit' => ' — ITN Digital',
                'library'  => ' — Library',
                'ingest'   => ' — At Ingest',
                'producer' => $c['holder_name'] ? " — {$c['holder_name']}" : ' — Unassigned',
                default    => ' — Unassigned',
            },
        ], $all));
    }
}
