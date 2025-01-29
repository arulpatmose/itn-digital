<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Dashboard";
        $data['page_description'] = "Welcome to ITN Digital Portal! Explore a world of information, resources, and tools at your fingertips.";

        return view('backend/dashboard', $data);
    }
}
