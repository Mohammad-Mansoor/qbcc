<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "CustomerAccountOrder (customer_account_orders):\n";
print_r(Schema::getColumnListing('customer_account_orders'));
echo "\nCustomerOrder (customer_orders):\n";
print_r(Schema::getColumnListing('customer_orders'));
echo "\nCustomerOrderDetails (customer_order_details):\n";
print_r(Schema::getColumnListing('customer_order_details'));
