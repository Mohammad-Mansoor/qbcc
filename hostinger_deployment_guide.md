# Hostinger Deployment Guide (Testing Subdomain)

This guide will walk you through deploying your Laravel application to a Hostinger subdomain from scratch. Since you want everything to be "created from zero," we will set up a fresh database and run the migrations/seeders directly on the server.

---

## Step 1: Prepare Your Files Locally
Before uploading anything, we need to prepare the project folder.

1. **Clear Caches:** In your local terminal, run these commands to ensure no local paths are cached:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```
2. **Zip the Project:** Compress your entire `public_html` folder into a zip file (e.g., `project.zip`). 
   * **Why?** Uploading a single ZIP file to Hostinger is hundreds of times faster than uploading thousands of individual PHP files.

---

## Step 2: Create the Subdomain on Hostinger
We need a dedicated URL for your testing environment.

1. Log in to your **Hostinger hPanel**.
2. Go to **Domains** -> **Subdomains**.
3. In the "Subdomain" field, type your desired prefix (e.g., `test` or `finance`).
4. **Important (Custom Folder):** Check the box for "Custom folder for subdomain".
5. Set the folder path to exactly point to the `public` directory of your Laravel app. For example: `/public_html/test/public`.
   * **Why?** Laravel's entry point is the `public/index.php` file. If you don't point the domain to the `public` folder, users will see your core system files (like `.env`), which is a massive security risk.

---

## Step 3: Create a Fresh Database
Since you want the system to be created from zero, we need an empty database.

1. Go to **Databases** -> **MySQL Databases** in hPanel.
2. Enter a **Database Name**, **MySQL Username**, and **Password**.
3. Click **Create**.
4. **Important:** Copy and save the Database Name, Username, and Password in a notepad. We will need them in Step 5.

---

## Step 4: Upload and Extract Files
Now we put your code onto the server.

1. Go to **Files** -> **File Manager**.
2. Navigate to the folder you created for the subdomain (e.g., `public_html/test`).
3. Click the **Upload** button (usually an arrow pointing up at the top right) and upload your `project.zip` file.
4. Once uploaded, right-click `project.zip` and select **Extract**. Enter the current directory (`/public_html/test`) as the destination.
5. After extraction, you can delete `project.zip` to save space.

---

## Step 5: Configure the Environment File (`.env`)
The `.env` file tells your code how to connect to the new Hostinger database.

1. In the File Manager, inside your `test` folder, look for the `.env` file. (If you don't see it, look for `.env.example`, copy it, and rename the copy to `.env`).
2. Right-click `.env` and select **Edit**.
3. Change the following lines:
   ```env
   APP_NAME="Qalam Testing"
   APP_ENV=production
   APP_KEY= # (Leave this as is, or we will generate it later)
   APP_DEBUG=true  # (Keep true for now so you can see error details if something breaks during testing)
   APP_URL=https://test.yourdomain.com # (Change to your actual subdomain)

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u123456789_your_db_name   # (Paste from Step 3)
   DB_USERNAME=u123456789_your_db_user   # (Paste from Step 3)
   DB_PASSWORD=your_strong_password      # (Paste from Step 3)
   ```
4. Click **Save**.

---

## Step 6: Server Configuration via SSH (The "From Zero" Setup)
To build the database tables from zero, we need to run Laravel commands on Hostinger via SSH.

1. **Enable SSH:** Go to **Advanced** -> **SSH Access** in hPanel and ensure SSH is enabled. Note the SSH IP, Port, Username, and Password.
2. Open a terminal on your Linux computer and connect to Hostinger:
   ```bash
   ssh -p 65002 u123456789@your_hostinger_ip
   ```
3. Once logged in, navigate to your project folder:
   ```bash
   cd domains/yourdomain.com/public_html/test
   ```
4. **Verify PHP Version:** Laravel 6 requires PHP 7.4. Run `php -v`. If it's not 7.4, you might need to use the specific path to PHP 7.4 on Hostinger (e.g., `/opt/alt/php74/usr/bin/php`).
5. **Install Dependencies (if needed):** If you didn't upload your `vendor` folder, run:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
6. **Generate App Key:**
   ```bash
   php artisan key:generate
   ```
7. **Migrate and Seed (CRITICAL STEP):**
   * **What this does:** This command creates all your database tables from scratch (`migrate`) and inserts the initial data like Admin users, default Chart of Accounts, and Mapping Rules (`--seed`).
   ```bash
   php artisan migrate:fresh --seed
   ```
8. **Link Storage:** To make sure uploaded images (like carpets or user profiles) show up correctly:
   ```bash
   php artisan storage:link
   ```

---

## Step 7: Final Tests
Your application should now be live!

1. Open your web browser and go to `https://test.yourdomain.com`.
2. You should see the login screen.
3. Log in using the default Admin credentials that are created by your Database Seeders.
4. **Test the new Finance Modules:** Go to "Accounting" or "Journals" and try creating a transaction to verify that the database is working flawlessly.

If you encounter any 500 Server Errors, check the `storage/logs/laravel.log` file in the File Manager to see exactly what went wrong.
