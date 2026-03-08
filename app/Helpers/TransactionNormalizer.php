<?php

namespace App\Helpers;

class TransactionNormalizer
{
    public static function normalize(array $transactions, array $vaultsById = [], ?string $filterType = null): array
    {
        $groups = [];
        $singles = [];

        foreach ($transactions as $t) {
            $key = self::getTransferGroupKey($t);

            if ($key === null) {
                $singles[] = self::normalizeSingle($t, $vaultsById);
                continue;
            }

            if (!isset($groups[$key])) {
                $groups[$key] = [];
            }
            $groups[$key][] = $t;
        }

        foreach ($groups as $rows) {
            if (count($rows) < 2) {
                $singles[] = self::normalizeSingle($rows[0], $vaultsById);
            } else {
                $singles[] = self::normalizeTransferGroup($rows, $vaultsById);
            }
        }

        if ($filterType) {
            $singles = array_values(array_filter($singles, function (array $t) use ($filterType) {
                $uiType = $t['ui_type'] ?? $t['type'] ?? null;
                return $uiType === $filterType;
            }));
        }

        return $singles;
    }

    private static function getTransferGroupKey(array $t): ?string
    {
        $type = $t['type'] ?? null;
        if ($type !== 'transfer_in' && $type !== 'transfer_out') {
            return null;
        }

        $user = (string) ($t['user_id'] ?? '');
        $name = (string) ($t['name'] ?? '');
        $title = (string) ($t['title'] ?? '');
        $amount = (string) ($t['amount'] ?? '0');
        $date = (string) ($t['date'] ?? ($t['created_at'] ?? ''));
        $dateKey = $date ? substr($date, 0, 16) : '';

        return $user . '|' . $name . '|' . $title . '|' . $amount . '|' . $dateKey;
    }

    private static function normalizeSingle(array $t, array $vaultsById): array
    {
        $type = $t['type'] ?? null;
        $isIncome = $type === 'income';
        $isOutcome = $type === 'outcome';

        $uiType = $type;
        $sign = null;
        if ($isIncome) {
            $sign = '+';
        } elseif ($isOutcome) {
            $sign = '-';
        }

        $vaultId = $t['vault_id'] ?? null;
        $vaultName = null;
        if ($vaultId !== null && isset($vaultsById[$vaultId])) {
            $vaultName = $vaultsById[$vaultId]['name'] ?? null;
        }

        $uiName = $t['name'] ?? '';
        $uiTitle = $t['title'] ?? '';

        return array_merge($t, [
            'ui_type' => $uiType,
            'ui_sign' => $sign,
            'ui_name' => $uiName,
            'ui_title' => $uiTitle,
            'ui_vault_name' => $vaultName,
        ]);
    }

    private static function normalizeTransferGroup(array $rows, array $vaultsById): array
    {
        $in = null;
        $out = null;

        foreach ($rows as $r) {
            if (($r['type'] ?? null) === 'transfer_in') {
                $in = $r;
            } elseif (($r['type'] ?? null) === 'transfer_out') {
                $out = $r;
            }
        }

        if ($in === null && $out === null) {
            return self::normalizeSingle($rows[0], $vaultsById);
        }

        $base = $in ?? $out;
        $amount = (float) ($base['amount'] ?? 0);
        $date = $base['date'] ?? ($base['created_at'] ?? null);

        $inVaultId = $in['vault_id'] ?? null;
        $outVaultId = $out['vault_id'] ?? null;

        $inVault = ($inVaultId !== null && isset($vaultsById[$inVaultId])) ? $vaultsById[$inVaultId] : null;
        $outVault = ($outVaultId !== null && isset($vaultsById[$outVaultId])) ? $vaultsById[$outVaultId] : null;

        $uiType = 'transfer';
        $uiName = '';
        $uiTitle = '';
        $sign = null;

        if ($outVaultId === null && $inVaultId !== null) {
            $uiType = 'deposit';
            $uiTitle = 'Depósito';
            $uiName = $inVault['name'] ?? ('Caixinha #' . $inVaultId);

            $base['vault_id'] = $inVaultId;
        } elseif ($outVaultId !== null && $inVaultId === null) {
            $uiType = 'withdraw';
            $uiTitle = 'Resgate';
            $uiName = $outVault['name'] ?? ('Caixinha #' . $outVaultId);

            $base['vault_id'] = $outVaultId;
        } else {
            $uiType = 'transfer';
            $fromName = $outVault['name'] ?? ($outVaultId !== null ? ('Caixinha #' . $outVaultId) : 'Geral');
            $toName = $inVault['name'] ?? ($inVaultId !== null ? ('Caixinha #' . $inVaultId) : 'Geral');
            $uiName = $fromName . ' → ' . $toName;
            $uiTitle = 'Transferência entre caixinhas';
        }

        return array_merge($base, [
            'type' => $uiType,
            'ui_type' => $uiType,
            'ui_sign' => $sign,
            'ui_name' => $uiName,
            'ui_title' => $uiTitle,
            'ui_vault_name' => $uiType === 'transfer'
                ? null
                : ($inVault['name'] ?? $outVault['name'] ?? null),
            'from_vault_id' => $outVaultId,
            'to_vault_id' => $inVaultId,
            'amount' => $amount,
            'date' => $date,
        ]);
    }
}

