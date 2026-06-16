<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$pm = \App\Carpet::first();
echo json_encode($pm ? array_keys($pm->toArray()) : 'null');
