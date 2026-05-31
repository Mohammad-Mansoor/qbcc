<?php

// Bootstrap Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StringSellerController;
use Illuminate\Http\Request;

try {
    echo "1. Checking database connection and currencies...\n";
    $currencies = \App\Currency::all();
    echo "   Found " . $currencies->count() . " currencies.\n";

    // Share empty errors bag for CLI execution
    view()->share('errors', new \Illuminate\Support\ViewErrorBag());

    // Mock logged in user
    $user = \App\User::first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
    }

    echo "2. Bootstrapping StringSellerController...\n";
    $controller = new StringSellerController();

    echo "3. Rendering index page...\n";
    $response = $controller->index();
    $html = $response->render();
    echo "   Index rendered successfully: " . strlen($html) . " bytes.\n";

    echo "4. Rendering search page...\n";
    $request = new Request(['search' => 'Test']);
    $responseSearch = $controller->search($request);
    $htmlSearch = $responseSearch->render();
    echo "   Search rendered successfully: " . strlen($htmlSearch) . " bytes.\n";

    echo "5. Rendering accounts page...\n";
    $responseAccounts = $controller->accounts();
    $htmlAccounts = $responseAccounts->render();
    echo "   Accounts rendered successfully: " . strlen($htmlAccounts) . " bytes.\n";

    echo "\n🥇 ALL VIEWS COMPILED & RENDERED PERFECTLY!\n";

} catch (\Exception $e) {
    echo "\n❌ EXCEPTION DETECTED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
