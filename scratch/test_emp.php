<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entries = DB::table('ledger_entries')
    ->where('party_type', 'App\OfficeEmployee')
    ->where('party_id', 1)
    ->get();
    
print_r($entries->toArray());
