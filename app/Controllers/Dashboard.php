<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        helper('dashboard');

        $data['pageTitle'] = "Dashboard";
        $data['pageDescription'] = get_greeting('<strong>' . esc(auth()->user()?->first_name ?? 'Guest') . '</strong>');
        $data['totals'] = get_total_counts();

        return view('backend/dashboard', $data);
    }
}
