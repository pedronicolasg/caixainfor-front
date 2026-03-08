<?php

namespace App\Controllers;

use App\Services\ApiService;
use App\Helpers\TransactionNormalizer;
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
        if ($redirect)
            return $redirect;

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
        if ($unauthorizedRedirect)
            return $unauthorizedRedirect;
        $summary = $summaryResponse['success'] ? $summaryResponse['data'] : null;

        $transactionsResponse = $this->apiService->getTransactions([
            'page'    => 1,
            'limit'   => 10,
            'orderBy' => 'created_at',
            'order'   => 'desc',
        ]);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($transactionsResponse);
        if ($unauthorizedRedirect)
            return $unauthorizedRedirect;
        $rawTransactions = $transactionsResponse['success'] ? ($transactionsResponse['data']['data'] ?? []) : [];

        $vaultsResponse = $this->apiService->getVaults();
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($vaultsResponse);
        if ($unauthorizedRedirect)
            return $unauthorizedRedirect;
        $vaults = $vaultsResponse['success'] ? ($vaultsResponse['data'] ?? []) : [];
        $vaultsById = [];
        foreach ($vaults as $v) {
            $vaultsById[$v['id']] = $v;
        }

        $normalizedTransactions = TransactionNormalizer::normalize($rawTransactions, $vaultsById);
        $recentTransactions = array_slice($normalizedTransactions, 0, 5);

        $totalVaultsBalance = 0;
        foreach ($vaults as $vault) {
            $totalVaultsBalance += (float) ($vault['balance'] ?? 0);
        }

        $totalBalance = (float) ($summary['balance'] ?? 0);
        $generalBalance = $totalBalance - $totalVaultsBalance;

        usort($vaults, function (array $a, array $b) {
            $aBalance = (float) ($a['balance'] ?? 0);
            $bBalance = (float) ($b['balance'] ?? 0);
            return $bBalance <=> $aBalance;
        });
        $topVaults = array_slice($vaults, 0, 3);

        $data = [
            'summary'          => $summary,
            'recentTransactions' => $recentTransactions,
            'user_email'       => $this->session->get('user_email'),
            'vaults'           => $vaults,
            'topVaults'        => $topVaults,
            'totalVaultsBalance' => $totalVaultsBalance,
            'generalBalance'   => $generalBalance,
        ];

        return view('dashboard/index', $data);
    }
}
