<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        $data['page_title'] = "Dashboard";
        $data['page_description'] = get_greeting('<strong>' . esc(auth()->user()?->first_name ?? 'Guest') . '</strong>');

        return view('backend/dashboard', $data);
    }
}
