<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;
use App\Services\AccountingService;
use Exception;

class InventoryTransactionManager
{
    protected $inventoryService;
    protected $accountingService;

    public function __construct(InventoryService $inventoryService, AccountingService $accountingService)
    {
        $this->inventoryService = $inventoryService;
        $this->accountingService = $accountingService;
    }

    /**
     * Process a purchase of carpets or materials
     */
    public function processPurchase($model, array $params, callable $legacyCallback = null)
    {
        return DB::transaction(function () use ($model, $params, $legacyCallback) {
            if ($legacyCallback) $legacyCallback();

            // 1. Record Inventory Movement
            $inventoryTx = $this->inventoryService->recordMovement([
                'item_model' => $model,
                'type' => 'PURCHASE',
                'direction' => 'IN',
                'quantity' => $params['quantity'],
                'warehouse_id' => $params['warehouse_id'] ?? 1,
                'unit_cost' => $params['unit_cost'],
                'currency_code' => $params['currency_code'] ?? null,
                'exchange_rate' => $params['exchange_rate'] ?? null,
                'area' => $params['area'] ?? 0,
                'created_by' => auth()->id(),
                'is_value_adjustment' => false,
            ]);

            if (!$inventoryTx) return null;

            // 2. Post Accounting Entry
            $accountingTx = $this->accountingService->postAutoTransaction(
                $params['transaction_type'] ?? 'material_purchase',
                'credit',
                [
                    'date' => $params['date'] ?? now()->format('Y-m-d'),
                    'amount' => $params['total_amount'] ?? ($params['quantity'] * $params['unit_cost']),
                    'party_type' => $params['party_type'] ?? null,
                    'party_id' => $params['party_id'] ?? null,
                    'reference' => $params['reference'] ?? null,
                    'description' => $params['description'] ?? null,
                    'source_type' => get_class($model),
                    'source_id' => $model->getKey(),
                    'override_debit_account_id' => $params['override_debit_account_id'] ?? null,
                    'override_credit_account_id' => $params['override_credit_account_id'] ?? null,
                ]
            );

            return [
                'inventory_transaction_id' => $inventoryTx,
                'accounting_transaction' => $accountingTx
            ];
        });
    }

    /**
     * Record a service cost during production (Washing/Finishing)
     * Flow: DR WIP / CR Payable
     * @param $serviceModel The service record (e.g. App\CarpetWash)
     * @param $parentModel The inventory item record (e.g. App\Carpet)
     */
    public function recordProductionService($serviceModel, $parentModel, array $params, callable $legacyCallback = null)
    {
        return DB::transaction(function () use ($serviceModel, $parentModel, $params, $legacyCallback) {
            if ($legacyCallback) $legacyCallback();

            // 1. Record Inventory Movement (Cost adjustment for parent item)
            $inventoryTx = $this->inventoryService->recordMovement([
                'item_model' => $serviceModel,
                'parent_item_model' => $parentModel,
                'type' => $params['type'] ?? 'PROD_SERVICE',
                'direction' => 'IN',
                'quantity' => 0,
                'warehouse_id' => $params['warehouse_id'] ?? 1,
                'unit_cost' => $params['amount'],
                'area' => $params['area'] ?? 0,
                'created_by' => auth()->id(),
                'is_value_adjustment' => true,
            ]);

            if (!$inventoryTx) return null;

            // 2. Post Accounting Entry
            $accountingTx = $this->accountingService->postAutoTransaction(
                strtolower($params['type'] ?? 'production_service'),
                $params['mapping_key'] ?? ($params['type'] . '_CREDIT' ?? 'credit'),
                [
                    'date' => $params['date'] ?? now()->format('Y-m-d'),
                    'amount' => $params['amount'],
                    'party_type' => $params['party_type'] ?? null,
                    'party_id' => $params['party_id'] ?? null,
                    'reference' => $params['reference'] ?? null,
                    'description' => $params['description'] ?? null,
                    'source_type' => get_class($serviceModel),
                    'source_id' => $serviceModel->getKey(),
                    'override_debit_account_id' => $params['override_debit_account_id'] ?? null,
                    'override_credit_account_id' => $params['override_credit_account_id'] ?? null,
                ]
            );

            return [
                'inventory_transaction_id' => $inventoryTx,
                'accounting_transaction' => $accountingTx
            ];
        });
    }

    /**
     * Process production start (Issue Materials to WIP)
     * Flow: DR WIP / CR Inventory
     */
    public function processProductionStart($model, array $params, callable $legacyCallback = null)
    {
        return DB::transaction(function () use ($model, $params, $legacyCallback) {
            if ($legacyCallback) $legacyCallback();

            $inventoryTx = $this->inventoryService->recordMovement([
                'item_model' => $model,
                'type' => 'PROD_ISSUE',
                'direction' => 'OUT',
                'quantity' => $params['quantity'],
                'warehouse_id' => $params['warehouse_id'] ?? 1,
                'unit_cost' => $params['unit_cost'] ?? 0,
                'created_by' => auth()->id(),
                'is_value_adjustment' => false,
            ]);

            if (!$inventoryTx) return null;

            $accountingTx = $this->accountingService->postAutoTransaction(
                'production_start',
                'transfer',
                [
                    'date' => $params['date'] ?? now()->format('Y-m-d'),
                    'amount' => $params['amount'],
                    'reference' => $params['reference'] ?? null,
                    'description' => $params['description'] ?? null,
                    'source_type' => get_class($model),
                    'source_id' => $model->getKey(),
                    'override_debit_account_id' => $params['override_debit_account_id'] ?? null,
                    'override_credit_account_id' => $params['override_credit_account_id'] ?? null,
                ]
            );

            return [
                'inventory_transaction_id' => $inventoryTx,
                'accounting_transaction' => $accountingTx
            ];
        });
    }

