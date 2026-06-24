# Step-by-Step Execution Plan: Advanced ERP "Wrap & Extend" Integration (Production-Grade)

This is the master integration plan for the QBIC ERP system. It combines high-depth manufacturing accounting with the module-specific requirements from the **QBIC Database notes.pdf**. 

**Principle:** Non-destructive upgrade. Existing tables remain; new services provide the ERP logic with strict audit trails and concurrency control.

---

## Phase -1: Production Hardening Layer (MANDATORY BEFORE IMPLEMENTATION)
**Goal:** Harden the architecture to prevent data corruption, ensure performance, and handle legacy data migration.

### 1. Item Architecture Fix (Unified Mapping)
*   **Create `items` table:** 
    *   Fields: `id`, `type` (carpet, material), `ref_id` (foreign key to legacy tables).
    *   **Constraint:** `UNIQUE(type, ref_id)` to prevent duplicate mapping.
*   **Reference Integrity:** All `inventory_transactions` must use the `items.id` as their foreign key. No more direct polymorphic links in transaction rows.

### 2. Indexing Strategy (Performance)
*   **INDEX(item_id, warehouse_id):** Required for fast stock calculation and warehouse-specific reporting.
*   **INDEX(reference_type, reference_id):** Required for auditing specific transactions (Sales, Purchases, Washes) and facilitating fast reversals.
*   **INDEX(status):** Required for filtering approved vs. pending inventory movements in dashboards.

### 3. Phase 0.5: Backfill & Data Migration (Legacy Sync)
*   **Goal:** Convert all existing "Balances" into "Audit Trails."
*   **Logic:** A one-time idempotent artisan command that:
    1.  Loops through `carpets` and `material_stocks`.
    2.  Creates an **OPENING** type `inventory_transactions` record for each.
    3.  Sets `direction: IN` and `status: APPROVED`.
    4.  Ensures no duplication if the script is run multiple times.

### 4. Approval Enforcement Rule
*   **Logic:** The system must strictly enforce that **ONLY approved records** (status = 1) affect physical stock counts or trigger accounting entries.
*   **Pending Records:** Are stored in the database for visibility but are **ignored** by balance calculations and the General Ledger.

### 5. Inventory Transfer Logic
*   **Type:** `TRANSFER`.
*   **Behavior:** A single user action generates **two transaction rows**:
    1.  `OUT` from the source warehouse.
    2.  `IN` to the destination warehouse.
    3.  **Note:** Transfers do not trigger accounting entries (as the value remains within the company inventory asset).

### 6. WAC (Weighted Average Cost) Strategy
*   **Recalculation:** WAC is updated automatically on every **Purchase** or **Positive Adjustment**.
*   **Storage:** The current cost is stored at `items.current_cost`.
*   **COGS Usage:** When a sale occurs, the `AccountingService` will fetch this `current_cost` to post the DR COGS, CR Inventory entry.

### 7. Validation & Safety Layer
*   **`InventoryService` Enforcements:**
    *   Prevent negative stock (optional but recommended for accuracy).
    *   Ensure both `item_id` and `warehouse_id` exist before processing.
    *   Prevent duplicate processing of the same `reference_id`.
    *   Validate `quantity > 0` for all movements.

### 8. Concurrency & Locking Rule
*   **Implementation:** Use **`lockForUpdate()`** when reading current stock levels or item balances within a transaction. This prevents "Double-Selling" or "Double-Washing" the same item during high-traffic moments.

### 9. Atomic Transaction Rule (Strict)
*   **Implementation:** ALL operations (Inventory + Accounting) must be wrapped in a single **`DB::transaction(function () { ... })`**.
*   **Reason:** This ensures that if the accounting entry fails, the inventory movement is automatically rolled back by Laravel. No manual `commit` or `rollback` is allowed.

### 10. Orchestration Layer
*   **Introduce `InventoryTransactionManager`:** A service that coordinates both `InventoryService` and `AccountingService`.
*   **Responsibility:** It ensures that both services run in the same database transaction and prevents tight coupling (dependency) between the two services.

---

## Phase 0: Core Engine & Inventory Infrastructure (Audit & Safety)
**Goal:** Build the "Brain" that handles all movements with a perfect paper trail.

### 1. Actions to take:
*   **[NEW] `warehouses` table:** `id`, `name`, `location`, `type`. 
    *   *Rule:* Define "Main Office" as Warehouse ID 1 (Default fallback).
*   **[NEW] `inventory_transactions` table (The Truth):** 
    *   Fields: `id`, `item_id`, `warehouse_id`, `type` (Purchase, Sale, Wash, etc.), **`direction` (IN/OUT)**, `quantity`, `area`, `unit_cost`, `total_cost`, **`reference_type`** (Model), **`reference_id`**, **`status`** (Pending/Approved), **`created_by`** (User ID).
*   **[NEW] `items` (Mapping Table):** Polymorphic link to `carpets` and `material_stocks` for unified reporting.
*   **[NEW] `carpet_processes` (WIP Lifecycle):** Centralized tracking of a carpet's manufacturing cost across all stages.
*   **[NEW] `attachments` (Polymorphic):** Support for scanning and linking receipts/documents to any record.

---

## Phase 1: Accounting Core & Multi-Currency Upgrade
**Goal:** Prepare the ledger for USD/AFN standards and automated sub-ledger mapping.

