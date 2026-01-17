<?php

namespace App\Controllers;

use App\Services\ApiService;
use CodeIgniter\HTTP\RedirectResponse;

class DashboardController extends BaseController
{
    private ApiService $apiService;
    private $session;

    public function __construct()
    {
        $this->apiService = new ApiService();
        $this->session = \Config\Services::session();
    }
    
    private function checkAuth(): ?RedirectResponse
    {
        $redirect = $this->ensureValidSessionToken();
        if ($redirect) return $redirect;

        $token = $this->session->get('access_token');
        $this->apiService->setToken($token);
        return null;
    }

    public function index(): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $summaryResponse = $this->apiService->getSummary('month');
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($summaryResponse);
        if ($unauthorizedRedirect) return $unauthorizedRedirect;
        $summary = $summaryResponse['success'] ? $summaryResponse['data'] : null;

        $transactionsResponse = $this->apiService->getTransactions([
            'page' => 1,
            'limit' => 5,
            'orderBy' => 'created_at',
            'order' => 'desc',
        ]);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($transactionsResponse);
        if ($unauthorizedRedirect) return $unauthorizedRedirect;
        $transactions = $transactionsResponse['success'] ? ($transactionsResponse['data']['data'] ?? []) : [];

        $data = [
            'summary' => $summary,
            'recentTransactions' => $transactions,
            'user_email' => $this->session->get('user_email'),
        ];

        return view('dashboard/index', $data);
    }
}
