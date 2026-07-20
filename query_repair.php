<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$repairs = \App\CarpetRepair::orderBy('id', 'desc')->take(3)->get()->toArray();
print_r($repairs);
