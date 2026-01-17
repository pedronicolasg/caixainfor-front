<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->helpers = ['form', 'url', 'gravatar'];

        parent::initController($request, $response, $logger);

    }

    protected function ensureValidSessionToken(): ?RedirectResponse
    {
        $session = session();
        $token = $session->get('access_token');

        if (!$token) {
            return redirect()->to('/auth/login');
        }

        if ($this->isJwtExpired($token)) {
            $session->destroy();
            $session->setFlashdata('error', 'Sessão expirada. Faça login novamente.');
            return redirect()->to('/auth/login');
        }

        return null;
    }

    protected function logoutIfUnauthorizedApiResponse(array $apiResponse): ?RedirectResponse
    {
        if (!($apiResponse['unauthorized'] ?? false)) {
            return null;
        }

        $session = session();
        $session->destroy();
        $session->setFlashdata('error', 'Sessão expirada. Faça login novamente.');
        return redirect()->to('/auth/login');
    }

    protected function isJwtExpired(string $token): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        $payloadJson = $this->base64UrlDecode($parts[1]);
        if ($payloadJson === null) {
            return false;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return false;
        }

        $exp = $payload['exp'] ?? null;
        if (!is_numeric($exp)) {
            return false;
        }

        return (int) $exp <= time();
    }

    protected function base64UrlDecode(string $input): ?string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($input, '-_', '+/'), true);
        return $decoded === false ? null : $decoded;
    }
}