    /**
     * Process production completion (WIP to Finished Goods)
     */
    public function processProductionCompletion($model, array $params, callable $legacyCallback = null)
    {
        return DB::transaction(function () use ($model, $params, $legacyCallback) {
            if ($legacyCallback) $legacyCallback();

            $inventoryTx = $this->inventoryService->recordMovement([
                'item_model' => $model,
                'type' => 'PROD_FINISH',
                'direction' => 'IN',
                'quantity' => $params['quantity'] ?? 1,
                'warehouse_id' => $params['warehouse_id'] ?? 1,
                'unit_cost' => $params['unit_cost'] ?? 0,
                'area' => $params['area'] ?? 0,
                'created_by' => auth()->id(),
                'is_value_adjustment' => false,
            ]);

            if (!$inventoryTx) return null;

            $accountingTx = $this->accountingService->postAutoTransaction(
                'production_completion',
                'transfer',
                [
                    'date' => $params['date'] ?? now()->format('Y-m-d'),
                    'amount' => $params['amount'],
                    'reference' => $params['reference'] ?? null,
                    'description' => $params['description'] ?? null,
                    'source_type' => get_class($model),
                    'source_id' => $model->getKey(),
                    'override_debit_account_id' => $params['override_debit_account_id'] ?? null,
                    'override_credit_account_id' => $params['override_credit_account_id'] ?? null,
                ]
            );

            return [
                'inventory_transaction_id' => $inventoryTx,
                'accounting_transaction' => $accountingTx
            ];
        });
    }

    /**
     * Process a sale of carpets or materials
     */
    public function processSale($model, array $params, callable $legacyCallback = null)
    {
        return DB::transaction(function () use ($model, $params, $legacyCallback) {
            if ($legacyCallback) $legacyCallback();

            // 1. Record Inventory Movement (Physical OUT)
            $inventoryTx = $this->inventoryService->recordMovement([
                'item_model' => $model,
                'type' => 'SALE',
                'direction' => 'OUT',
                'quantity' => $params['quantity'] ?? 1,
                'warehouse_id' => $params['warehouse_id'] ?? 1,
                'unit_cost' => $params['unit_cost'] ?? 0,
                'currency_code' => $params['currency_code'] ?? 'USD',
                'exchange_rate' => $params['exchange_rate'] ?? 1.0,
                'created_by' => auth()->id(),
                'is_value_adjustment' => false,
            ]);

            if (!$inventoryTx) return null;

            // 2. Fetch the actual cost from the item record (WAC/Specific Cost)
            $itemType = get_class($model);
            $itemRefId = $model->getKey();
            if ($model instanceof \App\MaterialSale) {
                $itemType = 'App\MaterialType';
                $itemRefId = $model->type_id;
            }

            $item = DB::table('items')
                ->where('type', $itemType)
                ->where('ref_id', $itemRefId)
                ->first();
            
            // WAC is already in USD base
            $actualCost = $item ? (bcmul($item->current_cost, ($params['quantity'] ?? 1), 8)) : 0;

            // 3. Post Revenue Entry (DR Receivable / CR Revenue)
            $revenueTx = $this->accountingService->postAutoTransaction(
                $params['transaction_type'] ?? 'sale',
                $params['mapping_key'] ?? 'SALES_REVENUE',
                [
                    'date' => $params['date'] ?? now()->format('Y-m-d'),
                    'amount' => $params['sale_amount'],
                    'party_type' => $params['party_type'] ?? 'App\Customer',
                    'party_id' => $params['customer_id'] ?? $params['party_id'],
                    'currency_code' => $params['currency_code'] ?? 'USD',
                    'exchange_rate' => $params['exchange_rate'] ?? 1.0,
                    'reference' => $params['reference'] ?? null,
                    'description' => $params['description'] ?? "Sale of item",
                    'source_type' => get_class($model),
                    'source_id' => $model->getKey(),
                    'override_debit_account_id' => $params['override_debit_account_id'] ?? null,
                    'override_credit_account_id' => $params['override_credit_account_id'] ?? null,
                ]
            );

            // 4. Post COGS Entry (DR COGS / CR Inventory) - COGS is ALWAYS base currency (USD)
            $cogsTx = $this->accountingService->postAutoTransaction(
                $params['transaction_type'] ?? 'sale',
                'SALES_COGS',
                [
                    'date' => $params['date'] ?? now()->format('Y-m-d'),
                    'amount' => $actualCost,
                    'currency_code' => 'USD', // COGS is normalized
                    'exchange_rate' => 1.0,
                    'reference' => $params['reference'] ?? null,
                    'description' => "COGS for " . ($params['reference'] ?? "sale"),
                    'source_type' => get_class($model),
                    'source_id' => $model->getKey(),
                    'override_debit_account_id' => $params['override_cogs_debit_id'] ?? null,
                    'override_credit_account_id' => $params['override_cogs_credit_id'] ?? null,
                ]
            );

            return [
                'inventory_transaction_id' => $inventoryTx,
                'revenue_transaction' => $revenueTx,
                'cogs_transaction' => $cogsTx
            ];
        });
    }

    /**
     * Generic movement
     */
    public function processGenericMovement(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->inventoryService->recordMovement($data);
        });
    }

    /**
     * Reverse all transactions (Inventory + Accounting) for a model
     */
    public function reverseTransactions($model, $reason = null)
    {
        return DB::transaction(function () use ($model, $reason) {
            // 1. Reverse Inventory
            $this->inventoryService->reverseMovement($model, $reason);

            // 2. Reverse Accounting
            $this->accountingService->reverseTransactionBySource($model->getKey(), $reason, get_class($model));
        });
    }
}
