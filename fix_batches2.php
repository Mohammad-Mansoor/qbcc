<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$batches = \App\ProductionBatch::whereNull('team_id')->get();
$updated = 0;
foreach($batches as $batch) {
    if ($batch->type == 'kachaee') {
        $repair = \App\CarpetRepair::where('kachaee_number', $batch->reference_number)->first();
        if ($repair && $repair->team_id) {
            $batch->team_id = $repair->team_id;
            $batch->save();
            $updated++;
        }
    } elseif ($batch->type == 'finish') {
        $finish = \App\FinishingWork::where('finish_number', $batch->reference_number)->first();
        // Wait, FinishingWork doesn't have team_id directly, it uses category_id or carpet's finishing_id.
        // Let's just find the first carpet that used this finish_number.
        if ($finish) {
            $carpet = \App\Carpet::where('carpet_id', $finish->carpetId)->first();
            if ($carpet && $carpet->finishing_id) {
                $batch->team_id = $carpet->finishing_id;
                $batch->save();
                $updated++;
            }
        }
    }
}
echo "Updated $updated batches.\n";
