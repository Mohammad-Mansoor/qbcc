# Step-by-Step Execution Plan: Enterprise Accounting Engine Integration

Based on a thorough review of your existing Laravel architecture, controllers, and migrations, this plan outlines the exact, sequential path to integrate a complete Double-Entry system **while preserving your existing UI and tables**. 

Your existing entities that will act as Sub-Ledgers (and their respective controllers) are:
1. **Customers** (`CustomerPaymentController`, `SaleController`, `InvoiceController`)
2. **Agents** (`AgentPaymentController`, `AgentsCarpetController`)
3. **Washing Teams** (`WashingPaymentController`, `CarpetWashController`)
4. **Finishing Teams** (`FinishingTeamPaymentController`, `FinishingWorkController`)
5. **String Sellers / Vendors** (`SellerPaymentController`, `PurchaseMaterialController`)
6. **Employees** (`EmployeePaymentController`, `EmployeeSalaryController`)
7. **Cash/Office Books & Expenses** (`OfficeCashBookController`, `MonthlyExpenseController`, `OfficeDebitController`, `OfficeCreditController`)
8. **Custom Accounts** (`DifferentAccountController`, `AjnasAccountController`)

---

## Phase 1: Foundation (Database Schema & Models)

**Goal:** Lay down the new, isolated database structure without touching existing tables yet. We will build the new tables specifically designed to hook into your existing entity models polymorphically.

### 1. Actions to take:
*   Create migrations for the new core accounting tables in this specific order:
    1.  `fiscal_periods` (To lock closed periods)
    2.  `cost_centers` (To track branch/project profitability)
    3.  `chart_of_accounts` (Standard 5-type structure: Asset, Liab, Equity, Rev, Exp)
    4.  `ledger_transactions` (The Journal header)
    5.  `ledger_entries` (The double-entry lines, utilizing `party_type` and `party_id` to link directly to your existing `Customer`, `Agent`, `WashingTeam`, etc. models)
    6.  `audit_logs` (To track who posted/reversed what)
*   Create the corresponding Eloquent Models and define their relationships.

### 🛑 Confirmation Checkpoint 1
*   Run `php artisan migrate`. Ensure no existing tables break.
*   Verify model relationships in `php artisan tinker`.

---

## Phase 2: Core Accounting Engine Development

**Goal:** Build the strict logic layer (`AccountingService`) that acts as the financial gatekeeper for all your existing controllers.

### 1. Actions to take:
*   Create `app/Services/AccountingService.php`.
*   Implement `postTransaction()`:
    *   Validates `Debit == Credit`.
    *   Converts foreign currency (USD) to Base Currency (AFN or vice-versa).
    *   Automatically assigns the correct Sub-Ledger (e.g., `party_type = 'App\Models\Customer'`, `party_id = 15`).
*   Implement `reverseTransaction()` to handle voids without deleting DB rows.

### 🛑 Confirmation Checkpoint 2
*   Test posting a manual journal via Tinker to ensure the `AccountingService` strictly rejects unbalanced entries and correctly calculates the `base_debit` and `base_credit`.

---

## Phase 3: Bridging Old Tables to the New Core

**Goal:** Prepare your existing transaction tables (Sales, Payments) to hold a reference to the accounting core so the UI can provide a "View Journal Entry" button.

### 1. Actions to take:
*   Create a migration: `add_ledger_links_to_transaction_tables`.
    *   Add `ledger_transaction_id` (nullable) to all transaction tables: `sales`, `customer_payments`, `agent_payments`, `washing_payments`, `seller_payments`, `finishing_team_payments`, `employee_payments`, `office_credits`, `office_debits`.
*   *(Note: We do NOT need to add `account_id` to the entity tables directly because `ledger_entries` uses polymorphic `party_type`/`party_id` to link to them, saving schema space).*

### 🛑 Confirmation Checkpoint 3
*   Run the migration. Ensure your existing app UI (Adding a customer payment, making a sale) still works perfectly without crashing.

---

## Phase 4: UI Updates & Accounting Screens

**Goal:** Build the interfaces required to manage the new system, keeping the old screens intact.

### 1. Actions to take:
*   **Chart of Accounts UI**: A grid to Create/Edit accounts (e.g., 1100 Cash, 1300 AR, 4100 Sales, 5000 COGS, 6000 Salary).
*   **Journal Entry UI**: A screen for manual journal vouchers (e.g., Depreciation, manual adjustments) that allows selecting multiple Debit/Credit lines.
*   **Existing Screens Update**: On the existing `CustomerPayment` or `Sale` view pages, add a small panel showing the generated Accounting Journal Entry.

### 🛑 Confirmation Checkpoint 4
*   Ensure the new routes and UI are accessible and users can successfully create a COA and a manual Journal Voucher.

---

## Phase 5: Controller Refactoring & Live Integration

**Goal:** Force all new actions in the software to use the `AccountingService`. Your existing UI forms remain the same, but the backend controller does more work.

### 1. Actions to take:
*   Update `SaleController`: After saving the sale, call `AccountingService` to Debit AR (Customer sub-ledger) and Credit Revenue. Save the returned `ledger_transaction_id` to the sale.
*   Update `CustomerPaymentController`: Debit Cash, Credit AR.
*   Update `SellerPaymentController` (Vendors): Debit AP (Seller sub-ledger), Credit Cash.
*   Update `WashingPaymentController`, `AgentPaymentController`, `FinishingTeamPaymentController`: Debit specific Expense Accounts, Credit AP or Cash.
*   Update `OfficeDebit/CreditController`: Debit/Credit the specific Cash/Bank accounts.
*   Wrap all these controller actions in `DB::transaction()`.

### 🛑 Confirmation Checkpoint 5
*   **Manual UI Testing**: Log into the application. Create a new Sale. Make a Customer Payment. Make a Seller Payment. Verify that `ledger_transactions` and perfectly balanced `ledger_entries` are automatically generated behind the scenes.

---

## Phase 6: Chart of Accounts Seeding & Data Migration (CRITICAL)

**Goal:** Migrate the historical balances into the new double-entry engine so you don't lose past data.

### 1. Actions to take:
*   Write an Artisan Command (`php artisan finance:migrate-balances`).
*   Loop through all existing entities (Customers, Agents, Washing Teams, String Sellers).
*   Calculate their current "Outstanding Balance".
*   Post these balances through the `AccountingService` as an `opening_balance` journal entry.

### 🛑 Confirmation Checkpoint 6
*   Run the command on a backup database. Pick 3 random Vendors and 3 Customers. Compare their old "Outstanding Balance" on the existing UI with their new Sub-ledger balance in the accounting engine. **They must match exactly.**

---

## Phase 7: Financial Reporting Engine

**Goal:** Generate the required IFRS/GAAP reports reading *only* from the Ledger.

### 1. Actions to take:
*   Build UI and logic for **Trial Balance** (Grouped by Account, summing debits and credits).
*   Build **Profit & Loss** (Filtering `account_type` = Revenue/Expense with Date ranges).
*   Build **Balance Sheet** (Assets = Liabilities + Equity).

### 🛑 Confirmation Checkpoint 7
*   Generate the reports. The Net Profit shown on the P&L must equal the change in Equity on the Balance Sheet. The Trial Balance must always net to zero.

### Let's start Phase 1 of the finance plan