<?php
$content = file_get_contents(__DIR__.'/../app/Services/InventoryTransactionManager.php');
if (preg_match_all('/postAutoTransaction\s*\([^;]+;/', $content, $matches)) {
    foreach ($matches[0] as $match) {
        $hasCurrency = strpos($match, "'currency_code'") !== false;
        $hasExchange = strpos($match, "'exchange_rate'") !== false;
        echo "Has Currency: " . ($hasCurrency ? 'Yes' : 'No') . "\n";
        echo "Has Exchange: " . ($hasExchange ? 'Yes' : 'No') . "\n";
        echo substr($match, 0, 200) . "...\n\n";
    }
}
