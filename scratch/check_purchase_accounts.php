<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\ChartOfAccount;

echo "Checking Account Details:\n";
$accounts = ChartOfAccount::whereIn('id', [6, 3])->get();
foreach ($accounts as $acc) {
    echo "ID: " . $acc->id . ", Code: " . $acc->account_code . ", Name: " . $acc->account_name . ", Type: " . $acc->account_type . ", Group: " . $acc->report_group . ", Cash: " . $acc->is_cash_account . "\n";
}
