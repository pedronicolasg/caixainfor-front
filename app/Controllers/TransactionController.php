<?php

namespace App\Controllers;

use App\Services\ApiService;
use App\Helpers\TransactionNormalizer;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Mpdf\Mpdf;

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

    if ($type && in_array($type, ['income', 'outcome'], true)) {
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
    if ($unauthorizedRedirect)
      return $unauthorizedRedirect;

    $vaultsResponse = $this->apiService->getVaults();
    $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($vaultsResponse);
    if ($unauthorizedRedirect)
      return $unauthorizedRedirect;
    $vaults = $vaultsResponse['success'] ? ($vaultsResponse['data'] ?? []) : [];
    $vaultsById = [];
    foreach ($vaults as $v) {
      $vaultsById[$v['id']] = $v;
    }

    $rawTransactions = $response['success'] ? ($response['data']['data'] ?? []) : [];
    $normalized = TransactionNormalizer::normalize($rawTransactions, $vaultsById, $type ?: null);

    $data = [
      'transactions' => $normalized,
      'pagination' => $response['success'] ? ($response['data']['pagination'] ?? null) : null,
      'filters' => [
        'type' => $type,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'orderBy' => $orderBy,
        'order' => $order,
      ],
      'vaultsById' => $vaultsById,
    ];

    return view('transactions/index', $data);
  }

  public function create(): string|RedirectResponse
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

    return view('transactions/create', ['vaults' => $vaults]);
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

    $vaultId = $this->request->getPost('vault_id');
    if ($vaultId !== null && $vaultId !== '') {
      $data['vault_id'] = (int) $vaultId;
    }

    $response = $this->apiService->createTransaction($data);
    $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
    if ($unauthorizedRedirect)
      return $unauthorizedRedirect;

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
    if ($unauthorizedRedirect)
      return $unauthorizedRedirect;

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
    if ($unauthorizedRedirect)
      return $unauthorizedRedirect;

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
    if ($unauthorizedRedirect)
      return $unauthorizedRedirect;

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

  private function getTransactionsForExport(): array
  {
    $redirect = $this->checkAuth();
    if ($redirect) {
      return [[], $redirect];
    }

    $type = $this->request->getGet('type');
    $startDate = $this->request->getGet('startDate');
    $endDate = $this->request->getGet('endDate');
    $orderBy = $this->request->getGet('orderBy') ?? 'created_at';
    $order = $this->request->getGet('order') ?? 'desc';

    $params = [
      'page' => 1,
      'limit' => 5000,
      'orderBy' => $orderBy,
      'order' => $order,
    ];
    if ($type && in_array($type, ['income', 'outcome'], true)) {
      $params['type'] = $type;
    }
    if ($startDate)
      $params['startDate'] = $startDate;
    if ($endDate)
      $params['endDate'] = $endDate;

    $response = $this->apiService->getTransactions($params);
    $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($response);
    if ($unauthorizedRedirect) {
      return [[], $unauthorizedRedirect];
    }

    $vaultsResponse = $this->apiService->getVaults();
    $unauthorizedRedirect = $this->logoutIfUnauthorizedApiResponse($vaultsResponse);
    if ($unauthorizedRedirect) {
      return [[], $unauthorizedRedirect];
    }
    $vaults = $vaultsResponse['success'] ? ($vaultsResponse['data'] ?? []) : [];
    $vaultsById = [];
    foreach ($vaults as $v) {
      $vaultsById[$v['id']] = $v;
    }

    $raw = $response['success'] ? ($response['data']['data'] ?? []) : [];
    $transactions = TransactionNormalizer::normalize($raw, $vaultsById, $type ?: null);

    return [$transactions, null];
  }

  public function exportExcel(): ResponseInterface|RedirectResponse
  {
    [$transactions, $redirect] = $this->getTransactionsForExport();
    if ($redirect) {
      return $redirect;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Transações');

    $headers = ['Nome', 'Título', 'Descrição', 'Tipo', 'Valor (R$)', 'Data'];
    $col = 'A';
    foreach ($headers as $h) {
      $sheet->setCellValue($col . '1', $h);
      $col++;
    }
    $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E3F2FD');
    $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:F1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    $row = 2;
    foreach ($transactions as $t) {
      $uiType = $t['ui_type'] ?? $t['type'] ?? '';

      if ($uiType === 'deposit') {
        $typeLabel = 'Depósito';
      } elseif ($uiType === 'withdraw') {
        $typeLabel = 'Resgate';
      } elseif ($uiType === 'transfer') {
        $typeLabel = 'Transferência';
      } elseif ($uiType === 'income') {
        $typeLabel = 'Entrada';
      } elseif ($uiType === 'outcome') {
        $typeLabel = 'Saída';
      } else {
        $typeLabel = ucfirst((string) $uiType);
      }

      $sheet->setCellValue('A' . $row, $t['ui_name'] ?? ($t['name'] ?? ''));
      $sheet->setCellValue('B' . $row, $t['ui_title'] ?? ($t['title'] ?? ''));
      $sheet->setCellValue('C' . $row, $t['description'] ?? '');
      $sheet->setCellValue('D' . $row, $typeLabel);
      $sheet->setCellValue('E' . $row, number_format((float) ($t['amount'] ?? 0), 2, ',', '.'));
      $sheet->setCellValue('F' . $row, !empty($t['date']) ? date('d/m/Y', strtotime($t['date'])) : '');
      $row++;
    }

    if ($row > 2) {
      $sheet->getStyle('A2:F' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
      $sheet->getColumnDimension('A')->setAutoSize(true);
      $sheet->getColumnDimension('B')->setAutoSize(true);
      $sheet->getColumnDimension('C')->setAutoSize(true);
      $sheet->getColumnDimension('D')->setAutoSize(true);
      $sheet->getColumnDimension('E')->setAutoSize(true);
      $sheet->getColumnDimension('F')->setAutoSize(true);
    }

    $properties = $spreadsheet->getProperties();

    $properties
      ->setCreator('Sistema de Gerenciamento de Caixa - Informática 3 (2026)')
      ->setLastModifiedBy('Sistema de Gerenciamento de Caixa - Informática 3 (2026)')
      ->setTitle('Relatório de Transações Financeiras')
      ->setSubject('Relatório de Transações - Gerado em ' . date('d/m/Y H:i'))
      ->setDescription(
        'Relatório de transações financeiras gerado automaticamente pelo ' .
        'Sistema de Gerenciamento de Caixa da disciplina Informática 3, ' .
        'referente ao ano letivo de 2026.'
      )
      ->setCategory('Financeiro')
      ->setCompany(
        '3º Ano do Ensino Médio Integrado ao Curso Técnico em Informática ' .
        '- Turma de 2024-2026'
      );


    $filename = 'transacoes_' . date('Y-m-d_His') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $tempPath = sys_get_temp_dir() . '/' . uniqid('xlsx_', true) . '.xlsx';
    $writer->save($tempPath);
    $content = file_get_contents($tempPath);
    @unlink($tempPath);

    return $this->response->download($filename, $content)->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  }

  public function exportPdf(): ResponseInterface|RedirectResponse
  {
    [$transactions, $redirect] = $this->getTransactionsForExport();
    if ($redirect) {
      return $redirect;
    }

    $mpdf = new Mpdf([
      'mode' => 'utf-8',
      'format' => 'A4',
      'margin_left' => 15,
      'margin_right' => 15,
      'margin_top' => 20,
      'margin_bottom' => 20,
    ]);

    $totalIncome = 0;
    $totalOutcome = 0;
    foreach ($transactions as $t) {
      $amt = (float) ($t['amount'] ?? 0);
      $uiType = $t['ui_type'] ?? $t['type'] ?? '';
      if ($uiType === 'income') {
        $totalIncome += $amt;
      } elseif ($uiType === 'outcome') {
        $totalOutcome += $amt;
      }
    }
    $balance = $totalIncome - $totalOutcome;

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
        
            .page {
              padding: 26px 24px 18px 24px;
            }
        
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
              font-size: 20pt;
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
        
            .summary-grid tr:last-child td {
              border-bottom: 0;
            }
        
            .k {
              color: #334155;
              width: 45%;
            }
        
            .v {
              color: #0f172a;
              width: 55%;
              text-align: right;
              font-weight: 700;
              white-space: nowrap;
            }
        
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
        
            table.report tbody tr:nth-child(even) {
              background: #f8fafc;
            }
        
            .amount-cell {
              text-align: right;
              white-space: nowrap;
              font-variant-numeric: tabular-nums;
            }
        
            .date-cell {
              white-space: nowrap;
            }
        
            .muted {
              color: #64748b;
            }
        
            .badge {
              display: inline-block;
              padding: 3px 8px;
              border-radius: 999px;
              font-size: 8.5pt;
              font-weight: 700;
              border: 1px solid transparent;
              white-space: nowrap;
            }
        
            .badge.in {
              background: #ecfdf5;
              color: #065f46;
              border-color: #a7f3d0;
            }
        
            .badge.out {
              background: #fef2f2;
              color: #7f1d1d;
              border-color: #fecaca;
            }
        
            .footer {
              margin-top: 10px;
              padding-top: 12px;
              border-top: 1px solid #e2e8f0;
              font-size: 8pt;
              color: #64748b;
              text-align: center;
              line-height: 1.35;
            }
        
            .footer strong {
              color: #334155;
            }
        
            .footer a {
              color: #0369a1;
              text-decoration: none;
              font-weight: 600;
            }
        
            .footer a:hover {
              text-decoration: underline;
            }
          </style>
        </head>
        <body>
          <div class="page">
            <div class="header">
              <p class="brand"><strong>INFOR-3</strong> • Caixa 2026</p>
              <h1>Relatório de Transações</h1>
              <p class="subtitle">Gerado em ' . date('d/m/Y H:i') . '</p>
            </div>
        
            <div class="summary">
              <p class="summary-title"><strong>Resumo financeiro</strong></p>
        
              <table class="summary-grid">
                <tr>
                  <td class="k">Total de entradas</td>
                  <td class="v amount-in">R$ ' . number_format($totalIncome, 2, ',', '.') . '</td>
                </tr>
                <tr>
                  <td class="k">Total de saídas</td>
                  <td class="v amount-out">R$ ' . number_format($totalOutcome, 2, ',', '.') . '</td>
                </tr>
                <tr>
                  <td class="k">Saldo</td>
                  <td class="v balance ' . (($balance ?? 0) < 0 ? 'neg' : 'pos') . '">R$ ' . number_format($balance, 2, ',', '.') . '</td>
                </tr>
                <tr>
                  <td class="k">Quantidade de transações</td>
                  <td class="v">' . count($transactions) . '</td>
                </tr>
              </table>
            </div>
        
            <table class="report">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Título</th>
                  <th>Tipo</th>
                  <th style="text-align:right;">Valor</th>
                  <th>Data</th>
                </tr>
              </thead>
              <tbody>';
    foreach ($transactions as $t) {
      $uiType = $t['ui_type'] ?? $t['type'] ?? '';

      if ($uiType === 'deposit') {
        $typeLabel = 'Depósito';
      } elseif ($uiType === 'withdraw') {
        $typeLabel = 'Resgate';
      } elseif ($uiType === 'transfer') {
        $typeLabel = 'Transferência';
      } elseif ($uiType === 'income') {
        $typeLabel = 'Entrada';
      } elseif ($uiType === 'outcome') {
        $typeLabel = 'Saída';
      } else {
        $typeLabel = ucfirst((string) $uiType);
      }

      $amount = (float) ($t['amount'] ?? 0);

      $class = $uiType === 'income'
        ? 'amount-in'
        : ($uiType === 'outcome' ? 'amount-out' : '');

      $sinal = $uiType === 'income'
        ? '+'
        : ($uiType === 'outcome' ? '-' : '');
      $dateStr = !empty($t['date']) ? date('d/m/Y', strtotime($t['date'])) : '—';
      $badgeClass = $uiType === 'income'
        ? 'in'
        : ($uiType === 'outcome' ? 'out' : '');

      $html .= '<tr>
            <td>' . htmlspecialchars($t['ui_name'] ?? ($t['name'] ?? '')) . '</td>
            <td>' . htmlspecialchars($t['ui_title'] ?? ($t['title'] ?? '')) . '</td>
            <td><span class="badge ' . $badgeClass . '">' . htmlspecialchars($typeLabel) . '</span></td>
            <td class="amount-cell ' . $class . '">' . $sinal . ' R$ ' . number_format($amount, 2, ',', '.') . '</td>
            <td class="date-cell muted">' . $dateStr . '</td>
          </tr>';
    }
    $html .= '
              </tbody>
            </table>
        
            <div class="footer">
              <div>
                <strong>Documento gerado pelo sistema</strong> • <a href="https://github.com/pedronicolasg/caixainfor-front#readme" target="_blank">Caixa 2026 — Informática 3 (2024 - 2026) </a>
              </div>
              <div class="muted">
                Uso interno • Valores em BRL
              </div>
            </div>
          </div>
        </body>
        </html>';


    $mpdf->SetTitle('Relatório de Transações gerado em ' . date('d/m/Y H:i'));
    $mpdf->SetAuthor('3º Ano do Ensino Médio Integrado ao Curso Técnico em Informática - Turma de 2024-2026');
    $mpdf->SetCreator('Sistema de Gerenciamento de Caixa 2026 - Informática 3');


    $mpdf->WriteHTML($html);
    $filename = 'relatorio_transacoes_' . date('Y-m-d_His') . '.pdf';
    $output = $mpdf->Output('', 'S');

    return $this->response->download($filename, $output)->setHeader('Content-Type', 'application/pdf');
  }
}
