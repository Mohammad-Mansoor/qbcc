<?php

namespace App\Services;

use App\ChartOfAccount;
use App\MappingRule;
use Exception;

class AccountSelectionService
{
    /**
     * Defines the constraints for each mapping key.
     * Format: [mapping_key => [debit => [filters], credit => [filters]]]
     */
    protected static $constraints = [
        'MATERIAL_PURCHASE_CREDIT' => [
            // 'debit' => ['account_type' => 'Asset'], // broader filter for safety
            'debit' => [], // broader filter for safety
            // 'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1]
            'credit' => []
        ],
        'SALES_REVENUE' => [
            // 'debit' => ['account_type' => 'Asset'],
            'debit' => [],
            // 'credit' => ['account_type' => 'Revenue']
            'credit' => []
        ],
        'SALES_COGS' => [
            // 'debit' => ['account_type' => 'Expense'], // COGS is an expense
            'debit' => [], // COGS is an expense
            // 'credit' => ['report_group' => 'Inventory']
            'credit' => []
        ],
        'PYMT_IN' => [
            'debit' => [],
            'credit' => []
        ],
        'PYMT_OUT' => [
            'debit' => [],
            'credit' => []
        ],
        'kachaee_payment' => [
            // 'debit' => ['account_type' => ['Asset', 'Liability']],
            'debit' => [],
            // 'credit' => ['account_type' => ['Asset', 'Liability']]
            'credit' => []
        ],
        'washing_payment' => [
            // 'debit' => ['account_type' => ['Asset', 'Liability']],
            'debit' => [],
            // 'credit' => ['account_type' => ['Asset', 'Liability']]
            'credit' => []
        ],
        'finishing_payment' => [
            // 'debit' => ['account_type' => ['Asset', 'Liability']],
            'debit' => [],
            // 'credit' => ['account_type' => ['Asset', 'Liability']]
            'credit' => []
        ],
        'CASH_OUT' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'EXPENSE_DEFAULT' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'WASHING_CREDIT' => [
            // 'debit' => ['account_type' => 'Asset'],
            'debit' => [],
            // 'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1]
            'credit' => []
        ],
        'FINISHING_CREDIT' => [
            // 'debit' => ['account_type' => 'Asset'],
            'debit' => [],
            // 'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1]
            'credit' => []
        ],
        'EXP_RENT' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'EXP_FOOD' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'EXP_TRANS' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'EXP_MISC' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'EXP_MISC_OFFICE' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'EXPENSE_کرایه_و_برق' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'EXP_FUEL' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'EXP_WAGES' => [
            // 'debit' => ['account_type' => 'Expense'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'PAYROLL_ACCRUAL' => [
            // 'debit' => ['account_type' => ['Expense', 'Asset', 'Liability']],
            'debit' => [],
            // 'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1]
            'credit' => []
        ],
        'PAYROLL_PAYMENT' => [
            // 'debit' => ['account_type' => ['Liability', 'Expense']],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1]
            'credit' => []
        ],
        'ASSET_PURCH' => [
            // 'debit' => ['report_group' => 'Fixed Asset'],
            'debit' => [],
            // 'credit' => ['is_cash_account' => 1, 'account_type' => 'Liability']
            'credit' => []
        ],
        'MATERIAL_REVENUE' => [
            // 'debit' => ['account_type' => 'Asset'], // Default to asset for receivables/cash
            'debit' => [],
            // 'credit' => ['account_type' => 'Revenue']
            'credit' => []
        ],
        'CARPET_INVENTORY' => [
            // 'debit' => ['report_group' => 'Inventory'],
            'debit' => [],
            // 'credit' => ['report_group' => 'Inventory']
            'credit' => []
        ],
        'MATERIAL_RECEIPT' => [
            // 'debit' => ['report_group' => 'Inventory'],
            'debit' => [],
            // 'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1]
            'credit' => []
        ],
        'MATERIAL_PAYMENT' => [
            // 'debit' => ['account_type' => 'Liability', 'is_cash_account' => 1],
            'debit' => [],
            // 'credit' => ['report_group' => 'Inventory']
            'credit' => []
        ],
        'DIFF_IN' => [
            'debit' => [],
            'credit' => []
        ],
        'DIFF_OUT' => [
            'debit' => [],
            'credit' => []
        ]
    ];

    /**
     * Get valid accounts for a specific mapping key and side
     */
    public function getValidAccounts($mappingKey, $side)
    {
        $filters = self::$constraints[$mappingKey][$side] ?? null;
        $query = ChartOfAccount::query();

        if ($filters) {
            // Special handling for OR logic (e.g. Liability OR Cash)
            if (isset($filters['account_type']) && $filters['account_type'] === 'Liability' && isset($filters['is_cash_account'])) {
                $query->where(function ($q) {
                    $q->where('account_type', 'Liability')
                        ->orWhere('is_cash_account', 1);
                });

                // Remove these from filters so they aren't added again as AND
                unset($filters['account_type']);
                unset($filters['is_cash_account']);
            }

            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }

        $results = $query->orderBy('account_code')->get();

        // If no results match specific filters, return broader type-based results to avoid empty dropdowns
        if ($results->isEmpty()) {
            if ($side === 'debit') {
                return ChartOfAccount::whereIn('account_type', ['Asset', 'Expense'])->orderBy('account_code')->get();
            } else {
                return ChartOfAccount::whereIn('account_type', ['Liability', 'Asset', 'Revenue'])->orderBy('account_code')->get();
            }
        }

        return $results;
    }

    /**
     * Validate if the selected account is valid for the given mapping key and side
     */
    public function validate($mappingKey, $accountId, $side)
    {
        $filters = self::$constraints[$mappingKey][$side] ?? null;
        if (!$filters)
            return true;

        $account = ChartOfAccount::find($accountId);
        if (!$account)
            throw new Exception("حساب انتخاب شده وجود ندارد.");

        foreach ($filters as $key => $value) {
            if (($key === 'is_cash_account' || $key === 'account_type') && isset($filters['account_type']) && $filters['account_type'] === 'Liability' && isset($filters['is_cash_account'])) {
                if ($account->account_type !== 'Liability' && $account->is_cash_account != 1) {
                    throw new Exception("برای این معامله باید حساب بدهی یا حساب نقد انتخاب شود.");
                }
                continue;
            }

            if (is_array($value)) {
                if (!in_array($account->$key, $value)) {
                    $attrName = ($key === 'account_type') ? 'نوعیت حساب' : (($key === 'report_group') ? 'گروه حساب' : $key);
                    $valueStr = implode(' یا ', $value);
                    throw new Exception("مقدار ($attrName) برای حساب انتخاب شده ($account->$key) است، اما برای این معامله ($valueStr) لازم است.");
                }
            } else {
                if ($account->$key != $value) {
                    $attrName = ($key === 'account_type') ? 'نوعیت حساب' : (($key === 'report_group') ? 'گروه حساب' : $key);
                    throw new Exception("مقدار ($attrName) برای حساب انتخاب شده ($account->$key) است، اما برای این معامله ($value) لازم است.");
                }
            }
        }

        return true;
    }
}
