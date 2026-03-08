<?php

namespace App\Controllers;

use App\Helpers\TransactionNormalizer;
use App\Services\ApiService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Mpdf\Mpdf;

class VaultController extends BaseController
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
        if ($redirect) {
            return $redirect;
        }

        $token = $this->session->get('access_token');
        $this->apiService->setToken($token);
        return null;
    }

    private function saveVaultImageAndGetUrl(): ?string
    {
        $file = $this->request->getFile('image');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mimeType = $file->getMimeType();
        if (!isset($allowedMimeTypes[$mimeType])) {
            return null;
        }

        $uploadDir = rtrim(FCPATH . 'assets/images/vaults', DIRECTORY_SEPARATOR);

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            return null;
        }

        $extension = $allowedMimeTypes[$mimeType];
        $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;

        try {
            $file->move($uploadDir, $filename);
        } catch (\Throwable $e) {
            return null;
        }

        if (!is_file($uploadDir . DIRECTORY_SEPARATOR . $filename)) {
            return null;
        }

        $imageUrl = base_url('assets/images/vaults/' . $filename);

        return filter_var($imageUrl, FILTER_VALIDATE_URL) ? $imageUrl : null;
    }

    public function index(): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $summaryResponse = $this->apiService->getSummary(null);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($summaryResponse);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        $summary = $summaryResponse['success'] ? $summaryResponse['data'] : null;

        $vaultsResponse = $this->apiService->getVaults();
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($vaultsResponse);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        $vaults = $vaultsResponse['success'] ? ($vaultsResponse['data'] ?? []) : [];

        $totalVaultsBalance = 0;
        $today = new \DateTimeImmutable('today');

        foreach ($vaults as &$vault) {
            $totalVaultsBalance += (float) ($vault['balance'] ?? 0);

            $vault['daysRemaining'] = null;
            $vault['deadlineSeverity'] = null;

            if (!empty($vault['until'])) {
                try {
                    $until = new \DateTimeImmutable($vault['until']);
                    $diff = $today->diff($until);
                    $days = (int) $diff->format('%r%a');
                    $vault['daysRemaining'] = $days;

                    if ($days < 0) {
                        $vault['deadlineSeverity'] = 'past';
                    } elseif ($days <= 3) {
                        $vault['deadlineSeverity'] = 'danger';
                    } elseif ($days <= 7) {
                        $vault['deadlineSeverity'] = 'warning';
                    } else {
                        $vault['deadlineSeverity'] = 'ok';
                    }
                } catch (\Exception $e) {
                    $vault['daysRemaining'] = null;
                    $vault['deadlineSeverity'] = null;
                }
            }
        }
        unset($vault);

        usort($vaults, function (array $a, array $b) {
            $aHas = !empty($a['until']);
            $bHas = !empty($b['until']);

            if ($aHas && !$bHas) {
                return -1;
            }
            if (!$aHas && $bHas) {
                return 1;
            }
            if ($aHas && $bHas) {
                return strcmp($a['until'], $b['until']);
            }

            return 0;
        });

        $totalBalance = (float) ($summary['balance'] ?? 0);
        $generalBalance = $totalBalance - $totalVaultsBalance;

        $data = [
            'summary' => $summary,
            'vaults' => $vaults,
            'generalBalance' => $generalBalance,
            'totalVaultsBalance' => $totalVaultsBalance,
        ];

        return view('vaults/index', $data);
    }

    public function create(): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        return view('vaults/create');
    }

    public function store(): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'goal' => $this->request->getPost('goal') ? (float) $this->request->getPost('goal') : null,
            'until' => $this->request->getPost('until') ?: null,
            'description' => $this->request->getPost('description') ?: null,
        ];

        $imageUrl = $this->saveVaultImageAndGetUrl();
        if ($imageUrl !== null) {
            $data['image'] = $imageUrl;
        }

        $response = $this->apiService->createVault($data);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if ($response['success']) {
            $this->session->setFlashdata('success', 'Caixinha criada com sucesso!');
            return redirect()->to('/vaults');
        }

        $message = is_array($response['message'])
            ? implode(', ', $response['message'])
            : $response['message'];

        $this->session->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    public function showDepositForm(int $id): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $response = $this->apiService->getVault($id);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if (!$response['success']) {
            $this->session->setFlashdata('error', $response['message'] ?? 'Vault não encontrada');
            return redirect()->to('/vaults');
        }

        return view('vaults/deposit', ['vault' => $response['data']]);
    }

    public function deposit(int $id): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $amount = (float) $this->request->getPost('amount');
        $response = $this->apiService->depositToVault($id, $amount);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if ($response['success']) {
            $this->session->setFlashdata('success', 'Depósito realizado com sucesso!');
            return redirect()->to('/vaults');
        }

        $message = is_array($response['message'])
            ? implode(', ', $response['message'])
            : $response['message'];

        $this->session->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    public function showWithdrawForm(int $id): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $response = $this->apiService->getVault($id);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if (!$response['success']) {
            $this->session->setFlashdata('error', $response['message'] ?? 'Vault não encontrada');
            return redirect()->to('/vaults');
        }

        return view('vaults/withdraw', ['vault' => $response['data']]);
    }

    public function withdraw(int $id): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $amount = (float) $this->request->getPost('amount');
        $response = $this->apiService->withdrawFromVault($id, $amount);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if ($response['success']) {
            $this->session->setFlashdata('success', 'Resgate realizado com sucesso!');
            return redirect()->to('/vaults');
        }

        $message = is_array($response['message'])
            ? implode(', ', $response['message'])
            : $response['message'];

        $this->session->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    public function showTransferForm(): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $vaultsResponse = $this->apiService->getVaults();
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($vaultsResponse);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        $vaults = $vaultsResponse['success'] ? ($vaultsResponse['data'] ?? []) : [];

        return view('vaults/transfer', ['vaults' => $vaults]);
    }

    public function transfer(): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $fromVaultId = (int) $this->request->getPost('from_vault_id');
        $toVaultId = (int) $this->request->getPost('to_vault_id');
        $amount = (float) $this->request->getPost('amount');

        $response = $this->apiService->transferBetweenVaults($fromVaultId, $toVaultId, $amount);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if ($response['success']) {
            $this->session->setFlashdata('success', 'Transferência entre caixinhas realizada com sucesso!');
            return redirect()->to('/vaults');
        }

        $message = is_array($response['message'])
            ? implode(', ', $response['message'])
            : $response['message'];

        $this->session->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    public function showDeleteForm(int $id): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $response = $this->apiService->getVault($id);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if (!$response['success']) {
            $this->session->setFlashdata('error', $response['message'] ?? 'Vault não encontrada');
            return redirect()->to('/vaults');
        }

        return view('vaults/delete', ['vault' => $response['data']]);
    }

    public function edit(int $id): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $response = $this->apiService->getVault($id);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        if (!$response['success']) {
            $this->session->setFlashdata('error', $response['message'] ?? 'Caixinha não encontrada');
            return redirect()->to('/vaults');
        }

        return view('vaults/edit', ['vault' => $response['data']]);
    }

    public function update(int $id): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description') ?: null,
        ];

        $imageUrl = $this->saveVaultImageAndGetUrl();
        if ($imageUrl !== null) {
            $data['image'] = $imageUrl;
        } elseif ($this->request->getPost('remove_image') === '1') {
            $data['image'] = null;
        }

        $response = $this->apiService->updateVault($id, $data);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if ($response['success']) {
            $this->session->setFlashdata('success', 'Caixinha atualizada com sucesso!');
            return redirect()->to('/vaults/' . $id);
        }

        $message = is_array($response['message'])
            ? implode(', ', $response['message'])
            : $response['message'];

        $this->session->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    public function show(int $id): string|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $vaultResponse = $this->apiService->getVault($id);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($vaultResponse);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        if (!$vaultResponse['success']) {
            $this->session->setFlashdata('error', $vaultResponse['message'] ?? 'Caixinha não encontrada');
            return redirect()->to('/vaults');
        }

        $vault = $vaultResponse['data'];

        $vaultsResponse = $this->apiService->getVaults();
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($vaultsResponse);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        $vaultsList = $vaultsResponse['success'] ? ($vaultsResponse['data'] ?? []) : [];
        $vaultsById = [];
        foreach ($vaultsList as $v) {
            $vaultsById[(int) $v['id']] = $v;
        }

        $transactionsResponse = $this->apiService->getTransactions([
            'page' => 1,
            'limit' => 100,
            'orderBy' => 'created_at',
            'order' => 'desc',
            'vault_id' => $id,
        ]);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($transactionsResponse);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        $rawTransactions = $transactionsResponse['success'] ? ($transactionsResponse['data']['data'] ?? []) : [];
        $transactions = TransactionNormalizer::normalize($rawTransactions, $vaultsById);

        $today = new \DateTimeImmutable('today');
        $daysRemaining = null;
        $deadlineSeverity = null;
        if (!empty($vault['until'])) {
            try {
                $until = new \DateTimeImmutable($vault['until']);
                $diff = $today->diff($until);
                $daysRemaining = (int) $diff->format('%r%a');
                if ($daysRemaining < 0) {
                    $deadlineSeverity = 'past';
                } elseif ($daysRemaining <= 3) {
                    $deadlineSeverity = 'danger';
                } elseif ($daysRemaining <= 7) {
                    $deadlineSeverity = 'warning';
                } else {
                    $deadlineSeverity = 'ok';
                }
            } catch (\Exception $e) {
                $daysRemaining = null;
                $deadlineSeverity = null;
            }
        }

        $data = [
            'vault' => $vault,
            'transactions' => $transactions,
            'vaultsById' => $vaultsById,
            'daysRemaining' => $daysRemaining,
            'deadlineSeverity' => $deadlineSeverity,
        ];

        return view('vaults/show', $data);
    }

    public function destroy(int $id): RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $mode = $this->request->getPost('mode') ?: 'move_to_general';
        $response = $this->apiService->deleteVault($id, $mode);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }

        if ($response['success']) {
            $this->session->setFlashdata('success', 'Caixinha excluída com sucesso!');
            return redirect()->to('/vaults');
        }

        $message = is_array($response['message'])
            ? implode(', ', $response['message'])
            : $response['message'];

        $this->session->setFlashdata('error', $message);
        return redirect()->back()->withInput();
    }

    public function exportPdf(int $id): ResponseInterface|RedirectResponse
    {
        $redirect = $this->checkAuth();
        if ($redirect) {
            return $redirect;
        }

        $vaultResponse = $this->apiService->getVault($id);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($vaultResponse);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        if (!$vaultResponse['success']) {
            $this->session->setFlashdata('error', $vaultResponse['message'] ?? 'Caixinha não encontrada');
            return redirect()->to('/vaults');
        }
        $vault = $vaultResponse['data'];

        $transactionsResponse = $this->apiService->getTransactions([
            'page' => 1,
            'limit' => 5000,
            'orderBy' => 'created_at',
            'order' => 'desc',
            'vault_id' => $id,
        ]);
        $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($transactionsResponse);
        if ($unauthorizedRedirect) {
            return $unauthorizedRedirect;
        }
        $transactions = $transactionsResponse['success'] ? ($transactionsResponse['data']['data'] ?? []) : [];

        $totalIn = 0;
        $totalOut = 0;
        foreach ($transactions as $t) {
            $amt = (float) ($t['amount'] ?? 0);
            $type = $t['type'] ?? '';
            if (in_array($type, ['income', 'transfer_in'], true)) {
                $totalIn += $amt;
            } elseif (in_array($type, ['outcome', 'transfer_out'], true)) {
                $totalOut += $amt;
            }
        }
        $balance = $totalIn - $totalOut;

        $today = new \DateTimeImmutable('today');
        $daysRemaining = null;
        if (!empty($vault['until'])) {
            try {
                $until = new \DateTimeImmutable($vault['until']);
                $diff = $today->diff($until);
                $daysRemaining = (int) $diff->format('%r%a');
            } catch (\Exception $e) {
                $daysRemaining = null;
            }
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
          <meta charset="UTF-8">
          <style>
            body {
              font-family: DejaVu Sans, sans-serif;
              font-size: 10pt;
              color: #0f172a;
              margin: 0;
              padding: 0;
            }
            .page { padding: 26px 24px 18px 24px; }
            .header {
              border-bottom: 2px solid #e2e8f0;
              padding-bottom: 14px;
              margin-bottom: 18px;
            }
            .brand {
              font-size: 8.5pt;
              color: #475569;
              letter-spacing: 0.2px;
              margin: 0 0 6px 0;
            }
            h1 {
              color: #0369a1;
              font-size: 18pt;
              margin: 0;
              line-height: 1.1;
            }
            .subtitle {
              color: #64748b;
              font-size: 9pt;
              margin: 6px 0 0 0;
            }
            .summary {
              background: #f8fafc;
              border: 1px solid #e2e8f0;
              border-radius: 10px;
              padding: 14px;
              margin-bottom: 18px;
            }
            .summary-title {
              font-size: 11pt;
              color: #0f172a;
              margin: 0 0 10px 0;
            }
            .summary-grid {
              width: 100%;
              border-collapse: collapse;
            }
            .summary-grid td {
              padding: 6px 8px;
              border-bottom: 1px dashed #e2e8f0;
              font-size: 9.5pt;
            }
            .summary-grid tr:last-child td { border-bottom: 0; }
            .k { color: #334155; width: 45%; }
            .v { color: #0f172a; width: 55%; text-align: right; font-weight: 700; white-space: nowrap; }
            .amount-in { color: #16a34a; font-weight: 700; }
            .amount-out { color: #dc2626; font-weight: 700; }
            .balance { font-weight: 800; }
            .balance.pos { color: #0f766e; }
            .balance.neg { color: #b91c1c; }
            table.report {
              width: 100%;
              border-collapse: collapse;
              margin-bottom: 16px;
              border: 1px solid #cbd5e1;
            }
            table.report thead th {
              background: #0b74a8;
              color: #ffffff;
              padding: 10px 8px;
              text-align: left;
              font-size: 9pt;
              border: 1px solid #075b83;
            }
            table.report tbody td {
              padding: 9px 8px;
              font-size: 9.5pt;
              color: #0f172a;
              border: 1px solid #cbd5e1;
            }
            table.report tbody tr:nth-child(even) { background: #f8fafc; }
            .amount-cell {
              text-align: right;
              white-space: nowrap;
              font-variant-numeric: tabular-nums;
            }
            .date-cell { white-space: nowrap; }
            .muted { color: #64748b; }
            .footer {
              margin-top: 10px;
              padding-top: 12px;
              border-top: 1px solid #e2e8f0;
              font-size: 8pt;
              color: #64748b;
              text-align: center;
              line-height: 1.35;
            }
            .footer strong { color: #334155; }
          </style>
        </head>
        <body>
          <div class="page">
            <div class="header">
              <p class="brand"><strong>INFOR-3</strong> • Caixa 2026</p>
              <h1>Relatório da Caixinha</h1>
              <p class="subtitle">Caixinha: ' . htmlspecialchars($vault['name'] ?? '') . ' • Gerado em ' . date('d/m/Y H:i') . '</p>
            </div>

            <div class="summary">
              <p class="summary-title"><strong>Resumo da caixinha</strong></p>

              <table class="summary-grid">
                <tr>
                  <td class="k">Saldo atual</td>
                  <td class="v balance ' . (($vault['balance'] ?? 0) < 0 ? 'neg' : 'pos') . '">R$ ' . number_format((float) ($vault['balance'] ?? 0), 2, ',', '.') . '</td>
                </tr>
                <tr>
                  <td class="k">Total de créditos (entradas + transferências)</td>
                  <td class="v amount-in">R$ ' . number_format($totalIn, 2, ',', '.') . '</td>
                </tr>
                <tr>
                  <td class="k">Total de débitos (saídas + transferências)</td>
                  <td class="v amount-out">R$ ' . number_format($totalOut, 2, ',', '.') . '</td>
                </tr>';

        if (!empty($vault['goal'])) {
            $html .= '
                <tr>
                  <td class="k">Meta definida</td>
                  <td class="v">R$ ' . number_format((float) $vault['goal'], 2, ',', '.') . '</td>
                </tr>';
        }

        if ($daysRemaining !== null) {
            $label = $daysRemaining < 0
                ? 'Prazo encerrado'
                : ($daysRemaining === 0 ? 'Encerra hoje' : "Faltam {$daysRemaining} dia" . ($daysRemaining > 1 ? 's' : ''));
            $html .= '
                <tr>
                  <td class="k">Prazo</td>
                  <td class="v">' . htmlspecialchars($label) . '</td>
                </tr>';
        }

        $html .= '
              </table>
            </div>

            <table class="report">
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Título</th>
                  <th>Tipo</th>
                  <th style="text-align:right;">Valor</th>
                </tr>
              </thead>
              <tbody>';

        foreach ($transactions as $t) {
            $type = $t['type'] ?? '';
            if ($type === 'income') {
                $typeLabel = 'Entrada externa';
            } elseif ($type === 'outcome') {
                $typeLabel = 'Saída externa';
            } elseif ($type === 'transfer_in') {
                $typeLabel = 'Crédito interno';
            } elseif ($type === 'transfer_out') {
                $typeLabel = 'Débito interno';
            } else {
                $typeLabel = ucfirst((string) $type);
            }

            $amount = (float) ($t['amount'] ?? 0);
            $dateStr = !empty($t['date']) ? date('d/m/Y', strtotime($t['date'])) : '—';

            $html .= '<tr>
            <td class="date-cell muted">' . $dateStr . '</td>
            <td>' . htmlspecialchars($t['title'] ?? '') . '</td>
            <td>' . htmlspecialchars($typeLabel) . '</td>
            <td class="amount-cell">R$ ' . number_format($amount, 2, ',', '.') . '</td>
          </tr>';
        }

        $html .= '
              </tbody>
            </table>

            <div class="footer">
              <div>
                <strong>Documento gerado pelo sistema</strong> • Caixa 2026 — Informática 3 (2024 - 2026)
              </div>
              <div class="muted">
                Uso interno • Valores em BRL
              </div>
            </div>
          </div>
        </body>
        </html>';

        $mpdf->SetTitle('Relatório da Caixinha - ' . ($vault['name'] ?? '') . ' - ' . date('d/m/Y H:i'));
        $mpdf->SetAuthor('3º Ano do Ensino Médio Integrado ao Curso Técnico em Informática - Turma de 2024-2026');
        $mpdf->SetCreator('Sistema de Gerenciamento de Caixa 2026 - Informática 3');

        $mpdf->WriteHTML($html);
        $filename = 'relatorio_caixinha_' . $id . '_' . date('Y-m-d_His') . '.pdf';
        $output = $mpdf->Output('', 'S');

        return $this->response->download($filename, $output)->setHeader('Content-Type', 'application/pdf');
    }
}

