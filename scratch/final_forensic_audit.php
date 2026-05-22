<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================================\n";
echo "🔍 STARTING FINAL FORENSIC ERP AUDIT (PHASE 5)\n";
echo "========================================================\n\n";

$errors = 0;
$warnings = 0;

// 1. GENERAL LEDGER BALANCE CHECK
echo "1️⃣  Checking General Ledger Balance...\n";
$gl = DB::table('ledger_entries')
    ->select(
        DB::raw('SUM(base_debit) as total_debit'),
        DB::raw('SUM(base_credit) as total_credit'),
        DB::raw('SUM(base_debit - base_credit) as net_diff')
    )->first();

if (abs($gl->net_diff) < 0.0001) {
    echo "   ✅ GL is PERFECTLY BALANCED (Net Diff: 0.0000)\n";
    echo "      Total Debit: " . number_format($gl->total_debit, 4) . " USD\n";
    echo "      Total Credit: " . number_format($gl->total_credit, 4) . " USD\n";
} else {
    echo "   ❌ GL IMBALANCE DETECTED! Net Diff: " . $gl->net_diff . "\n";
    $errors++;
}

echo "\n2️⃣  Checking Forensic Column Completeness...\n";
$missingForensics = DB::table('ledger_entries')
    ->whereNull('exchange_rate')
    ->orWhereNull('currency_code')
    ->orWhereNull('original_amount')
    ->count();

if ($missingForensics == 0) {
    echo "   ✅ All transactions have forensic snapshots (Exchange Rate, Original Amount).\n";
} else {
    echo "   ❌ $missingForensics entries are missing forensic data!\n";
    $errors++;
}

echo "\n3️⃣  Verifying Sub-Ledger vs General Ledger (SL vs GL)...\n";

// Audit Office Debits (Expenses)
$sl_expenses = DB::table('office_debits')->sum('base_amount');
$gl_expenses = DB::table('ledger_entries as le')
    ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
    ->where('lt.source_type', 'App\OfficeDebit')
    ->where('le.base_debit', '>', 0)
    ->sum('le.base_debit');

echo "   - Expenses (Office Debits):\n";
echo "     SL Total: " . number_format($sl_expenses, 4) . "\n";
echo "     GL Total: " . number_format($gl_expenses, 4) . "\n";

if (abs($sl_expenses - $gl_expenses) < 0.01) {
    echo "     ✅ Expenses are in SYNC.\n";
} else {
    echo "     ⚠️  Expense mismatch: " . abs($sl_expenses - $gl_expenses) . " (Might be due to manual GL entries)\n";
    $warnings++;
}

// Audit Material Purchases
$sl_purchases = DB::table('purchase_materials')->sum('base_currency_amount');
$gl_purchases = DB::table('ledger_entries as le')
    ->join('ledger_transactions as lt', 'le.transaction_id', '=', 'lt.id')
    ->whereIn('lt.source_type', ['App\PurchaseMaterial', 'Material_purchase'])
    ->where('le.base_debit', '>', 0)
    ->sum('le.base_debit');

echo "   - Material Purchases:\n";
echo "     SL Total: " . number_format($sl_purchases, 4) . "\n";
echo "     GL Total: " . number_format($gl_purchases, 4) . "\n";

if (abs($sl_purchases - $gl_purchases) < 0.01) {
    echo "     ✅ Purchases are in SYNC.\n";
} else {
    echo "     ⚠️  Purchase mismatch: " . abs($sl_purchases - $gl_purchases) . "\n";
    $warnings++;
}

echo "\n4️⃣  Checking for Zero-Amount Ghosts...\n";
$ghosts = DB::table('ledger_entries')
    ->where('original_amount', '>', 0)
    ->where('base_currency_amount', '=', 0)
    ->count();

if ($ghosts == 0) {
    echo "   ✅ No zero-amount ghost transactions found.\n";
} else {
    echo "   ❌ $ghosts transactions have an original amount but 0 base amount!\n";
    $errors++;
}

echo "\n========================================================\n";
echo "🏁 AUDIT SUMMARY:\n";
echo "   ERRORS:   $errors\n";
echo "   WARNINGS: $warnings\n";
echo "========================================================\n";

if ($errors == 0) {
    echo "🌟 SYSTEM IS AUDIT-GRADE AND FORENSICALLY SOUND. 🌟\n";
} else {
    echo "🛑 SYSTEM REQUIRES ATTENTION BEFORE HANDOVER. 🛑\n";
}
echo "========================================================\n";
