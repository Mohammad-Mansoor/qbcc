import re

content = open('routes/web.php', 'r').read()

mapping = [
    # Dashboards
    (r"Route::get\('/(?:production|inventory|sales|purchases|finance|cost-analytics)', 'EnterpriseDashboardController@[^']+'\)", 'view_production_dashboard'), # Simplify for now or map individually
    (r"Route::get\('/production', 'EnterpriseDashboardController@production'\)", 'view_production_dashboard'),
    (r"Route::get\('/inventory', 'EnterpriseDashboardController@inventory'\)", 'view_inventory_dashboard'),
    (r"Route::get\('/finance', 'EnterpriseDashboardController@finance'\)", 'view_finance_dashboard'),
    (r"Route::get\('/sales', 'EnterpriseDashboardController@sales'\)", 'view_sales_dashboard'),
    (r"Route::get\('/purchases', 'EnterpriseDashboardController@purchases'\)", 'view_purchases_dashboard'),
    (r"Route::get\('/cost-analytics', 'EnterpriseDashboardController@costAnalytics'\)", 'view_cost_analytics'),
    
    # Users & Roles
    (r"Route::resource\('/users', 'UserController'\)", 'manage_users'),
    (r"Route::post\('/user-search', 'UserController@search'\)", 'manage_users'),
    (r"Route::resource\('/roles', 'RoleController'\)", 'manage_roles'),
    
    # Accounting & Finance
    (r"Route::resource\('/currencies', 'Accounting\\CurrencyController'\)(?:->names\(\[[^\]]+\]\))?", 'manage_currencies'),
    (r"Route::get\('/chart-of-accounts', 'Accounting\\ChartOfAccountController@index'\)(?:->name\('[^']+'\))?", 'view_coa'),
    (r"Route::get\('/chart-of-accounts/create', 'Accounting\\ChartOfAccountController@create'\)(?:->name\('[^']+'\))?", 'create_coa'),
    (r"Route::post\('/chart-of-accounts', 'Accounting\\ChartOfAccountController@store'\)(?:->name\('[^']+'\))?", 'create_coa'),
    (r"Route::get\('/chart-of-accounts/\{id\}/edit', 'Accounting\\ChartOfAccountController@edit'\)(?:->name\('[^']+'\))?", 'edit_coa'),
    (r"Route::put\('/chart-of-accounts/\{id\}', 'Accounting\\ChartOfAccountController@update'\)(?:->name\('[^']+'\))?", 'edit_coa'),
    
    (r"Route::get\('/journals', 'Accounting\\JournalController@index'\)(?:->name\('[^']+'\))?", 'view_journals'),
    (r"Route::get\('/journals/create', 'Accounting\\JournalController@create'\)(?:->name\('[^']+'\))?", 'create_journal'),
    (r"Route::post\('/journals', 'Accounting\\JournalController@store'\)(?:->name\('[^']+'\))?", 'create_journal'),
    (r"Route::get\('/journals/\{id\}', 'Accounting\\JournalController@show'\)(?:->name\('[^']+'\))?", 'view_journals'),
    (r"Route::get\('/journals/\{id\}/print', 'Accounting\\JournalController@print'\)(?:->name\('[^']+'\))?", 'print_journal'),
    (r"Route::post\('/journals/\{id\}/reverse', 'Accounting\\JournalController@reverse'\)(?:->name\('[^']+'\))?", 'reverse_journal'),
    
    (r"Route::get\('/mapping-rules', 'Accounting\\MappingRuleController@index'\)(?:->name\('[^']+'\))?", 'view_mapping_rules'),
    (r"Route::post\('/mapping-rules', 'Accounting\\MappingRuleController@update'\)(?:->name\('[^']+'\))?", 'edit_mapping_rules'),
    
    (r"Route::get\('/reports/trial-balance', 'Accounting\\ReportController@trialBalance'\)(?:->name\('[^']+'\))?", 'view_trial_balance'),
    (r"Route::get\('/reports/profit-loss', 'Accounting\\ReportController@profitLoss'\)(?:->name\('[^']+'\))?", 'view_pl_report'),
    (r"Route::get\('/reports/balance-sheet', 'Accounting\\ReportController@balanceSheet'\)(?:->name\('[^']+'\))?", 'view_balance_sheet'),
    
    # Kachaee
    (r"Route::resource\('kachaee-team', 'KachaeeController'\)(?:->parameters\(\[[^\]]+\]\))?", 'manage_kachaee_teams'),
    (r"Route::resource\('kachaee-payments', 'KachaeePaymentController'\)", 'manage_kachaee_payments'),
    
    # Washing
    (r"Route::resource\('washing-team', 'WashingTeamController'\)(?:->parameters\(\[[^\]]+\]\))?", 'manage_washing_teams'),
    (r"Route::resource\('washing-payments', 'WashingPaymentController'\)", 'manage_washing_payments'),
    
    # Finishing
    (r"Route::resource\('/finish-team', 'FinishingTeamController'\)(?:->parameters\(\[[^\]]+\]\))?", 'manage_finishing_teams'),
    (r"Route::resource\('finishing-center', 'FinishingWorkController'\)(?:->parameters\(\[[^\]]+\]\))?", 'manage_finishing_centers'),
    (r"Route::resource\('finishing-payments', 'FinishingTeamPaymentController'\)", 'manage_finishing_payments'),
    
    # Sales
    (r"Route::resource\('/sales', 'SaleController'\)", 'manage_sales'),
    (r"Route::resource\('/invoices', 'InvoiceController'\)", 'manage_invoices'),
    
    # Customers
    (r"Route::resource\('/customers', 'CustomerController'\)", 'manage_customers'),
    (r"Route::resource\('customer-payments', 'CustomerPaymentController'\)", 'manage_customer_payments'),
    
    # Agents
    (r"Route::resource\('/agents', 'AgentsController'\)", 'manage_agents'),
    (r"Route::resource\('/agent-payments', 'AgentPaymentController'\)(?:->parameters\(\[[^\]]+\]\))?", 'manage_agent_accounts'),
    
    # Carpets
    (r"Route::resource\('/carpet-types', 'CarpetTypeController'\)", 'manage_carpet_types'),
    (r"Route::resource\('/carpet-qualities', 'QualityController'\)", 'manage_carpet_qualities'),
]

# We will apply middleware to matching routes.
lines = content.split('\n')
new_lines = []

for line in lines:
    modified = line
    for pattern, perm in mapping:
        # Check if route matches pattern and doesn't already have middleware
        if re.search(pattern, modified) and '->middleware(' not in modified:
            # We append ->middleware('permission:perm') before the ending semicolon
            modified = re.sub(r'([^;]+);', r"\1->middleware('permission:" + perm + r"');", modified)
            break
    new_lines.append(modified)

open('routes/web.php', 'w').write('\n'.join(new_lines))
