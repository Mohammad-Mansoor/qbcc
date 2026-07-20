<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sale = \App\Sale::orderBy('id', 'desc')->first();
if ($sale) {
    $cogsTx = DB::table('ledger_transactions')
        ->where('source_type', 'App\Carpet')
        ->where('source_id', $sale->carpet_id)
        ->where('mapping_key', 'SALES_COGS')
        ->first();
        
    if ($cogsTx) {
        $carpet = \App\Carpet::find($sale->carpet_id);
        $correctCost = $carpet->total_price;
            
        DB::table('ledger_entries')
            ->where('transaction_id', $cogsTx->id)
            ->update([
                'original_amount' => $correctCost,
                'base_currency_amount' => $correctCost,
                'debit' => DB::raw("CASE WHEN debit > 0 THEN $correctCost ELSE 0 END"),
                'credit' => DB::raw("CASE WHEN credit > 0 THEN $correctCost ELSE 0 END"),
                'base_debit' => DB::raw("CASE WHEN base_debit > 0 THEN $correctCost ELSE 0 END"),
                'base_credit' => DB::raw("CASE WHEN base_credit > 0 THEN $correctCost ELSE 0 END"),
            ]);
            
        echo "COGS Fixed to: $correctCost\n";
    }
}
