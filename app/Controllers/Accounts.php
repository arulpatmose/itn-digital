<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FormatsModel;
use App\Models\PlatformsModel;

class Accounts extends BaseController
{
    public function index($date = null)
    {
        if (!auth()->user()->can('accounts.access')) {
            $status = 'error';
            $message = 'You do not have permissions to access that page!';
            return redirect()->back()->with($status, $message);
        } else {
            $data = array();

            $router = service('router');
            $data['controller'] = class_basename($router->controllerName());
            $data['method'] = $router->methodName();

            $platformsModel = new PlatformsModel();
            $formatsModel = new FormatsModel();

            $data['platforms'] = $platformsModel->select('pfm_id as id, name')->findAll();
            $data['formats'] = $formatsModel->select('format_id as id, name')->findAll();

            $data['page_title'] = "Accounts";
            $data['page_description'] = "Manage accounts for TV commercial campaigns.";

            return view('backend/accounts/index', $data);
        }
    }
}
