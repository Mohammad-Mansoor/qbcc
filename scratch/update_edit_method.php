<?php
$file = '/home/anonymous/projects/public_html/app/Http/Controllers/CarpetsController.php';
$content = file_get_contents($file);

$method = 'edit';

// Inject the variable fetch
$content = preg_replace(
    '/(public\s+function\s+' . $method . '\s*\([^)]*\)\s*\{)/',
    '$1' . "\n        \$inventoryAccounts = \$this->accountSelectionService->getValidAccounts('CARPET_INVENTORY', 'debit');",
    $content
);

// Inject it into the compact()
$content = preg_replace(
    '/(return\s+view\(\'carpets\.contract-carpet-list\'[^)]+compact\([^)]+)(\)\);)/',
    '$1, \'inventoryAccounts\'$2',
    $content
);

file_put_contents($file, $content);
echo "Edit method updated\n";
