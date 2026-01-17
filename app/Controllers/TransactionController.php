<?php

namespace App\Controllers;

use App\Services\ApiService;
use CodeIgniter\HTTP\RedirectResponse;

class TransactionController extends BaseController
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

        $page = $this->request->getGet('page') ?? 1;
        $limit = $this->request->getGet('limit') ?? 10;
        $type = $this->request->getGet('type');
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');
        $orderBy = $this->request->getGet('orderBy') ?? 'created_at';
        $order = $this->request->getGet('order') ?? 'desc';

        $params = [
            'page' => $page,
            'limit' => $limit,
            'orderBy' => $orderBy,
            'order' => $order,
        ];

        if ($type) {
            $params['type'] = $type;
        }
        if ($startDate) {
            $params['startDate'] = $startDate;
        }
        if ($endDate) {
            $params['endDate'] = $endDate;
        }

        $response = $this->apiService->getTransactions($params);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) return $unauthorizedRedirect;

        $data = [
            'transactions' => $response['success'] ? ($response['data']['data'] ?? []) : [],
            'pagination' => $response['success'] ? ($response['data']['pagination'] ?? null) : null,
            'filters' => [
                'type' => $type,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'orderBy' => $orderBy,
                'order' => $order,
            ],
        ];

        return view('transactions/index', $data);
    }

    public function create(): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        return view('transactions/create');
    }

    public function store(): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'amount' => (float) $this->request->getPost('amount'),
        ];

        $date = $this->request->getPost('date');
        if ($date) {
            $data['date'] = $date;
        }

        $response = $this->apiService->createTransaction($data);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) return $unauthorizedRedirect;

        if ($response['success']) {
            $this->session->setFlashdata('success', 'Transação criada com sucesso!');
            return redirect()->to('/transactions');
        }

        $message = is_array($response['message'])
            ? implode(', ', $response['message'])
            : $response['message'];

        $this->session->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    public function edit(string $id): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $response = $this->apiService->getTransaction($id);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) return $unauthorizedRedirect;

        if (!$response['success']) {
            $this->session->setFlashdata('error', $response['message'] ?? 'Transação não encontrada');
            return redirect()->to('/transactions');
        }

        $data = [
            'transaction' => $response['data'],
        ];

        return view('transactions/edit', $data);
    }

    public function update(string $id): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $data = [];

        if ($this->request->getPost('name')) {
            $data['name'] = $this->request->getPost('name');
        }
        if ($this->request->getPost('title')) {
            $data['title'] = $this->request->getPost('title');
        }
        if ($this->request->getPost('description') !== null) {
            $data['description'] = $this->request->getPost('description');
        }
        if ($this->request->getPost('type')) {
            $data['type'] = $this->request->getPost('type');
        }
        if ($this->request->getPost('amount')) {
            $data['amount'] = (float) $this->request->getPost('amount');
        }
        if ($this->request->getPost('date')) {
            $data['date'] = $this->request->getPost('date');
        }

        $response = $this->apiService->updateTransaction($id, $data);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) return $unauthorizedRedirect;

        if ($response['success']) {
            $this->session->setFlashdata('success', 'Transação atualizada com sucesso!');
            return redirect()->to('/transactions');
        }

        $message = is_array($response['message'])
            ? implode(', ', $response['message'])
            : $response['message'];

        $this->session->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    public function delete(string $id): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $response = $this->apiService->deleteTransaction($id);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) return $unauthorizedRedirect;

        if (($response['status'] ?? null) === 204) {
            $this->session->setFlashdata('success', 'Transação deletada com sucesso!');
        } else {
            $this->session->setFlashdata(
                'error',
                $response['message'] ?? 'Erro ao deletar transação'
            );
        }


        return redirect()->to('/transactions');
    }
}
