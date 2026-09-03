<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/dashboard/accounting/journals', 'POST', [
    'journal_id' => 'JV-2026-99999',
    'date' => '2026-09-01',
    'description' => 'Test Transaction',
    'journal_type' => 'journal',
    'entries' => [
        ['account_id' => 1, 'debit' => 100, 'credit' => 0],
        ['account_id' => 2, 'debit' => 0, 'credit' => 100],
    ]
]);

$controller = $app->make('App\Http\Controllers\Accounting\JournalController');
try {
    $response = $controller->store($request);
    print_r($response);
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Validation Error:\n";
    print_r($e->errors());
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
