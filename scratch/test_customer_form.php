<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Validator;
use App\Customer;
use Illuminate\Support\Facades\DB;

echo "Running customer validation and database insertion tests...\n";

// 1. Validation Rules from CustomerController
$rules = [
    'customer_code' => 'required|min:2|max:256',
    'name' => 'required|min:2|max:256',
    'type' => 'required|min:2|max:256',
    'company_name' => 'required|min:2|max:256',
    'company_address' => 'required|min:2|max:256',
    'phone' => 'required|min:3|max:14',
    'email' => '',
    'website' => '',
];

// Test Case A: company_address is missing
$dataA = [
    'customer_code' => 'C-TEST-99',
    'name' => 'John Doe',
    'type' => 'مشتری قالین',
    'company_name' => 'Doe Corp',
    // 'company_address' is missing
    'phone' => '0700112233',
];

$validatorA = Validator::make($dataA, $rules);
if ($validatorA->fails()) {
    echo "[PASS] Test A: Validation correctly failed when company_address is missing. Errors: \n";
    print_r($validatorA->errors()->all());
} else {
    echo "[FAIL] Test A: Validation passed even when company_address was missing.\n";
}

// Test Case B: company_address is provided
$dataB = [
    'customer_code' => 'C-TEST-99',
    'name' => 'John Doe',
    'type' => 'مشتری قالین',
    'company_name' => 'Doe Corp',
    'company_address' => 'Kabul, Afghanistan',
    'phone' => '0700112233',
];

$validatorB = Validator::make($dataB, $rules);
if ($validatorB->passes()) {
    echo "[PASS] Test B: Validation correctly passed when company_address is provided.\n";
} else {
    echo "[FAIL] Test B: Validation failed even when company_address was provided. Errors: \n";
    print_r($validatorB->errors()->all());
}

// Test Case C: DB transaction create & rollback to verify database compatibility
try {
    DB::beginTransaction();
    $customer = Customer::create([
        'customer_code' => 'C-TEST-AUTO',
        'name' => 'Test Customer',
        'type' => 'مشتری قالین',
        'company_name' => 'Test Company',
        'company_address' => 'Test Address',
        'phone' => '0799999999',
    ]);
    
    if ($customer && $customer->id) {
        echo "[PASS] Test C: Customer successfully created in database with company_address (ID: {$customer->id}).\n";
    } else {
        echo "[FAIL] Test C: Customer creation returned empty.\n";
    }
    
    DB::rollBack();
    echo "DB Transaction rolled back successfully.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "[FAIL] Test C: Exception during customer database insertion: " . $e->getMessage() . "\n";
}
