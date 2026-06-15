<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/dashboard/accounting/reports/different-account-statement?export=pdf&entity_id=1', 'GET');
$response = $kernel->handle($request);
echo "Status: " . $response->status() . "\n";
if($response->status() == 200) {
    echo "Content Length: " . strlen($response->getContent()) . "\n";
} else {
    echo "Content: " . substr($response->getContent(), 0, 500) . "\n";
}
