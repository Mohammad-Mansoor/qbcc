# Detailed Implementation Plan: Financial Accounting System (Double-Entry)

## 1. Project Background
Based on the review of the current system, the application manages a sophisticated carpet production chain involving multiple entities (Agents, Washing Teams, Finishing Teams, String Sellers, and Customers). Currently, financial movements are recorded as simple credits and debits in isolated tables (e.g., `washing_payments`, `customer_payments`).

This plan details the upgrade to a **Full Double-Entry Accrual Accounting System** as per IFRS/GAAP standards.

---

## 2. Core Modules & Accounting Hooks

To integrate successfully with the existing logic, the following hooks will be created:

### A. Inventory Management (Carpet & Material Stock)
*   **Action**: When a carpet is purchased/manufactured.
*   **Hook**: 
    *   Debit: `Inventory (1400)`
    *   Credit: `Accounts Payable (2100)` or `Cash (1100)`

### B. Production Expenses (Washing, Finishing, Kachaee)
*   **Action**: When a service payment is approved in `WashingPaymentController` or `FinishingTeamPaymentController`.
*   **Hook**: 
    *   Debit: `Direct Manufacturing Cost - Washing (5000)`
    *   Credit: `Accounts Payable / Team Balance (2100)`

### C. Sales & Receivables
*   **Action**: When a sale is recorded in `SaleController`.
*   **Hook**: 
    *   Debit: `Accounts Receivable (1300)`
    *   Credit: `Sales Revenue (4100)`

---

## 3. Database Schema: Detailed Design

### New Core Tables

#### 1. `chart_of_accounts`
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INT (PK) | Internal ID |
| `account_code` | VARCHAR | e.g., 1100, 2100 |
| `account_name` | VARCHAR | e.g., Cash, Washing Expense |
| `account_type` | ENUM | Asset, Liability, Equity, Revenue, Expense |
| `report_group` | VARCHAR | Current Asset, Fixed Asset, COGS, etc. |
| `currency` | VARCHAR | USD, AFN |
| `is_cash_account` | BOOLEAN | For Cash Flow statement mapping |

#### 2. `ledger_transactions` (The Transaction Master)
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INT (PK) | Transaction ID |
| `journal_id` | INT | Optional link to Journal Book |
| `date` | DATE | Posting date |
| `reference` | VARCHAR | Invoice No, Payment ID, etc. |
| `description` | TEXT | Transaction notes |
| `source_type` | VARCHAR | e.g., 'WashingPayment', 'Sale' |
| `source_id` | INT | ID of the record in the original table |

#### 3. `ledger_entries` (The Double Entry Lines)
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INT (PK) | Entry ID |
| `transaction_id` | INT (FK) | Link to `ledger_transactions` |
| `account_id` | INT (FK) | Link to `chart_of_accounts` |
| `debit` | DECIMAL(15,2)| Debit amount in local/USD currency |
| `credit` | DECIMAL(15,2)| Credit amount |
| `exchange_rate`| DECIMAL(10,4)| Rate at time of transaction |
| `project_class`| VARCHAR | To track project-specific costs |

---

## 4. Specific Database Changes

### A. New Tables to be Created
These tables form the bedrock of the new accounting system:

1.  **`chart_of_accounts`**: Stores the hierarchy and definitions of all accounts (Asset, Liability, etc.).
2.  **`ledger_transactions`**: The master header for every accounting event (contains date, reference, description).
3.  **`ledger_entries`**: The granular double-entry lines (Debits and Credits) for every transaction.
4.  **`accounting_mapping_rules`**: Rules used by the system to automate postings from different controllers.
5.  **`fiscal_periods`**: To manage financial years and lock periods after closing.

### B. Existing Tables Requiring Modifications
The following tables must be updated with new columns to link existing data to the new financial core:

| Table Name | Changes Required | Purpose |
| :--- | :--- | :--- |
| **All Entity Tables** | Add `accounting_account_id` | Links a specific Customer, Agent, or Vendor to their sub-ledger in the COA. |
| (**customers, agents, washing_teams, finishing_teams, string_sellers, office_employees**) | | |
| **All Transaction Tables** | Add `ledger_transaction_id` | Links a payment or sale directly to its corresponding accounting entry. |
| (**sales, invoices, agent_payments, washing_payments, customer_payments, employee_payments, etc.**) | | |
| **`office_cash_books`** | Add `chart_of_account_id` | To specifically link different cash boxes (Sales/Central) to their COA counterpart. |

---

## 5. Data Migration Confirmation
**Yes, the currently available data can be successfully migrated to the new structure.**

### How the Migration will Work:
1.  **COA Initialization**: First, we will create the default accounts based on your FRS.
2.  **Entity Mapping**: Every entry in your `customers`, `agents`, and `teams` tables will automatically have a corresponding account created in the `chart_of_accounts`.
3.  **Beginning Balances**: We will run a script to calculate the current "Outstanding Balance" for every entity and post it as a "Beginning Balance" entry in the new Ledger.
4.  **Historical Records**: We can reconstruct the ledger history by processing all existing `Approved (status=1)` payments. For every payment in your current database, we will generate a matching set of `ledger_entries` so that your new Trial Balance matches your current manual records.

---

## 6. Why These Changes are Needed

### Phase 1: Setup & Initialization (3 Days)
*   Deploy new migrations.
*   Initialize the **Chart of Accounts** with the standard template (Assets, Liabilities, etc.).
*   Update UI for Account Management.

### Phase 2: Accounting Engine Development (4 Days)
*   Create an `AccountingService` class in Laravel.
*   Implement `post($data)` method that handles atomic transactions (ensures Debit = Credit).
*   Implement Approval logic: Entries are only posted when the underlying record (e.g., a payment request) is marked as `Approved (status=1)`.

### Phase 3: Controller Hooking (5 Days)
*   **Vendors**: Update `SellerPaymentController` to post to Ledger.
*   **Washing/Expenses**: Update `WashingPaymentController` and `OfficeDebitController` to update the Cash Book and Expense Accounts.
*   **Customers**: Update `SaleController` and `InvoiceController` to record Revenue and Receivables.

### Phase 4: Reporting & Dashboard (4 Days)
*   Build SQL views for **Trial Balance**.
*   Develop the **Income Statement (P&L)** logic.
*   Develop the **Balance Sheet** (Assets = Liabilities + Equity).
*   Implement **Indirect Cash Flow Statement**.

---

## 6. Output & Verification

*   **Trial Balance Output**: A live table where Total Debits must always equal Total Credits.
*   **Audit Trail**: Every transaction recorded in the system (Washing Payment, Carpet Sale, Material Purchase) will have a "View Accounting" button to see the underlying Journal Entry.
*   **Zero-Balance Check**: System-wide check that prevents saving any transaction that doesn't balance.

## 7. Estimated Completion Time
*   **Total Duration**: **16 to 18 Working Days**.
*   **Complexity Level**: High (Requires careful migration of existing balances into the new COA).
