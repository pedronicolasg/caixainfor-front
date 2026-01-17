<?php

namespace App\Services;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class ApiService
{
    private CURLRequest $client;
    private string $baseUrl;
    private ?string $token = null;

    private const INVALID_TOKEN_MESSAGE = 'Invalid or expired token';

    public function __construct()
    {
        $this->client = Services::curlrequest();
        $this->baseUrl = getenv('api.baseURL');
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    private function request(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($this->token) {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->token;
        }

        $headers = array_merge($defaultHeaders, $headers);

        $options = [
            'headers' => $headers,
            'http_errors' => false,
        ];

        if (!empty($data)) {
            if (in_array(strtoupper($method), ['GET', 'DELETE'])) {
                $url .= '?' . http_build_query($data);
            } else {
                $options['json'] = $data;
            }
        }

        try {
            $response = $this->client->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            
            $decoded = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'status' => $statusCode,
                    'message' => 'Erro ao decodificar resposta JSON',
                    'data' => null,
                    'unauthorized' => false,
                ];
            }

            $unauthorized = $this->isInvalidOrExpiredTokenResponse($statusCode, $decoded);

            return [
                'success' => $statusCode >= 200 && $statusCode < 300,
                'status' => $statusCode,
                'message' => $decoded['message'] ?? ($statusCode >= 200 && $statusCode < 300 ? 'Sucesso' : 'Erro'),
                'data' => $decoded,
                'unauthorized' => $unauthorized,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 0,
                'message' => 'Erro de conexão: ' . $e->getMessage(),
                'data' => null,
                'unauthorized' => false,
            ];
        }
    }

    private function isInvalidOrExpiredTokenResponse(int $statusCode, $decoded): bool
    {
        if ($statusCode !== 401 || !is_array($decoded)) {
            return false;
        }

        return ($decoded['message'] ?? null) === self::INVALID_TOKEN_MESSAGE
            && ($decoded['error'] ?? null) === 'Unauthorized'
            && (int) ($decoded['statusCode'] ?? 0) === 401;
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function get(string $endpoint, array $params = []): array
    {
        return $this->request('GET', $endpoint, $params);
    }

    public function patch(string $endpoint, array $data = []): array
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    public function signUp(string $email, string $password): array
    {
        return $this->post('auth/sign-up', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function signIn(string $email, string $password): array
    {
        return $this->post('auth/sign-in', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function getTransactions(array $params = []): array
    {
        return $this->get('transactions', $params);
    }

    public function getTransaction(string $id): array
    {
        return $this->get("transactions/{$id}");
    }

    public function createTransaction(array $data): array
    {
        return $this->post('transactions', $data);
    }

    public function updateTransaction(string $id, array $data): array
    {
        return $this->patch("transactions/{$id}", $data);
    }

    public function deleteTransaction(string $id): array
    {
        return $this->delete("transactions/{$id}");
    }

    public function getSummary(?string $period = null): array
    {
        $params = [];
        if ($period) {
            $params['period'] = $period;
        }
        return $this->get('transactions/summary', $params);
    }
}
