<?php

namespace App\Controllers;

use App\Services\ApiService;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    private ApiService $apiService;
    private $session;

    public function __construct()
    {
        $this->apiService = new ApiService();
        $this->session = \Config\Services::session();
    }

    public function login(): string|RedirectResponse
    {
        $token = $this->session->get('access_token');
        if ($token) {
            if ($this->isJwtExpired($token)) {
                $this->session->destroy();
            } else {
                return redirect()->to('/dashboard');
            }
        }

        return view('auth/login');
    }

    public function doLogin(): RedirectResponse
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            $this->session->setFlashdata('error', 'Email e senha são obrigatórios');
            return redirect()->back()->withInput();
        }

        $response = $this->apiService->signIn($email, $password);

        if ($response['success']) {
            $data = $response['data'];
            $token = $data['accessToken'] ?? $data['session']['access_token'] ?? null;
            $user = $data['user'] ?? [];

            if ($token) {
                $this->apiService->setToken($token);
                $this->session->set([
                    'access_token' => $token,
                    'user_id' => $user['id'] ?? null,
                    'user_email' => $user['email'] ?? $email,
                ]);

                return redirect()->to('/dashboard');
            }
        }

        $this->session->setFlashdata('error', $response['message'] ?? 'Erro ao fazer login');
        return redirect()->back()->withInput();
    }

    public function signup(): string|RedirectResponse
    {
        $token = $this->session->get('access_token');
        if ($token) {
            if ($this->isJwtExpired($token)) {
                $this->session->destroy();
            } else {
                return redirect()->to('/dashboard');
            }
        }

        return view('auth/signup');
    }

    public function doSignup(): RedirectResponse
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (empty($email) || empty($password)) {
            $this->session->setFlashdata('error', 'Email e senha são obrigatórios');
            return redirect()->back()->withInput();
        }

        if ($password !== $confirmPassword) {
            $this->session->setFlashdata('error', 'As senhas não coincidem');
            return redirect()->back()->withInput();
        }

        if (strlen($password) < 6) {
            $this->session->setFlashdata('error', 'A senha deve ter no mínimo 6 caracteres');
            return redirect()->back()->withInput();
        }

        $response = $this->apiService->signUp($email, $password);

        if ($response['success']) {
            $data = $response['data'];
            $token = $data['session']['access_token'] ?? null;
            $user = $data['user'] ?? [];

            if ($token) {
                $this->apiService->setToken($token);
                $this->session->set([
                    'access_token' => $token,
                    'user_id' => $user['id'] ?? null,
                    'user_email' => $user['email'] ?? $email,
                ]);

                $this->session->setFlashdata('success', 'Conta criada com sucesso!');
                return redirect()->to('/dashboard');
            }
        }

        $this->session->setFlashdata('error', $response['message'] ?? 'Erro ao criar conta');
        return redirect()->back()->withInput();
    }

    public function logout(): RedirectResponse
    {
        $this->session->destroy();
        return redirect()->to('/auth/login');
    }
}
