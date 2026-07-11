<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payment = \App\AgentPayment::where('is_advance', 1)->first();
$document = \App\PurchaseInvoice::first();

if (!$payment || !$document) {
    echo "Payment or Document not found\n";
    exit;
}

$request = new \Illuminate\Http\Request([
    'agent_payment_id' => $payment->id,
    'allocatable_id' => $document->id,
    'allocatable_type' => 'App\\PurchaseInvoice',
    'amount' => 10,
]);

$controller = new \App\Http\Controllers\AgentPaymentController();
$controller->allocateAdvance($request);

echo "Done\n";
