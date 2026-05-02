<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$issues = [
    'agent_payments' => 'status',
    'different_account_payments' => 'status',
    'carpets' => 'parcha_number',
    'kachaee_payments' => 'status',
    'washing_payments' => 'status',
    'finishing_team_payments' => 'status',
    'purchase_materials' => 'status',
    'material_sales' => 'status',
    'seller_payments' => 'status',
    'employee_payments' => 'status',
];

$valid = [];
foreach ($issues as $table => $column) {
    if (Schema::hasTable($table)) {
        if (!Schema::hasColumn($table, $column)) {
            $valid[$table] = $column;
        }
    }
}

echo "Valid Missing Columns to Fix:\n";
print_r($valid);
