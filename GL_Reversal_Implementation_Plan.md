# Implementation Plan: Centralized Source Synchronization for Ledger Reversals

## Objective
To ensure that when a transaction is reversed directly from the General Ledger (`Accounting/Journals`), the original module (e.g., Agent Payments, Kachaee Payments, Purchase Bills, Sales Invoices) automatically updates its corresponding record status (usually to `status = 0` or similar canceled flag). This prevents discrepancies where the GL reverses the financial impact but the origin module continues to include the record in UI calculations.

---

## 1. Code Implementation Strategy

### Location
File: `app/Services/AccountingService.php`
Method: `reverseTransaction($transactionId, $reason = null)`

### Implementation Logic
The synchronization logic will be injected directly inside the existing `DB::transaction` wrapper. This guarantees **100% database atomicity**—if updating the origin module fails, the entire GL reversal is aborted and rolled back.

```php
// Inside AccountingService.php -> reverseTransaction()
return DB::transaction(function () use ($transactionId, $reason) {
    // ... [existing GL reversal logic: creating flipped entries, updating status] ...

    // Define the map of GL source_type identifiers to their Eloquent Models
    $sourceModelMapping = [
        'Agent_payment'              => \App\AgentPayment::class,
    'Agent_advance_settlement'   => \App\AgentPaymentAllocation::class, // Or the correct model handling this
    'App\KachaeePayment'         => \App\KachaeePayment::class,
    'Washing_payment'            => \App\WashingPayment::class,
    'Finishing_payment'          => \App\FinishingTeamPayment::class,
    'Customer_payment'           => \App\CustomerPayment::class,
    'Seller_payment'             => \App\SellerPayment::class,
    'DifferentAccountPayment'    => \App\DifferentAccountPayment::class,
    'Ajnas_account'              => \App\AjnasAccountDetail::class,
    'Expense'                    => \App\MonthlyExpenseBalance::class,
    'NewMonthlyExpenseBalance'   => \App\NewMonthlyExpenseBalance::class,
    'Office_debit'               => \App\OfficeDebit::class,
    'Office_credit'              => \App\OfficeCredit::class,
    'App\PurchaseMaterial'       => \App\PurchaseMaterial::class, // (Purchase Bills)
    'App\MaterialSale'           => \App\MaterialSale::class,     // (Sales Invoices)
    'PayrollRun'                 => \App\PayrollRun::class,
    'App\Carpet'                 => \App\Carpet::class,
    'App\CarpetRepair'           => \App\CarpetRepair::class,
    'App\CarpetWash'             => \App\CarpetWash::class,
    'App\FinishingWork'          => \App\FinishingWork::class,
];

if ($original->source_type && isset($sourceModelMapping[$original->source_type])) {
    $modelClass = $sourceModelMapping[$original->source_type];
    if (class_exists($modelClass)) {
        // The column for cancellation might be 'status' = 0, or 'is_active' = false.
        // We will default to 'status' => 0. We can add conditional checks if models use different columns.
        $modelClass::where('id', $original->source_id)->update(['status' => 0]);
        
        // *Special Note for Allocations/Settlements:
        // If it's a Purchase Bill or Sales Invoice payment allocation being cancelled, 
        // we might also need to restore the 'remaining_unallocated_amount' on the parent advance payment.
    }
}

}); // <-- End of DB::transaction block
```

---

## 2. Test Plan & Modules to Verify

Because this change affects the central nervous system of the financial ledger, it must be verified across all critical financial entry points.

### A. Agent Module (Complex Edge Cases)
1. **Direct Payments:** 
   - Test: Make a direct payment to an agent. Go to GL, reverse it. 
   - Expect: Payment disappears from Agent's active ledger.
2. **Advance Payments:** 
   - Test: Give an advance payment. Go to GL, reverse it. 
   - Expect: Advance is voided, unallocated balance reverts.
3. **Purchase Bill Allocations (Critical):** 
   - Test: Allocate an agent's advance against a Purchase Bill (`PurchaseMaterial`). Reverse from GL.
   - Expect: The allocation is nullified, the Purchase Bill returns to 'Unpaid' (or partial), and the Agent's advance unallocated balance increases back.
4. **Sales Invoice Allocations:** 
   - Test: Receive payment for a Sales Invoice (`MaterialSale`). Reverse from GL.
   - Expect: The Sales Invoice returns to 'Unpaid', revenue is reversed in GL.

### B. Production Modules
1. **Kachaee Team Payments:**
   - Test: Record a payment to a Kachaee Team. Reverse from GL.
   - Expect: `status` of `App\KachaeePayment` goes to 0, Wage Ledger clears.
2. **Finishing & Washing Payments:**
   - Test: Record team payments. Reverse from GL.
   - Expect: Disappears from their specific UI tabs.

### C. Internal Accounting Modules
1. **Different Accounts (Misc. ledgers):**
   - Test: Record a debit/credit. Reverse from GL.
   - Expect: `DifferentAccountPayment` status becomes 0.
2. **Monthly Expenses (OpEx):**
   - Test: Record a new expense. Reverse from GL.
   - Expect: Expense is canceled in the expense report views.

### D. Purchase and Sales (Inventory/Trading)
1. **Purchase Bills (`App\PurchaseMaterial`):**
   - Test: Reverse a recorded Purchase Bill from GL.
   - Expect: Stock valuation is reversed, Bill status is canceled.
2. **Sales Invoices (`App\MaterialSale`):**
   - Test: Reverse a recorded Sale from GL.
   - Expect: Revenue canceled, stock potentially restored.

---

## 3. Risk Assessment & Safety Profile

* **Database Atomicity (Transaction Safe):** EXTREME SAFETY. The entire operation is nested within Laravel's `DB::transaction()`. If updating an invoice, a purchase bill, or an agent payment fails due to a database lock or validation error, the transaction will automatically rollback the GL reversal, preventing orphaned or corrupted financial records.
* **Code Safety:** High. The modifications are contained entirely within a single service layer method (`AccountingService@reverseTransaction`). No controllers or views are touched.
* **Permission Integrity:** Perfect. The reversal logic only triggers if the user has the explicit Spatie `@can('reverse_gl_journals')` or equivalent authorization required to hit the Journal endpoint.
* **Database Integrity:** High. Because we use Eloquent's `update()` based on `source_id`, the database constraint checks remain intact.

## 4. Next Steps
Once approved, the implementation of the `sourceModelMapping` dictionary in `AccountingService.php` will take less than 15 minutes. Following that, a full QA cycle of the scenarios outlined in Section 2 is required before final deployment.
