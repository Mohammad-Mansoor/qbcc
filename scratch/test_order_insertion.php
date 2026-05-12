<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$order_id = 1;
$customer_order = DB::table('customer_orders')->where('co_id', $order_id)->first();

if (!$customer_order) {
    echo "Order ID 1 not found. Creating a test order first...\n";
    $customer_account_id = DB::table('customer_account_orders')->value('c_id');
    $master_customer_id = DB::table('customers')->value('id');
    
    $order_id = DB::table('customer_orders')->insertGetId([
        'order_name' => 'TEST-001',
        'order_date' => date('Y-m-d'),
        'customer_id' => $customer_account_id,
        'main_customer_id' => $master_customer_id,
        'customer_order' => 'TEST-001'
    ]);
}

echo "Adding test specification for Order ID: $order_id\n";

$detail_id = DB::table('customer_order_details')->insertGetId([
    'quality' => 'TEST QUALITY',
    'height' => '2.00',
    'width' => '3.00',
    'area' => '6.00',
    'unit_price' => 100.00,
    'total_amount' => 600.00,
    'currency_code' => 'USD',
    'exchange_rate' => 1,
    'current_status' => 'On loom',
    'customer_order_id' => $order_id,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "Successfully added Detail ID: $detail_id\n";
