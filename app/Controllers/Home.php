<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $session = \Config\Services::session();

        if ($session->get('access_token')) {
            return redirect()->to('/dashboard');
        }

        return redirect()->to('/auth/login');
    }
}
