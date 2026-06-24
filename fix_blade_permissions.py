import re

with open('resources/views/dsh/master.blade.php', 'r') as f:
    content = f.read()

# Map the incorrect 'manage_*' generic permissions to the exact 'view_*' permissions defined in PermissionSeeder
replacements = {
    "'manage_agents'": "'view_agents'",
    "'manage_buy_carpets'": "'view_buy_carpets'",
    "'manage_carpet_types'": "'view_carpet_types'",
    "'manage_carpet_qualities'": "'view_carpet_qualities'",
    "'manage_purchase_bills'": "'view_purchase_bills'",
    "'manage_material_purchases'": "'view_material_purchases'",
    "'manage_raw_material_bills'": "'view_raw_material_bills'",
    "'manage_material_sales'": "'view_material_sales'",
    "'manage_material_stock'": "'view_material_stock'",
    "'manage_string_sellers'": "'view_string_sellers'",
    "'manage_material_categories'": "'view_material_categories'",
    "'manage_material_types'": "'view_material_types'",
    "'manage_different_accounts'": "'view_different_accounts'",
    "'manage_carpet_repairs'": "'view_carpet_repairs'",
    "'manage_kachaee_teams'": "'view_kachaee_teams'",
    "'manage_kachaee_batches'": "'view_kachaee_batches'",
    "'manage_carpet_washes'": "'view_carpet_washes'",
    "'manage_washing_teams'": "'view_washing_teams'",
    "'manage_washing_batches'": "'view_washing_batches'",
    "'manage_finishing_centers'": "'view_finishing_centers'",
    "'manage_finishing_teams'": "'view_finishing_teams'",
    "'manage_finishing_batches'": "'view_finishing_batches'",
    "'manage_carpet_stock'": "'view_carpet_stock'",
    "'manage_sales'": "'view_sales'",
    "'manage_invoices'": "'view_invoices'",
    "'manage_customers'": "'view_customers'",
    "'manage_customer_orders'": "'view_customer_orders'",
    "'manage_users'": "'view_users'",
    "'manage_roles'": "'manage_roles_and_permissions'",
    "'manage_settings'": "'manage_roles_and_permissions'",
    "'manage_assets_accounts'": "'view_assets_accounts'",
}

for old, new in replacements.items():
    content = content.replace(old, new)

with open('resources/views/dsh/master.blade.php', 'w') as f:
    f.write(content)