### 1. Actions to take:
*   **Update `ledger_entries` table:** Add `currency_code` (USD/AFN) and `original_amount`.
*   **Update `AccountingService.php`:**
    *   Enforce `exchange_rate` and `base_currency_amount` (USD) for every transaction.
    *   Standardize sub-ledger linking for Agents, Washing Teams, Weavers, and Vendors.
*   **Standardize Status:** Create `App\Enums\Status` (0:Pending, 1:Approved, 2:Rejected) to be used across all new ERP logic.

---

## Phase 2: The Service Layer (Strict Enforcement & Concurrency)
**Goal:** Ensure no direct DB updates to stock are allowed and prevent race conditions.

### 1. Develop `app/Services/InventoryService.php`:
*   **Strict Rule:** ALL controllers MUST use this service. No direct DB updates to `quantity` or `status` allowed.
*   **Method `processMovement()`:**
    *   Wraps all logic in **`DB::beginTransaction()`**.
    *   Uses **`lockForUpdate()`** on stock rows during processing.
    *   Calculates **Weighted Average Cost (WAC)**.
    *   Updates legacy columns (`quantity`, `total_price`) AND creates `inventory_transactions`.
    *   Calls `AccountingService` automatically.
    *   **Fallback:** Auto-assigns "Main Store" if `warehouse_id` is null.

---

## Phase 3: Module Integration - Purchased Carpets & Yarn (Inventory)
**Goal:** Track stock levels, warehouses, and COA mapping as per Page 1 & 4 of PDF.

### 1. Sidebar Modules: `قالین های خرید شده` (Purchased Carpets) & `تار` (Yarn)
*   **Modify Controllers:** `CarpetsController` (Buy methods) & `PurchaseMaterialController`.
*   **UI Updates:**
    *   Add **Warehouse Selection** dropdown to forms (Default: Main).
    *   Add **Attachment Upload** for purchase invoices/scans.
    *   Show current **Warehouse Balance** (total pieces/area) on the screen.
*   **Backend Logic:** Upon purchase approval: *Debit Inventory Asset (Carpet/Material), Credit Accounts Payable (Seller).*

---

## Phase 4: Module Integration - Kachaee, Washing, & Finishing (WIP)
**Goal:** Accumulate service costs and close the WIP loop as per Page 2 & 3 of PDF.

### 1. Sidebar Modules: `کچایی` (Repair), `شست` (Washing), `تیاری` (Finishing)
*   **Logic:**
    *   **WIP Start:** When sent to service, *Debit WIP Asset, Credit Inventory (Raw).*
    *   **Value Accumulation:** Every service fee is **debited to WIP** instead of being an expense.
    *   **WIP Close (Finishing Done):** *Debit Finished Goods Inventory (Total Cost), Credit WIP (Clear Balance).*
*   **UI Updates:** Add **Account Statements** button to these modules to view service provider balances.

---

## Phase 5: Module Integration - HR, Fixed Assets, & Monthly Expenses
**Goal:** Handle payroll, depreciation, and admin costs as per Page 3, 4, & 5 of PDF.

### 1. Sidebar Modules: `کارمندان دفتر` (HR), `اجناس ثابت شرکت` (Assets), `مصارف ماهانه` (Expenses)
*   **Logic:**
    *   **Fixed Assets:** Track location, price, and annual depreciation. Create scheduled task for journal entries.
    *   **HR:** Link salary payments to "Salary Expense" in COA and affect the Balance Sheet.
    *   **Expenses:** Every office expense now requires a COA category and a scanned attachment.
*   **Multi-Currency:** Support AFN entries while the ledger tracks USD (Base).

---

## Phase 6: Module Integration - Sales & Miscellaneous Accounts
**Goal:** Handle the revenue lifecycle and loan tracking as per Page 5 & 6 of PDF.

### 1. Sidebar Modules: `فروشات` (Sales) & `حساب متفرقه جدید` (Misc Accounts)
*   **Logic:**
    *   **COGS Fix:** When a sale is posted: *DR Accounts Receivable, CR Revenue* AND **DR Cost of Goods Sold, CR Finished Goods Inventory**.
    *   **Sales Inventory:** Automatically `OUT` the serial number via `InventoryService`.
    *   **Misc Accounts:** Support AFN/USD exchange rates for loans (Qarz-ul-Hasana).
*   **UI Updates:** Add **"View Journal Entry"** button to sales invoices.

---

## Phase 7: Reporting, Standardization & Atomic Integrity
**Goal:** Finalize the system with high-level ERP analytics and error safety.

### 1. Actions to take:
*   **Atomic Rollback:** Ensure all Service calls are wrapped in `try/catch`. If `Inventory` fails, `Accounting` MUST rollback.
*   **ERP Reports:** 
    *   **Unified Stock Report:** (Using the `items` mapping table).
    *   **WIP Value Dashboard:** (Showing capital tied up in production).
    *   **Balance Sheet (Multi-currency):** Toggle between USD and AFN views.

---

### 🛑 Verification Checkpoint:
*   [ ] Existing UI still adds carpets/materials correctly.
*   [ ] Legacy reports still show correct balances.
*   [ ] New `inventory_transactions` table is correctly populated with `direction` and `created_by`.

**Final Approval: Ready to begin Phase -1?**
