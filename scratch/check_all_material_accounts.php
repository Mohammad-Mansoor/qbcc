<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AccountSelectionService;

$service = new AccountSelectionService();

$keys = ['MATERIAL_PURCHASE_CREDIT', 'MATERIAL_REVENUE', 'MATERIAL_PAYMENT', 'MATERIAL_RECEIPT'];

foreach ($keys as $key) {
    echo "Key: $key\n";
    foreach (['debit', 'credit'] as $side) {
        $accounts = $service->getValidAccounts($key, $side);
        echo "  $side: " . count($accounts) . " accounts\n";
        foreach ($accounts as $acc) {
            echo "    - " . $acc->account_code . " " . $acc->account_name . "\n";
        }
    }
    echo "\n";
}
