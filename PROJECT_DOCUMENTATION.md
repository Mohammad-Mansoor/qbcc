# Project Documentation: Carpet Management System

## A. Project Overview
This project is a comprehensive **Carpet Manufacturing and Sales Management System** built on the Laravel framework. It is designed to handle the entire lifecycle of carpet production—from material procurement and agent contracting to manufacturing stages (washing, repairing, finishing) and finally to stock management and sales.

**Purpose of the System:**
The system provides a centralized platform for managing a carpet business, including production tracking, financial accounting with multi-currency support (USD and AFN), material stock management, and detailed reporting. It aims to streamline operations and provide real-time insights into the production pipeline and financial health of the business.

**Main Features:**
*   **Production Workflow Tracking:** Tracks carpets through various stages: Contract, Weight, Purchase, Wash, Repair, Finishing, and Stock.
*   **Agent & Contractor Management:** Manages relationships with agents and various specialized teams (Washing, Finishing, Kachaee).
*   **Financial Accounting:** Multi-currency cash books, payment and receipt tracking, and expense management.
*   **Material Inventory:** Tracking material categories, stocks, purchases, and sales.
*   **Sales & Invoicing:** Managing customers, sales orders, and generating invoices and packing lists.
*   **Comprehensive Reporting:** Real-time balance reports for agents, customers, teams, and overall business performance.
*   **Role-Based Access Control:** Fine-grained permissions using user types (Super Admin, Sales Officer, Central Office, etc.).

---

## B. Technology Stack
*   **Backend Framework:** Laravel 6.x (running on PHP 7.2+)
*   **Frontend UI:** Bootstrap 4.x & jQuery
*   **Templating Engine:** Laravel Blade
*   **Database:** MySQL
*   **Key PHP Packages:**
    *   `morilog/jalali`: For Persian/Jalali date support.
    *   `spatie/laravel-activitylog`: For tracking system activities.
    *   `owen-it/laravel-auditing`: For model auditing.
    *   `laravel/ui`: For authentication scaffolding.
*   **Development Tools:** Laravel Mix (for asset compilation).

---

## C. Project Architecture
The project follows the standard **Model-View-Controller (MVC)** architecture provided by Laravel:

1.  **Request Handling:** Requests enter the application via `public/index.php`, pass through the `web` middleware group (including CSRF protection and role-based `usertype` check), and are routed to the appropriate controller.
2.  **Logic (Controllers):** Controllers in `app/Http/Controllers/` handle the incoming request, interact with the models, and return the appropriate views. The project uses a role-based structure where different users access different routes and features based on their level of access.
3.  **Data (Models):** Models located in `app/` define the data structures and relationships. They encapsulate the business logic for specific entities like `Carpet`, `Agent`, `Payment`, etc.
4.  **Presentation (Views):** Blade templates in `resources/views/` generate the HTML. They are organized into folders reflecting the module structure (e.g., `carpets/`, `agents/`, `sales/`).

---

## D. Folder Structure Explanation
*   `app/`: Contains the core backend logic.
    *   `Http/Controllers/`: Application controllers.
    *   `Models/`: Eloquent models (directly in `app/` according to Laravel 6 convention).
    *   `Http/Middleware/`: Custom middleware including `UserType` checks.
*   `routes/`: Routing system.
    *   `web.php`: Primary web routes organized by user role.
    *   `api.php`: API endpoints (if used).
*   `resources/views/`: Blade templates for the UI.
    *   `layouts/`: Master layout files (e.g., `app.blade.php`).
*   `public/`: Publicly accessible assets (compiled CSS/JS, images).
*   `database/`: Database-related files.
    *   `migrations/`: Schema definitions (60+ migrations).
    *   `seeds/`: Initial data population.
*   `config/`: Configuration files for the application and packages.
*   `storage/`: Logs, compiled views, and file uploads.

---

## E. Module Breakdown

### 1. Authentication & User Management
Handles user logins, registrations (if enabled), and role-based access. Roles (User Types) determine what parts of the dashboard and features are visible to the user.

