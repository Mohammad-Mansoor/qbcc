# Frontend Permissions Implementation Checklist

This document tracks the step-by-step implementation of granular Spatie `@can` directives across all frontend Blade views. As each module is completed, it will be marked with a checkbox.

## Implemented Modules (Verified)
- [x] **Agents (`resources/views/agents/`)** - UI buttons wrapped with `create_agent`, `edit_agent`, `manage_agent_accounts`, etc.
- [x] **Users (`resources/views/users/`)** - UI forms/buttons wrapped with `create_user`, `edit_user`, `delete_user`.
- [x] **Customers (`resources/views/customers/`)** - UI forms/buttons wrapped with `create_customer`, `edit_customer`, `create_customer_payment`, `view_customer_statement`.

## Pending Modules (To Do)
### 1. Settings & Administration
- [x] Roles & Permissions (`resources/views/roles/`)
- [x] Activities Log (`resources/views/activities.blade.php`)
- [x] Phone Book (`resources/views/phone-book/`)
- [x] Provinces (`resources/views/provinces/`)
- [x] Carpet Orders/Numbers (`resources/views/carpet-order/`)
- [x] Agent Employees / Workers (`resources/views/agent-employee/`)

### 2. Core Carpet Production
- [x] **Purchased Carpets Report (`/dashboard/purchased-carpets-report`)** - Sidebar and Export buttons wrapped with `view_purchased_carpets_report`, `export_purchased_carpets_excel`, `export_purchased_carpets_pdf`
- [x] **List Buy Carpet (`/dashboard/list-buy-carpet`)** - UI buttons wrapped with `view_buy_carpets`, `create_buy_carpet`, `edit_buy_carpet`
- [x] **Check Book (`/dashboard/check-book`)** - UI buttons wrapped with `view_purchase_bills`, `create_purchase_bill`, `close_purchase_bill`, `print_purchase_bill_pdf`
- [x] **Carpet Types (`/dashboard/carpet-types`)** - UI forms/buttons wrapped with `view_carpet_types`, `create_carpet_type`, `edit_carpet_type`
- [x] **Carpet Qualities (`/dashboard/carpet-qualities`)** - UI forms/buttons wrapped with `view_carpet_qualities`, `create_carpet_quality`, `edit_carpet_quality`

### 3. Raw Materials (مواد خام)
- [x] Material Purchase (`/dashboard/material-purchase`)
- [x] Raw Material Purchase Bills (`/dashboard/raw-material-purchase-bills`)
- [x] Purchase Material Request List (`/dashboard/purchase-material-request-list`)
- [x] Material Sales (`/dashboard/material-sales`)
- [x] Material Sale Request List (`/dashboard/material-sale-request-list`)
- [x] Material Stock (`/dashboard/material-stock`)
- [x] String Seller (`/dashboard/string-seller`)
- [x] String Seller Statement (`accounting.reports.string_seller_statement`)
- [x] String Seller Request List (`/dashboard/string-seller-request-list`)
- [x] Material Category (`/dashboard/material-category`)
- [x] Material Types (`/dashboard/materialtypes`)

### 4. Production Stages (Batches)
- [x] Kachaee & Kachaee Teams (`resources/views/carpet-repair/`, `kachaee/`, `batches/kachaee/`)
- [x] Washing & Washing Teams (`resources/views/carpet-wash/`, `washing/`, `batches/wash/`)
- [x] Finishing & Finishing Teams (`resources/views/finishing-center/`, `finish-team/`, `batches/finish/`)

### 5. Sales & Orders
- [x] Sales & Invoices (`resources/views/sales/`, `invoices/`)
- [x] Customer Orders (`resources/views/customer-orders/`)

### 6. Finance & Accounting
- [x] Different Accounts (`resources/views/different-account/`, `new-different-account/`)
- [x] Monthly Expenses (`resources/views/new-monthly-expense/`)
- [x] Asset Accounts (`resources/views/assets-accounts/`)
- [x] Employee Payroll (`resources/views/payroll/`)
- [x] Office Employees & Departments (`resources/views/office-employee/`, `office-employee-department/`)
- [x] Financial Statements & Reports (`resources/views/Reports/`)
