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
            'debit'  => ['account_type' => 'Asset'], // broader filter for safety
            'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1] 
        ],
        'SALES_REVENUE' => [
            'debit'  => ['account_type' => 'Asset'], 
            'credit' => ['account_type' => 'Revenue']
        ],
        'SALES_COGS' => [
            'debit'  => ['account_type' => 'Expense'], // COGS is an expense
            'credit' => ['report_group' => 'Inventory']
        ],
        'PYMT_IN' => [
            'debit'  => ['account_type' => 'Asset'], // Must be cash/bank asset
            'credit' => ['account_type' => 'Asset']  // Must be receivable asset
        ],
        'PYMT_OUT' => [
            'debit'  => ['account_type' => 'Liability'], // Must be payable liability
            'credit' => ['account_type' => 'Asset']     // Must be cash/bank asset
        ],
        'CASH_OUT' => [
            'debit'  => ['account_type' => 'Expense'], 
            'credit' => ['is_cash_account' => 1]
        ],
        'EXPENSE_DEFAULT' => [
            'debit'  => ['account_type' => 'Expense'],
            'credit' => ['is_cash_account' => 1]
        ],
        'WASHING_CREDIT' => [
            'debit'  => ['account_type' => 'Asset'],
            'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1]
        ],
        'FINISHING_CREDIT' => [
            'debit'  => ['account_type' => 'Asset'],
            'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1]
        ],
        'EXP_RENT' => [
            'debit'  => ['account_type' => 'Expense'],
            'credit' => ['is_cash_account' => 1]
        ],
        'EXP_FOOD' => [
            'debit'  => ['account_type' => 'Expense'],
            'credit' => ['is_cash_account' => 1]
        ],
        'EXP_TRANS' => [
            'debit'  => ['account_type' => 'Expense'],
            'credit' => ['is_cash_account' => 1]
        ],
        'EXP_MISC' => [
            'debit'  => ['account_type' => 'Expense'],
            'credit' => ['is_cash_account' => 1]
        ],
        'PAYROLL_ACCRUAL' => [
            'debit'  => ['account_type' => 'Expense'],
            'credit' => ['account_type' => 'Liability']
        ],
        'ASSET_PURCH' => [
            'debit'  => ['report_group' => 'Fixed Assets'],
            'credit' => ['is_cash_account' => 1, 'account_type' => 'Liability']
        ],
        'MATERIAL_REVENUE' => [
            'debit'  => ['account_type' => 'Asset'], // Default to asset for receivables/cash
            'credit' => ['account_type' => 'Revenue']
        ],
        'CARPET_INVENTORY' => [
            'debit'  => ['report_group' => 'Inventory'],
            'credit' => ['report_group' => 'Inventory']
        ],
        'MATERIAL_RECEIPT' => [
            'debit'  => ['report_group' => 'Inventory'],
            'credit' => ['account_type' => 'Liability', 'is_cash_account' => 1]
        ],
        'MATERIAL_PAYMENT' => [
            'debit'  => ['account_type' => 'Liability', 'is_cash_account' => 1],
            'credit' => ['report_group' => 'Inventory']
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
                 $query->where(function($q) {
                     $q->where('account_type', 'Liability')
                       ->orWhere('is_cash_account', 1);
                 });
                 
                 // Remove these from filters so they aren't added again as AND
                 unset($filters['account_type']);
                 unset($filters['is_cash_account']);
            }

            foreach ($filters as $key => $value) {
                $query->where($key, $value);
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
        if (!$filters) return true;

        $account = ChartOfAccount::find($accountId);
        if (!$account) throw new Exception("حساب انتخاب شده وجود ندارد.");

        foreach ($filters as $key => $value) {
            if ($key === 'is_cash_account' && isset($filters['account_type']) && $filters['account_type'] === 'Liability') {
                if ($account->account_type !== 'Liability' && $account->is_cash_account != 1) {
                     throw new Exception("برای این معامله باید حساب بدهی یا حساب نقد انتخاب شود.");
                }
                continue; 
            }
            
            if ($account->$key != $value) {
                $attrName = ($key === 'account_type') ? 'نوعیت حساب' : (($key === 'report_group') ? 'گروه حساب' : $key);
                throw new Exception("مقدار ($attrName) برای حساب انتخاب شده ($account->$key) است، اما برای این معامله ($value) لازم است.");
            }
        }

        return true;
    }
}
