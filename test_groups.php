<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$permissions = Spatie\Permission\Models\Permission::where('name', 'like', '%employee%')->orWhere('name', 'like', '%payroll%')->get();

$groups = [];

foreach ($permissions as $p) {
    if (strpos($p->name, 'dashboard') !== false || strpos($p->name, 'cost_analytics') !== false) {
        $groups['dashboards'][] = $p->name;
    } elseif (strpos($p->name, 'coa') !== false || strpos($p->name, 'journal') !== false || strpos($p->name, 'mapping') !== false || strpos($p->name, 'currenc') !== false || strpos($p->name, '_report') !== false || strpos($p->name, '_statement') !== false || strpos($p->name, 'ledger') !== false || strpos($p->name, 'audit') !== false || strpos($p->name, 'balance') !== false || strpos($p->name, 'pl') !== false) {
        if(strpos($p->name, 'purchased_carpets_report') !== false) $groups['carpets'][] = $p->name;
        elseif(strpos($p->name, 'agent_statement') !== false) $groups['agents'][] = $p->name;
        elseif(strpos($p->name, 'seller_statement') !== false) $groups['materials'][] = $p->name;
        elseif(strpos($p->name, 'different_account_statement') !== false) $groups['different_accounts'][] = $p->name;
        elseif(strpos($p->name, 'kachaee') !== false && strpos($p->name, 'statement') !== false) $groups['kachaee'][] = $p->name;
        elseif(strpos($p->name, 'washing') !== false && strpos($p->name, 'statement') !== false) $groups['washing'][] = $p->name;
        elseif(strpos($p->name, 'finishing') !== false && strpos($p->name, 'statement') !== false) $groups['finishing'][] = $p->name;
        elseif(strpos($p->name, 'customer_statement') !== false || strpos($p->name, 'aging_report') !== false) $groups['customers'][] = $p->name;
        elseif(strpos($p->name, 'employee_statement') !== false) $groups['employees'][] = $p->name;
        elseif(strpos($p->name, 'assets_report') !== false) $groups['assets'][] = $p->name;
        elseif(strpos($p->name, 'warehouse_inventory_report') !== false || strpos($p->name, 'warehouse_movements') !== false || strpos($p->name, 'warehouse_in_out') !== false || strpos($p->name, 'available_stock') !== false) $groups['warehouse'][] = $p->name;
        else $groups['accounting'][] = $p->name;
    } elseif (strpos($p->name, 'agent') !== false && strpos($p->name, 'employee') === false) {
        $groups['agents'][] = $p->name;
    } elseif (strpos($p->name, 'carpet_type') !== false || strpos($p->name, 'carpet_qualit') !== false || strpos($p->name, 'buy_carpet') !== false || strpos($p->name, 'purchase_bill') !== false || strpos($p->name, 'purchased_carpets') !== false) {
        $groups['carpets'][] = $p->name;
    } elseif (strpos($p->name, 'material') !== false || strpos($p->name, 'seller') !== false) {
        $groups['materials'][] = $p->name;
    } elseif (strpos($p->name, 'different_account') !== false) {
        $groups['different_accounts'][] = $p->name;
    } elseif (strpos($p->name, 'kachaee') !== false || strpos($p->name, 'repair') !== false) {
        $groups['kachaee'][] = $p->name;
    } elseif (strpos($p->name, 'wash') !== false) {
        $groups['washing'][] = $p->name;
    } elseif (strpos($p->name, 'finish') !== false) {
        $groups['finishing'][] = $p->name;
    } elseif (strpos($p->name, 'sale') !== false || strpos($p->name, 'invoice') !== false || strpos($p->name, 'carpet_stock') !== false) {
        $groups['sales'][] = $p->name;
    } elseif (strpos($p->name, 'customer_order') !== false) {
        $groups['customer_orders'][] = $p->name;
    } elseif (strpos($p->name, 'assets') !== false) {
        $groups['assets'][] = $p->name;
    } elseif (strpos($p->name, 'warehouse') !== false || strpos($p->name, 'inventory_transfer') !== false) {
        $groups['warehouse'][] = $p->name;
    } elseif (strpos($p->name, 'customer') !== false) {
        $groups['customers'][] = $p->name;
    } elseif (strpos($p->name, 'employee') !== false || strpos($p->name, 'payroll') !== false) {
        $groups['employees'][] = $p->name;
    } elseif (strpos($p->name, 'expense') !== false) {
        $groups['expenses'][] = $p->name;
    } else {
        $groups['settings'][] = $p->name;
    }
}

foreach ($groups as $group => $perms) {
    echo strtoupper($group) . ":\n";
    foreach ($perms as $p) echo "  - $p\n";
}
