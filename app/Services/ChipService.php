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
     * Get the current holder of a chip.
     */
    public function getCurrentHolder(int $chipId): ?array
    {
        $chip = $this->chipModel->getWithCurrentHolder($chipId);
        if (!$chip || !$chip['holder_id']) return null;

        return [
            'id'   => $chip['holder_id'],
            'name' => $chip['holder_name'],
            'type' => $chip['holder_type'],
        ];
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
     * Get chip Select2 data: chip_code + type + current holder.
     */
    public function getSelect2Data(?string $search = null, bool $excludeOpenSession = false, ?string $excludeHolderType = null): array
    {
        $all = $this->chipModel->getAllWithCurrentHolder();

        if ($excludeOpenSession) {
            $all = array_filter($all, fn($c) => ($c['ingest_session_status'] ?? null) !== 'open');
        }

        if ($excludeHolderType !== null) {
            $all = array_filter($all, fn($c) => ($c['holder_type'] ?? null) !== $excludeHolderType);
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
            'text' => "[{$c['chip_type']}] {$c['chip_code']}" .
                      ($c['holder_type'] === 'staff'
                          ? ' — ITN Digital'
                          : ($c['holder_type'] === 'librarian'
                              ? ' — Library'
                              : ($c['holder_name'] ? " — {$c['holder_name']}" : ' — Unassigned'))),
        ], $all));
    }
}
