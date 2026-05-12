<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\MaterialSale;
use App\ChartOfAccount;

// Find some accounts for overrides
$revenueDrId = 1; 
$revenueCrId = 5; 
$cogsDrId = 6; 
$cogsCrId = 8; 

$sale = new MaterialSale();
$sale->sale_number = 'SA-TEST-' . rand(100, 999);
$sale->agent_id = 3;
$sale->category_id = 1;
$sale->type_id = 1;
$sale->warehouse_id = 1;
$sale->amount = 50.5;
$sale->price = 120; // AFG per kg
$sale->total_price_af = 50.5 * 120;
$sale->total_price = (50.5 * 120) / 88; // USD approx
$sale->date = date('Y-m-d');
$sale->status = 0; // Pending

// Overrides
$sale->override_debit_account_id = $revenueDrId;
$sale->override_credit_account_id = $revenueCrId;
$sale->override_cogs_debit_id = $cogsDrId;
$sale->override_cogs_credit_id = $cogsCrId;

$sale->save();

echo "Sale Request Created: " . $sale->sale_number . PHP_EOL;
echo "Check it at: /dashboard/material-sale-request-list" . PHP_EOL;
