<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\ChartOfAccount;

echo "All Inventory Accounts:\n";
$accounts = ChartOfAccount::where('report_group', 'Inventory')->get();
foreach ($accounts as $acc) {
    echo "ID: " . $acc->id . ", Code: " . $acc->account_code . ", Name: " . $acc->account_name . "\n";
}

echo "\nAll Liability or Cash Accounts:\n";
$accounts = ChartOfAccount::where('account_type', 'Liability')->orWhere('is_cash_account', 1)->get();
foreach ($accounts as $acc) {
    echo "ID: " . $acc->id . ", Code: " . $acc->account_code . ", Name: " . $acc->account_name . "\n";
}
