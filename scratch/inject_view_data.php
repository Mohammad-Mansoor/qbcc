<?php
$file = '/home/anonymous/projects/public_html/app/Http/Controllers/CarpetsController.php';
$content = file_get_contents($file);

$methods = [
    'index', 
    'show_all_contract_carpet', 
    'search_contract_carpet', 
    'search_contract_carpet_by_agent',
    'listBuyCarpet',
    'show_all_buy_carpet',
    'search_buy_carpet',
    'editBuyCarpet'
];

foreach ($methods as $method) {
    // Inject the variable fetch
    $content = preg_replace(
        '/(public\s+function\s+' . $method . '\s*\([^)]*\)\s*\{)/',
        '$1' . "\n        \$inventoryAccounts = \$this->accountSelectionService->getValidAccounts('CARPET_INVENTORY', 'debit');",
        $content
    );
    
    // Inject it into the compact()
    // Note: This regex is simple, might need refinement if compact() is complex
    $content = preg_replace(
        '/(return\s+view\([^)]+compact\([^)]+)(\)\);)/',
        '$1, \'inventoryAccounts\'$2',
        $content
    );
}

file_put_contents($file, $content);
echo "View data injected\n";
