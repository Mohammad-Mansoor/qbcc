<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$item = DB::table('items')->where('type', 'App\Carpet')->where('ref_id', 1)->first();
print_r($item);

$carpet = DB::table('carpets')->where('carpet_id', 1)->first();
print_r($carpet);

