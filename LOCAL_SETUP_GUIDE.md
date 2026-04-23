# Qasimi Brother Carpet Company - Local Setup Guide

This document provides a comprehensive guide for developers to run this Laravel 6 project on a local development environment. Because the project was originally hosted directly on a shared Hostinger environment, it requires specific configurations to run perfectly on a local machine.

---

## 1. Prerequisites
**CRITICAL:** This application is built on **Laravel 6**. It will **crash on PHP 8.x** with a `ReflectionParameter::getClass()` Fatal Error.

- **PHP Version required:** PHP 7.3 or PHP 7.4.
- **Local Server:** XAMPP 7.4 (Highly recommended for Linux/Windows environments).
- **Composer:** Composer 2.x is supported *only* if the Laravel framework is updated to `v6.20`.

---

## 2. Step-by-Step Installation

### Step 1: Database Setup
1. Open your XAMPP MySQL interface or run the terminal command:
   ```bash
   /opt/lampp/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS u104987031_dashboard;"
   ```
2. Open the `.env` file in the project root and ensure it connects to your local XAMPP credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u104987031_dashboard
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Step 2: Install Dependencies
Run composer install using your PHP 7.4 binary. If you get `Undefined index: name` errors, run an update with all dependencies to ensure Composer 2 compatibility:
```bash
/opt/lampp/bin/php $(which composer) update laravel/framework -W
```

### Step 3: Run Migrations and Seed the Database
Run the migrations to create the 64+ tables:
```bash
/opt/lampp/bin/php artisan migrate
```
Populate the database with the default categories, currencies, and admin user:
```bash
/opt/lampp/bin/php artisan db:seed
```

### Step 4: Start the Local Server
Because the original `public/` directory was moved to the root to accommodate Hostinger's shared hosting architecture, **DO NOT use `php artisan serve`**. It will result in 404 errors for your CSS and JS files because it hardcodes the document root to `/public`.

Instead, start the PHP built-in server manually using `server.php` as the router:
```bash
/opt/lampp/bin/php -S 127.0.0.1:8000 server.php
```

### Step 5: Log In
Visit `http://127.0.0.1:8000` in your browser. 
Use the default administrator credentials created by the database seeder:
- **Email:** `qbc1@live.com`
- **Password:** `Qbcc22000@1500af`

---

## 3. Known Issues & Troubleshooting

### Issue 1: Fatal Error: `ReflectionParameter::getClass() is deprecated`
- **Cause:** You are trying to run the project using PHP 8.0 or newer. Laravel 6 is not compatible.
- **Solution:** Downgrade your XAMPP installation to version 7.4. Make sure you use the `/opt/lampp/bin/php` absolute path when running commands in the terminal so it uses XAMPP's PHP instead of the system's global PHP 8.

### Issue 2: 404 Errors on CSS / JS files or Missing `index.php`
- **Cause:** The `public/` folder is empty. Hostinger serves from `public_html` directly, so the previous developer moved all assets to the root.
- **Solution:** `server.php` has been permanently updated to route to the root `index.php` instead of `public/index.php`. To ensure CSS loads, always start the server manually using `/opt/lampp/bin/php -S 127.0.0.1:8000 server.php`.

### Issue 3: `Table 'customer_order_details' doesn't exist`
- **Cause:** The original developer created the `customer_order_details` table directly in the production database and forgot to write a Laravel migration file for it.
- **Solution:** A new migration file (`2026_04_21_000000_create_customer_order_details_table.php`) was manually reverse-engineered and added to the project. Running `php artisan migrate` will now successfully build this table locally.

### Issue 4: 403 Forbidden Access on XAMPP (Linux)
- **Cause:** If you choose to host the project directly inside `/opt/lampp/htdocs` using a symlink, XAMPP's Apache user (`daemon`) is blocked by Linux from reading files inside your personal `/home` directory.
- **Solution:** Give read/execute permissions to your directories:
  ```bash
  chmod 755 /home/username
  chmod 755 /home/username/projects
  chmod 755 /home/username/projects/public_html
  chmod -R 777 /home/username/projects/public_html/storage
  chmod -R 777 /home/username/projects/public_html/bootstrap/cache
  ```
