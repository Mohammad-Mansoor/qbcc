import re

with open('resources/views/dsh/master.blade.php', 'r') as f:
    content = f.read()

# Make backup
with open('resources/views/dsh/master.blade.php.backup', 'w') as f:
    f.write(content)

replacements = [
    # Dashboard checks
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@canany(['view_coa', 'view_journals', 'view_mapping_rules', 'manage_currencies', 'view_pl_report'])\1@endcanany"),
    
    # Agents
    (r"@if\(auth\(\)->user\(\)->role != 'AO'\)\s*@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'CO' \|\| auth\(\)->user\(\)->role == 'CCO' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif\s*@endif", r"@can('manage_agents')\1@endcan"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/agent-money-request-list\".*?</li>)\s*@endif", r"@can('approve_agent_money_requests')\1@endcan"),

    # Carpets
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'CO' \|\| auth\(\)->user\(\)->role == 'CCO'\)([\s\S]*?)@endif", r"@canany(['manage_buy_carpets', 'manage_carpet_types', 'manage_carpet_qualities', 'manage_purchase_bills'])\1@endcanany"),

    # Raw Materials
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'CO' \|\| auth\(\)->user\(\)->role == 'CCO' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@canany(['manage_material_purchases', 'manage_raw_material_bills', 'manage_material_sales', 'manage_material_stock', 'manage_string_sellers', 'manage_material_categories', 'manage_material_types'])\1@endcanany"),
    
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/purchase-material-request-list\".*?</li>)\s*@endif", r"@can('approve_purchase_material_requests')\1@endcan"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/material-sale-request-list\".*?</li>)\s*@endif", r"@can('approve_material_sale_requests')\1@endcan"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/string-seller-request-list\".*?</li>)\s*@endif", r"@can('approve_seller_money_requests')\1@endcan"),

    # Different Account
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'SO' \|\| auth\(\)->user\(\)->role == 'CO' \|\| auth\(\)->user\(\)->role == 'SCO' \|\| auth\(\)->user\(\)->role == 'CCO' \|\| auth\(\)->user\(\)->role == 'MO' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@can('manage_different_accounts')\1@endcan"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/different-account-money-request-list\".*?</li>)\s*@endif", r"@can('approve_different_account_money_requests')\1@endcan"),

    # Kachaee
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'CO' \|\| auth\(\)->user\(\)->role == 'CCO'\)([\s\S]*?)@endif", r"@canany(['manage_carpet_repairs', 'manage_kachaee_teams', 'manage_kachaee_batches'])\1@endcanany"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/kachaee-money-request-list\".*?</li>)\s*@endif", r"@can('approve_kachaee_money_requests')\1@endcan"),

    # Washing
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'SO' \|\| auth\(\)->user\(\)->role == 'SCO' \|\| auth\(\)->user\(\)->role == 'CO' \|\| auth\(\)->user\(\)->role == 'CCO' \|\| auth\(\)->user\(\)->role == 'DE' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@canany(['manage_carpet_washes', 'manage_washing_teams', 'manage_washing_batches'])\1@endcanany"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/washing-money-request-list\".*?</li>)\s*@endif", r"@can('approve_washing_money_requests')\1@endcan"),

    # Finishing
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'SO' \|\| auth\(\)->user\(\)->role == 'CCO' \|\| auth\(\)->user\(\)->role == 'SCO' \|\| auth\(\)->user\(\)->role == 'DE' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@canany(['manage_finishing_centers', 'manage_finishing_teams', 'manage_finishing_batches'])\1@endcanany"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/finishing-money-request-list\".*?</li>)\s*@endif", r"@can('approve_finishing_money_requests')\1@endcan"),
    
    # Stock and Sales
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'SO' \|\| auth\(\)->user\(\)->role == 'SCO' \|\| auth\(\)->user\(\)->role == 'OM' \|\| auth\(\)->user\(\)->role == 'DE' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@can('manage_carpet_stock')\1@endcan"),
    (r"@if\(auth\(\)->user\(\)->role == 'SO' \|\| auth\(\)->user\(\)->role == 'SCO' \|\| auth\(\)->user\(\)->role == 'CCO' \|\| auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'OM' \|\| auth\(\)->user\(\)->role == 'DE' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@canany(['manage_sales', 'manage_invoices'])\1@endcanany"),

    # Customer Orders
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'SO' \|\| auth\(\)->user\(\)->role == 'CO' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@can('manage_customer_orders')\1@endcan"),
    
    # Customers
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@can('manage_customers')\1@endcan"),
    (r"@if\(auth\(\)->user\(\)->role == 'SCO' \|\| auth\(\)->user\(\)->role == 'CCO' \|\| auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@can('manage_customers')\1@endcan"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/customer-request-list\".*?</li>)\s*@endif", r"@can('approve_customer_money_requests')\1@endcan"),
    
    # Assets
    (r"<!-- @if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'SO' \|\| auth\(\)->user\(\)->role == 'CO'\)-->([\s\S]*?)<!-- @endif -->", r"<!-- @can('manage_assets_accounts') -->\1<!-- @endcan -->"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/monthly-expenses\".*?</li>)\s*@endif", r"@can('manage_monthly_expenses')\1@endcan"),
    
    # Warehouse
    (r"@if\(auth\(\)->user\(\)->role == 'SP' \|\| auth\(\)->user\(\)->role == 'SO' \|\| auth\(\)->user\(\)->role == 'CO' \|\| auth\(\)->user\(\)->role == 'FI'\)([\s\S]*?)@endif", r"@canany(['manage_warehouses', 'manage_inventory_transfers'])\1@endcanany"),
    
    # Employees
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/employee-request-list\".*?</li>)\s*@endif", r"@can('approve_employee_money_requests')\1@endcan"),

    # Settings
    (r"@if\(auth\(\)->user\(\)->role != 'MO' && auth\(\)->user\(\)->role != 'PH' && auth\(\)->user\(\)->role != 'AO' && auth\(\)->user\(\)->role != 'OM' && auth\(\)->user\(\)->role != 'DE'\)([\s\S]*?)@endif", r"@canany(['manage_users', 'manage_roles', 'manage_phone_book', 'manage_provinces'])\1@endcanany"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/users\".*?</li>)\s*@endif", r"@can('manage_users')\1@endcan"),
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/roles\".*?</li>)\s*@endif", r"@can('manage_roles')\1@endcan"),
    
    # Leftover SP checks for specific items
    (r"@if\(auth\(\)->user\(\)->role == 'SP'\)", r"@can('manage_settings')")
]

for pattern, replacement in replacements:
    content = re.sub(pattern, replacement, content)

with open('resources/views/dsh/master.blade.php', 'w') as f:
    f.write(content)
