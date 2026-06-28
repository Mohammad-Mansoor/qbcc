<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$batches = \App\ProductionBatch::whereNull('team_id')->get();
$updated = 0;
foreach($batches as $batch) {
    if ($batch->type == 'wash') {
        $wash = \App\CarpetWash::where('wash_number', $batch->reference_number)->first();
        if ($wash && $wash->team_id) {
            $batch->team_id = $wash->team_id;
            $batch->save();
            $updated++;
        }
    } elseif ($batch->type == 'kachaee') {
        // Find repair
        // We'll need to check how it's saved. But let's just do it for wash first.
    }
}
echo "Updated $updated wash batches.\n";
