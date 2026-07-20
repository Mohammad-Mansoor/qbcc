<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$carpet = App\Carpet::with(['carpet_wash', 'finishing_works'])->where('carpet_no', 'QB1000')->first();
if(!$carpet) { echo "Carpet QB1000 not found\n"; exit; }

echo "--- CARPET ---\n";
print_r($carpet->toArray());

echo "--- KACHAEE WORKS ---\n";
print_r(DB::table('carpet_kachaees')->where('carpetId', $carpet->carpet_id)->get()->toArray());

echo "--- WASHING WORKS ---\n";
print_r($carpet->carpet_wash ? $carpet->carpet_wash->toArray() : null);

echo "--- FINISHING WORKS ---\n";
print_r($carpet->finishing_works->toArray());

