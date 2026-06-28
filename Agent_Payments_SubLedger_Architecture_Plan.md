# Sub-ledger Driven Architecture: Implementation Plan

## Architectural Philosophy
Instead of allowing accountants to reverse transactions directly from the General Ledger (which orphans records in the origin modules), **all edits and cancellations will strictly originate from the respective sub-ledger module**. 
The GL Journal "Reverse" button will be disabled/hidden. The sub-ledger becomes the single source of truth and "pushes" reversals to the GL.

We will start with the **Agent Payments** module as the blueprint.

---

## Agent Payments Module: Comprehensive Reversal Strategy

The Agent Payments dashboard has 5 distinct tabs. Each handles different financial logic that must be carefully unwound when an entry is cancelled.

### 1. Cash Ledger Tab (Direct Payments) & Advances Tab
**Scenario:** The company gave cash/bank to the Agent either as a Direct Payment for a job, or as a general Advance Payment.
*   **Action Needed:** Add an explicit `ابطال` (Cancel) button on the UI for each row.
*   **Controller Logic (`AgentPaymentController@cancelPayment`):**
    1.  Ensure the payment has not already been used in allocations (if it's an advance). If `remaining_unallocated_amount != original_amount`, block the cancellation and throw an error: *"Please cancel the associated reconciliations first."*
    2.  Change the payment `status = 0`.
    3.  Call `$accountingService->reverseTransactionBySource($payment->id, 'Agent Payment Cancelled')`.
    4.  *(No hard deletions—we keep the record for audit trails).*

### 2. Purchase Bills Tab (Direct Payments vs. Advance Allocations)
**Scenario A: Direct Payment for a Purchase Bill**
*   **Action Needed:** Add a Cancel button next to direct payments.
*   **Controller Logic:**
    1.  Identify the linked `AgentPayment` record.
    2.  Execute the same logic as #1 (Status = 0, reverse GL).
    3.  Find the `PurchaseMaterial` (Purchase Bill) and recalculate its total paid.
    4.  Update the Purchase Bill `payment_status` to `'unpaid'` or `'partially_paid'`.

**Scenario B: Paid via "Advance Allocation" (Reconciliation)**
*   **Action Needed:** Add a Cancel button on the allocation record.
*   **Controller Logic (`AgentPaymentController@removeAllocation`):**
    1.  Find the `AgentPaymentAllocation` record and delete it (or mark inactive).
    2.  Find the parent Advance `AgentPayment` and **restore** its `remaining_unallocated_amount` by adding the cancelled allocation amount back to it.
    3.  Call `$accountingService->reverseTransactionBySource($allocation->id, 'Agent Allocation Reversed')` to pull the money out of Accounts Payable and put it back into the Agent's Advance GL account.
    4.  Update the Purchase Bill `payment_status` back to `'unpaid'` or `'partially_paid'`.

### 3. Sales Invoices Tab
**Scenario:** Similar to Purchase Bills, but handling accounts receivable (Customer/Agent paying us).
*   **Action Needed:** Cancel button for Invoice Payments and Allocations.
*   **Controller Logic:**
    1.  If direct payment: Cancel the `AgentPayment` (status = 0), reverse GL.
    2.  If advance allocation: Delete allocation, restore the Agent's unallocated advance balance, reverse the GL settlement entry.
    3.  Find the `MaterialSale` (Sales Invoice), recalculate total paid, and update `payment_status` to `'unpaid'`.

### 4. Reconciliation Tab (History)
**Scenario:** This tab shows the history of all allocations made.
*   **Current State:** The user mentioned this already partially works.
*   **Action Needed:** Audit the existing `removeAllocation` function to ensure it is correctly triggering the `$accountingService->reverseTransactionBySource()` and properly restoring the `remaining_unallocated_amount` on the parent advance.

---

## System-Wide Changes (The Next Phase)

Once this architecture is perfected in Agent Payments, we will replicate this exact pattern across:
1.  **Finishing Payments** (Direct wages vs. Team advances)
2.  **Washing Payments**
3.  **Kachaee Payments**
4.  **Monthly Expenses (Admin/OpEx)**

## Golden Rule for the GL
**Disable GL Direct Reversals:** Once the modules have their own Cancel buttons, we will wrap the GL Journal's "Reverse" button in a strict `@can('super_admin_override')` or remove it entirely. If an accountant sees an error in the GL, they must click the "Reference" link to go to the Agent Payment / Purchase Bill and click Cancel there.

