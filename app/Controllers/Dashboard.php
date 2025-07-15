<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Dashboard";
        $data['page_description'] = "Welcome to ITN Digital Portal! Everything you need, all in one place — simplified and supercharged.";

        return view('backend/dashboard', $data);
    }
}