### 2. Dashboard
The nerve center of the application, providing real-time stats on:
*   Cash book balances across different office roles.
*   Carpet stock levels and manufacturing statuses.
*   Financial summaries (Total receipts vs. total payments).

### 3. Carpet Lifecycle Management
The primary module for the business:
*   **Contracts:** Initializing carpet production with agents.
*   **Manufacturing Stages:** Tracking carpets through washing teams, repair teams (Kachaee), and finishing teams.
*   **Inventory:** Managing the "Ready to Sale" stock.

### 4. Material Management
Tracks the raw materials used in carpet production:
*   Categories (e.g., Wool, Silk, Yarn).
*   Purchasing from sellers and selling materials to agents if necessary.

### 5. Finance & Accounting
Advanced accounting module featuring:
*   **Office Cash Books:** Tracking cash flow for Sales, Central, and Super Admin offices.
*   **Payments:** Recording "Receipts" (Rasid) and "Give" (Geraft) for agents, customers, and teams.
*   **Multi-Currency:** Automatic conversion based on a defined currency rate (USD/AFN).

### 6. Sales & Inventory
Manages the end-of-pipeline operations:
*   Creating sales and invoices.
*   Generating packing lists and packages for shipment.

---

## F. Database Design Overview
The database consists of over 60 tables, with major relationships including:
*   **Carpets Table:** The central table, linked to `agents`, `carpet_orders`, `carpet_types`, and `qualities`.
*   **Payments:** Separate tables for different entities (`agent_payments`, `customer_payments`, `employee_payments`) for cleaner tracking.
*   **Agents & Teams:** Models representing the workforce, linked to their respective carpets and payment history.
*   **Accounting:** `office_cash_books` and `expense_details` track the financial state.

---

## G. How to Run the Project Safely

### Prerequisites
*   PHP ^7.2
*   Composer
*   Node.js & NPM
*   MySQL Server

### Installation Steps
1.  **Clone/Extract Project:** Ensure you are in the project root directory.
2.  **Install Dependencies:**
    ```bash
    composer install
    npm install
    ```
3.  **Environment Setup:**
    ```bash
    cp .env.example .env
    # Edit .env and configure your database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
    ```
4.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```
5.  **Database Migration:**
    ```bash
    # WARNING: Do not use --force in production. Ensure database exists.
    php artisan migrate
    ```
6.  **Run Development Server:**
    ```bash
    php artisan serve
    ```
7.  **Access the Application:** Open `http://localhost:8000` and login with your credentials.

---

## H. Safe Development Guidelines
*   **Blade Templates:** Do not modify the `layouts/app.blade.php` without understanding how `@yield('content')` affects the pages. Use `@section` and `@extends` in child views.
*   **Controllers:** The controllers contain significant business logic. When modifying, ensure you maintain the status transitions of carpets (e.g., transitioning from status 5 to 6) to avoid breaking dashboard statistics.
*   **Routing:** Always keep the role-based middleware (`usertype:XX`) in mind when adding new routes to ensure security.
*   **Database:** Use migrations for schema changes. Avoid manual SQL changes in production. Always test migrations on a local copy first.
*   **Localization:** The project uses Jalali dates. Ensure you use the provided helpers for date transformations to maintain consistency in reporting.
*   **CRITICAL:** Do not delete files in the `root` directory (like `jalali/`, `iransans/`) as they might contain custom assets or legacy libraries required by the system.

---

## I. Security & Best Practices
*   **CSRF Protection:** All forms must include the `@csrf` directive.
*   **Validation:** Controllers use Laravel's validation or manual checks. Ensure all user input is validated before being processed in DB operations.
*   **Middleware:** The `usertype` middleware is used throughout `web.php` to enforce role-based access. Ensure new features are correctly assigned to the relevant user groups.
*   **Auditing:** Model auditing is implemented for key entities; preserve this to maintain a trail of changes for sensitive data like financial payments.

### to start the xamp in ubuntu
sudo /opt/lampp/lampp start


### to start project
/opt/lampp/bin/php -S 127.0.0.1:8000 server.php

