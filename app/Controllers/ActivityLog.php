<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;

class ActivityLog extends BaseController
{
    protected $activityLogModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
    }

    public function index()
    {
        if (!auth()->user()->can('admin.settings')) {
            return redirect()->back()->with('error', 'You are not allowed to view this page!');
        }

        $data = [
            'page_title'       => 'Activity Log',
            'page_description' => 'A record of all actions performed by users in the system.',
        ];

        return view('backend/activity_log/index', $data);
    }
}
