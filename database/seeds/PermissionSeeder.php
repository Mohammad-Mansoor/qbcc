<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\User;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboards
            'view_production_dashboard',
            'view_inventory_dashboard',
            'view_finance_dashboard',
            'view_sales_dashboard',
            'view_purchases_dashboard',
            'view_cost_analytics',

            // Accounting
            'view_coa',
            'create_coa',
            'edit_coa',
            'view_journals',
            'create_journal',
            'reverse_journal',
            'print_journal',
            'view_mapping_rules',
            'edit_mapping_rules',
            'view_currencies',
            'create_currency',
            'edit_currency',
            'delete_currency',
            'view_pl_report',
            'view_balance_sheet',
            'view_trial_balance',
            'view_comparative_pl',
            'view_cash_flow_report',
            'view_fx_exposure_report',
            'view_inventory_valuation',
            'view_cost_center_performance',
            'view_audit_corrections',
            'view_account_ledger',
            'view_customer_statement',
            'view_agent_statement',
            'view_employee_statement',
            'view_seller_statement',
            'view_different_account_statement',
            'view_kachaee_statement',
            'view_washing_statement',
            'view_finishing_statement',

            // Agents
            'view_agents',
            'create_agent',
            'edit_agent',
            'delete_agent',
            'manage_agent_accounts',
            'deactivate_agent',
            'view_agent_carpets',
            'view_agent_money_requests',
            'approve_agent_money_requests',
            'delete_agent_money_requests',
            'export_agent_statement_pdf',
            'export_agent_statement_excel',
            'cancel_agent_payment',

            // Carpets
            'view_purchased_carpets_report',
            'export_purchased_carpets_pdf',
            'export_purchased_carpets_excel',
            'view_buy_carpets',
            'create_buy_carpet',
            'edit_buy_carpet',
            'print_buy_carpet',
            'view_purchase_bills',
            'create_purchase_bill',
            'print_purchase_bill_pdf',
            'close_purchase_bill',
            'view_carpet_types',
            'create_carpet_type',
            'edit_carpet_type',
            'delete_carpet_type',
            'view_carpet_qualities',
            'create_carpet_quality',
            'edit_carpet_quality',
            'delete_carpet_quality',

            // Raw Materials
            'view_material_purchases',
            'create_material_purchase',
            'edit_material_purchase',
            'delete_material_purchase',
            'view_raw_material_bills',
            'create_raw_material_bill',
            'edit_raw_material_bill',
            'close_raw_material_bill',
            'delete_raw_material_bill',
            'print_raw_material_bill_pdf',
            'export_raw_material_bill_excel',
            'view_purchase_material_requests',
            'approve_purchase_material_requests',
            'reject_purchase_material_requests',
            'view_material_sales',
            'create_material_sale',
            'edit_material_sale',
            'delete_material_sale',
            'view_material_sale_requests',
            'approve_material_sale_requests',
            'reject_material_sale_requests',
            'view_material_stock',
            'view_string_sellers',
            'create_string_seller',
            'edit_string_seller',
            'delete_string_seller',
            'manage_seller_payments',
            'cancel_seller_payment',
            'view_seller_money_requests',
            'approve_seller_money_requests',
            'reject_seller_money_requests',
            'view_material_categories',
            'create_material_category',
            'edit_material_category',
            'delete_material_category',
            'view_material_types',
            'create_material_type',
            'edit_material_type',
            'delete_material_type',

            // Misc Accounts
            'view_different_accounts',
            'create_different_account',
            'edit_different_account',
            'delete_different_account',
            'manage_different_account_payments',
            'edit_approved_different_account_payments',
            'view_different_account_money_requests',
            'approve_different_account_money_requests',
            'reject_different_account_money_requests',
            'export_different_account_statement_pdf',
            'export_different_account_statement_excel',

            // Kachaee / Repair
            'view_carpet_repairs',
            'create_carpet_repair',
            'send_carpet_to_kachaee',
            'edit_carpet_repair',
            'return_carpet_from_repair',
            'view_kachaee_teams',
            'create_kachaee_team',
            'edit_kachaee_team',
            'manage_kachaee_payments',
            'cancel_kachaee_payment',
            'view_kachaee_team_statement',
            'export_kachaee_statement_pdf',
            'export_kachaee_statement_excel',
            'view_kachaee_batches',
            'create_kachaee_batch',
            'manage_kachaee_batch_status',
            'print_kachaee_batches',
            'export_kachaee_batches_pdf',
            'export_kachaee_batches_excel',
            'view_kachaee_money_requests',
            'approve_kachaee_money_requests',
            'reject_kachaee_money_requests',

            // Washing
            'view_carpet_washes',
            'create_carpet_wash',
            'send_carpet_to_washing',
            'edit_carpet_wash',
            'return_carpet_from_wash',
            'view_washing_teams',
            'create_washing_team',
            'edit_washing_team',
            'manage_washing_payments',
            'cancel_washing_payment',
            'view_washing_team_statement',
            'export_washing_statement_pdf',
            'export_washing_statement_excel',
            'view_washing_batches',
            'create_washing_batch',
            'manage_washing_batch_status',
            'print_washing_batches',
            'export_washing_batches_pdf',
            'export_washing_batches_excel',
            'view_washing_money_requests',
            'approve_washing_money_requests',
            'reject_washing_money_requests',

            // Finishing
            'view_finishing_centers',
            'create_finishing_work',
            'send_carpet_to_finishing',
            're_saving_the_work',
            'view_finishing_teams',
            'create_finishing_team',
            'edit_finishing_team',
            'manage_finishing_payments',
            'cancel_finishing_payment',
            'view_finishing_team_statement',
            'export_finishing_statement_pdf',
            'export_finishing_statement_excel',
            'view_finishing_batches',
            'create_finishing_batch',
            'manage_finishing_batch_status',
            'print_finishing_batches',
            'export_finishing_batches_pdf',
            'export_finishing_batches_excel',
            'view_refinish_requests',
            'approve_refinish_requests',
            'reject_refinish_requests',
            'view_finishing_money_requests',
            'approve_finishing_money_requests',
            'reject_finishing_money_requests',

            // Carpet Stock & Sales
            'view_carpet_stock',
            'view_carpet_stock_details',
            'sell_carpet_from_stock',
            'view_sales',
            'create_sale',
            'edit_sale',
            'delete_sale',
            'view_invoices',
            'create_invoice',
            'edit_invoice',
            'close_invoice',
            'print_invoice',

            // Customer Orders
            'view_customer_orders',
            'create_customer_order',
            'edit_customer_order',
            'delete_customer_order',
            'manage_customer_order_details',
            'receive_customer_order_alerts',

            // Assets
            'view_assets_accounts',
            'create_assets_account',
            'edit_assets_account',
            'delete_assets_account',
            'manage_assets_account',
            'view_assets_report',
            'export_assets_report_pdf',
            'export_assets_report_excel',

            // Warehouse
            'view_warehouse_inventory_report',
            'view_warehouses',
            'create_warehouse',
            'edit_warehouse',
            'delete_warehouse',
            'view_warehouse_in_out_report',
            'view_warehouse_available_stock',
            'view_inventory_transfers',
            'create_inventory_transfer',
            'reverse_inventory_transfer',
            'view_warehouse_movements',
            'export_warehouse_movements_pdf',
            'export_warehouse_movements_excel',

            // Customers
            'view_customers',
            'create_customer',
            'edit_customer',
            'manage_customer_payments',
            'export_customer_statement_pdf',
            'export_customer_statement_excel',
            'view_customer_money_requests',
            'approve_customer_money_requests',
            'reject_customer_money_requests',
            'view_ar_aging_report',

            // Employees & Expenses
            'view_monthly_expenses',
            'create_monthly_expense',
            'manage_monthly_expense_payments',
            'view_employees',
            'create_employee',
            'edit_employee',
            'manage_employee_payments',
            'view_payroll',
            'run_payroll',
            'view_payroll_slip',
            'view_employee_money_requests',
            'approve_employee_money_requests',
            'reject_employee_money_requests',
            'view_employee_departments',
            'create_employee_department',
            'edit_employee_department',

            // Settings
            'view_users',
            'create_user',
            'edit_user',
            'delete_user',
            'assign_roles',
            'view_phone_book',
            'create_phone_book',
            'edit_phone_book',
            'delete_phone_book',
            'view_provinces',
            'create_province',
            'edit_province',
            'delete_province',
            'view_agent_employees',
            'create_agent_employee',
            'edit_agent_employee',
            'delete_agent_employee',
            'view_activities',
            'delete_activities',
            'manage_roles_and_permissions',
            'view_orders',
            'create_order',
            'edit_order',
            'delete_order'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Super Admin Role & Assign all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Create other legacy roles
        $legacyRoles = [
            'CO' => 'Central Office',
            'CCO' => 'Central Office & Customers',
            'SO' => 'Sales Office',
            'SCO' => 'Sales Office & Customers',
            'MO' => 'Misc Account',
            'PH' => 'Photographer',
            'AO' => 'Agent Account',
            'DE' => 'Data Entry',
            'FI' => 'Finance',
            'OM' => 'Omid'
        ];

        foreach ($legacyRoles as $code => $name) {
            Role::firstOrCreate(['name' => $name]);
        }

        // Migrate existing users based on legacy role string
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'SP') {
                $user->assignRole('Super Admin');
            } elseif (array_key_exists($user->role, $legacyRoles)) {
                $user->assignRole($legacyRoles[$user->role]);
            }
        }
    }
}
