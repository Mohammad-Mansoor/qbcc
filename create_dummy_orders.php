<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\CustomerOrder;
use Carbon\Carbon;

$customer = App\Customer::first();
$customerId = $customer ? $customer->id : 1;

CustomerOrder::create([
    'order_name' => 'ORD-TEST-1',
    'order_date' => Carbon::now()->subDays(10),
    'end_date' => Carbon::now()->addDays(5),
    'main_customer_id' => $customerId,
    'status' => 'in_progress',
    'customer_id' => null,
]);

CustomerOrder::create([
    'order_name' => 'ORD-TEST-2',
    'order_date' => Carbon::now()->subDays(20),
    'end_date' => Carbon::now()->addDays(14),
    'main_customer_id' => $customerId,
    'status' => 'in_progress',
    'customer_id' => null,
]);

CustomerOrder::create([
    'order_name' => 'ORD-TEST-3',
    'order_date' => Carbon::now()->subDays(5),
    'end_date' => Carbon::now()->addDays(28),
    'main_customer_id' => $customerId,
    'status' => 'in_progress',
    'customer_id' => null,
]);

echo "Dummy data created!\n";
