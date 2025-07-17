<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use CodeIgniter\HTTP\ResponseInterface;

class Settings extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        // Authorization using Shield
        if (!auth()->user()->can('settings.access')) {
            return redirect()->back()->with('error', 'You are not allowed to view this page!');
        }

        $data = [
            'page_title' => "Settings",
            'page_description' => "Manage Settings",
        ];

        return view('backend/settings/index', $data);
    }

    public function system()
    {
        // Authorization using Shield
        if (!auth()->user()->can('settings.access')) {
            return redirect()->back()->with('error', 'You are not allowed to view this page!');
        }

        $data = [
            'page_title'       => "System Settings",
            'page_description' => "Manage System Settings",
            'system_settings' => get_settings('system_settings', true),
            'setting_group'    => 'system'
        ];

        return view('backend/settings/forms/system', $data);
    }

    public function updateSettings()
    {
        // Authorization using Shield
        if (!auth()->user()->can('settings.update')) {
            return redirect()->back()->with('error', 'You are not allowed to view this page!');
        }

        $postData = $this->request->getPost();
        $group = $postData['group'] ?? null;

        // Update grouped settings in DB
        $this->settingModel->updateGroupedSettings($postData);

        if ($group) {
            return redirect()->to('settings/' . $group)->with('success', 'Settings updated successfully.');
        }

        return redirect()->to('settings')->with('success', 'Settings updated successfully.');
    }
}
