import re

with open('resources/views/dsh/master.blade.php', 'r') as f:
    content = f.read()

replacements = [
    # Accounting module
    ("auth()->user()->role == 'SP' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['view_coa', 'view_journals', 'view_mapping_rules', 'view_pl_report'])"),
    
    # Agents (wrapper)
    ("auth()->user()->role != 'AO'", "!auth()->user()->hasRole('Agent Account')"),
    ("auth()->user()->role == 'AO' || auth()->user()->role == 'SP'", "auth()->user()->hasAnyPermission(['view_agent_statement', 'manage_agent_accounts'])"),
    
    # Agent sub-menu
    ("auth()->user()->role == 'SP' || auth()->user()->role == 'CO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['manage_agents', 'manage_material_purchases', 'manage_warehouses'])"),
    
    # Carpet module
    ("auth()->user()->role == 'SP' || auth()->user()->role == 'CO' || auth()->user()->role == 'CCO'", "auth()->user()->hasAnyPermission(['manage_buy_carpets', 'manage_carpet_types', 'manage_kachaee_teams'])"),
    
    # Different account
    ("auth()->user()->role == 'SP' || auth()->user()->role == 'SO' || auth()->user()->role == 'CO' || auth()->user()->role == 'SCO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'MO' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['manage_different_accounts'])"),

    # Washing
    ("auth()->user()->role == 'SP' || auth()->user()->role == 'SO' || auth()->user()->role == 'SCO' || auth()->user()->role == 'CO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'DE' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['manage_carpet_washes', 'manage_washing_teams'])"),

    # Finishing
    ("auth()->user()->role == 'SP' || auth()->user()->role == 'SO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'SCO' || auth()->user()->role == 'DE' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['manage_finishing_centers', 'manage_finishing_teams'])"),

    # Carpet Stock
    ("auth()->user()->role == 'SP' || auth()->user()->role == 'SO' || auth()->user()->role == 'SCO' || auth()->user()->role == 'OM' || auth()->user()->role == 'DE' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['manage_carpet_stock'])"),
    
    # Sales
    ("auth()->user()->role == 'SO' || auth()->user()->role == 'SCO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'SP' || auth()->user()->role == 'OM' || auth()->user()->role == 'DE' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['manage_sales', 'manage_invoices'])"),

    # Customers
    ("auth()->user()->role == 'SCO' || auth()->user()->role == 'CCO' || auth()->user()->role == 'SP' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['manage_customers'])"),

    # Customer Orders
    ("auth()->user()->role == 'SP' || auth()->user()->role == 'SO' || auth()->user()->role == 'CO' || auth()->user()->role == 'FI'", "auth()->user()->hasAnyPermission(['manage_customer_orders', 'view_warehouses'])"),

    # Settings
    ("auth()->user()->role != 'MO' && auth()->user()->role != 'PH' && auth()->user()->role != 'AO' && auth()->user()->role != 'OM' && auth()->user()->role != 'DE'", "auth()->user()->hasAnyPermission(['manage_users', 'manage_roles'])"),

    # Leftover SP only checks (Super Admin or specific approval)
    # Using regex to target specific lines for SP approvals to ensure granularity
]

for old, new in replacements:
    content = content.replace(old, new)

# Handle specific SP checks based on context
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/agent-money-request-list\")", r"@if(auth()->user()->can('approve_agent_money_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/purchase-material-request-list\")", r"@if(auth()->user()->can('approve_purchase_material_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/material-sale-request-list\")", r"@if(auth()->user()->can('approve_material_sale_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/string-seller-request-list\")", r"@if(auth()->user()->can('approve_seller_money_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/different-account-money-request-list\")", r"@if(auth()->user()->can('approve_different_account_money_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/kachaee-money-request-list\")", r"@if(auth()->user()->can('approve_kachaee_money_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/washing-money-request-list\")", r"@if(auth()->user()->can('approve_washing_money_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/finishing-money-request-list\")", r"@if(auth()->user()->can('approve_finishing_money_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/customer-request-list\")", r"@if(auth()->user()->can('approve_customer_money_requests'))\n                  \1", content)
content = re.sub(r"@if\(auth\(\)->user\(\)->role == 'SP'\)\s*(<li><a href=\"/dashboard/employee-request-list\")", r"@if(auth()->user()->can('approve_employee_money_requests'))\n                  \1", content)

# Remaining SP checks (mostly settings, users, roles, activities)
content = content.replace("auth()->user()->role == 'SP'", "auth()->user()->hasAnyPermission(['manage_settings', 'manage_users', 'manage_roles'])")

with open('resources/views/dsh/master.blade.php', 'w') as f:
    f.write(content)
