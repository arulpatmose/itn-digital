<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ChipModel;
use App\Models\ParticipantModel;

class ChipReports extends BaseController
{
    protected ChipModel        $chipModel;
    protected ParticipantModel $participantModel;

    public function __construct()
    {
        $this->chipModel        = new ChipModel();
        $this->participantModel = new ParticipantModel();
    }

    /**
     * Full transaction timeline for a chip — /reports/chip-history/{id}
     */
    public function chipHistory(int $chipId)
    {
        if (!auth()->user()->can('ingest.reports')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $chip = $this->chipModel->getWithCurrentHolder($chipId);
        if (!$chip) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('backend/reports/chip_history', [
            'page_title'       => "Chip History — {$chip['chip_code']}",
            'page_description' => 'Complete transaction timeline for this chip.',
            'chip'             => $chip,
            'timeline'         => $this->chipModel->getTimeline($chipId),
        ]);
    }

    /**
     * Overview: all chips with current holder + last activity.
     */
    public function overview()
    {
        if (!auth()->user()->can('ingest.reports')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        return view('backend/reports/overview', [
            'page_title'       => 'Chip Report',
            'page_description' => 'Overview of all chips and their current locations.',
            'chips'            => $this->chipModel->getAllWithCurrentHolder(),
            'participants'     => $this->participantModel->getActive(),
        ]);
    }
}
