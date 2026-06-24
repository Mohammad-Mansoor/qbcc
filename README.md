# QBIC Carpet Manufacturing ERP

A production-grade, forensic-first Enterprise Resource Planning (ERP) system designed specifically for the carpet manufacturing industry. This system integrates advanced double-entry accounting, real-time multi-currency (USD/AFN/EUR/PKR) normalization, weighted average cost (WAC) inventory tracking, and warehouse lifecycle operations.

---

## 🏛 The 5 Core Pillars

This application is built upon a non-destructive, "Wrap and Extend" architecture that enforces absolute operational integrity across five essential functional layers:

### 1. Finance (Automatic GL Posting)
- Core business events (sales, purchases, returns, payments, wages, repair expenses) automatically compile and dispatch balanced journal entries directly to the General Ledger (GL).
- Prevents transaction drift between physical operations and financial ledgers.

### 2. Accounting (Accurate Double-Entry Mapping)
- **Immutable Ledger**: Once a ledger transaction is set to `posted`, it becomes completely immutable.
- **Append-only Reversals**: To correct errors, delete/edit actions write compensating journal records prefixed with `REV-%`. These transactions automatically swap debits and credits, preserving the full forensic audit trail.
- **Decimal Precision**: All currency calculations are normalized and held at a decimal precision of `19.4` via database settings and Laravel BCMath functions.

### 3. Inventory (Real-Time Stock & WAC Valuation)
- Moving carpets between production stages, repairs, or warehouse locations automatically recalculates valuations using a strict Weighted Average Cost (WAC) model.
- Integrated inventory transaction logs tie directly to financial asset entries.

### 4. Warehouse (Location Tracking & Packaging)
- Supports multi-warehouse storage transfers, carpet packaging workflows (assigning carpets to physical packages), and state transitions from raw manufacturing to ready-for-sale.

### 5. Multi-Currency (USD Normalization)
- Transactions can be posted in multiple currencies (AFN, USD, EUR, PKR).
- The system captures a forensic exchange rate snapshot (`exchange_rate`) at the moment of the transaction, normalizing all values to the base currency (USD) for unified reporting.
- Reversal entries automatically clone the original transaction's historical rate, preventing FX gain/loss contamination.

---

## 🛠 Features

- **Dashboard**: Glassmorphic, high-density dashboard tracking real-time asset balances, revenue, expenses, and transaction logs.
- **Forensic Cash Flow Report**: Dynamic indirect statement calculating operating, investing, and financing flows, reconciled directly against general ledger cash accounts with zero-drift protection.
- **Comparative Profit & Loss (P&L)**: Fully categorized revenue, cost of goods sold (COGS), and expense reports, comparing performance across periods.
- **Audit Corrections Log**: Monitor all compensating reversal transactions. Click any Trans ID to open a side-by-side visual drilldown of the original journal entries and the reversing compensating entries.
- **Cost Center Reports**: Track revenues and expenses allocated directly to specific manufacturing projects or departments.

---

## 💻 Local Installation & Setup

### Requirements
- **PHP**: `7.4` (Strict requirement; Laravel 6 is incompatible with newer PHP versions like PHP 8.x)
- **Web Server**: Apache / Nginx (or local XAMPP stack)
- **Database**: MySQL / MariaDB

### Steps
1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Mohammad-Mansoor/QBIC.git
   cd QBIC
   ```

2. **Configure Environment File**:
   Copy `.env.example` to `.env` and configure your database parameters:
   ```bash
   cp .env.example .env
   ```

3. **Install Dependencies**:
   Ensure you use Composer compatible with PHP 7.4:
   ```bash
   composer install
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations and Seeders**:
   This sets up the system schema and populates default accounts, roles, and settings from scratch:
   ```bash
   php artisan migrate --seed
   ```

6. **Create Storage Link**:
   ```bash
   php artisan storage:link
   ```

7. **Clear Cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

8. **Serve the Application**:
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your web browser.

---

## 🚀 Hostinger Subdomain Deployment Guide

When deploying to a Hostinger testing subdomain, follow this step-by-step procedure to ensure a secure, zero-dependency fresh start.

### Step 1: Clean and Pack Locally
1. Clear all local configs and caches using the PHP 7.4 path:
   ```bash
   /opt/lampp/bin/php artisan config:clear
   /opt/lampp/bin/php artisan cache:clear
   /opt/lampp/bin/php artisan view:clear
   ```
2. Compress your project folder into `project.zip`. Do not include node_modules or large local logs.

### Step 2: Set Up Subdomain on Hostinger hPanel
1. Navigate to **Domains** -> **Subdomains**.
2. Create your subdomain (e.g., `test`).
3. **CRITICAL**: Check the box for "Custom folder for subdomain" and set it to the `public` directory:
   - Path: `/public_html/test/public`
   - *Why?* Laravel applications enter through `public/index.php`. Exposing the root folder directly exposes your secret `.env` file to the web.

### Step 3: Create a Fresh MySQL Database
1. Go to **Databases** -> **MySQL Databases** on hPanel.
2. Create a new Database name, User, and strong Password. Save these details.

### Step 4: Upload and Extract Files
1. Open the **File Manager** and navigate to your subdomain folder (e.g., `public_html/test`).
2. Upload `project.zip` and extract it into that directory.
3. Once completed, delete `project.zip` to free disk space.

### Step 5: Configure the `.env` File
1. Edit the `.env` file in the subdomain root directory.
2. Update the credentials to match your Hostinger configurations:
   ```env
   APP_ENV=production
   APP_DEBUG=true  # (Set to false after testing is complete)
   APP_URL=https://test.yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u123456789_your_db_name
   DB_USERNAME=u123456789_your_db_user
   DB_PASSWORD=your_strong_password
   ```

### Step 6: Finalize Server Configuration via SSH
1. Enable SSH access in hPanel (**Advanced** -> **SSH Access**) and log in via your terminal:
   ```bash
   ssh -p 65002 u123456789@your_hostinger_ip
   ```
2. Navigate to your project folder:
   ```bash
   cd domains/yourdomain.com/public_html/test
   ```
3. Run key generation, migrations, and storage links. (If default `php` resolves to PHP 8.x, prefix commands with the Hostinger PHP 7.4 binary path, e.g., `/opt/alt/php74/usr/bin/php`):
   ```bash
   # Generate application security key
   php artisan key:generate

   # Clear any leftover cached configs
   php artisan config:clear

   # Run fresh migrations and seed initial data
   php artisan migrate:fresh --seed

   # Create symlink for file uploads
   php artisan storage:link
   ```

---

## 🔒 Forensic Controls & System Auditing

To maintain corporate accounting integrity, developers and auditors must respect the following runtime constraints:
- **Locked Fiscal Periods**: The `AccountingService` monitors closed periods. Any attempt to write or reverse a transaction within a locked period will throw an exception (`The selected date falls within a closed fiscal period.`).
- **Immutable Transactions**: Modification of a posted transaction directly inside the database is strictly prohibited. The `LedgerTransaction` model contains observer hooks that block direct SQL updates or deletions on posted elements.
- **Traceability**: All corrections log the original transaction ID and match them against the `activities` and `sales` tables to trace the operator who authorized the reversion.
