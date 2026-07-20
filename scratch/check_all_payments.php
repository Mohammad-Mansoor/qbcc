<?php
$files = glob(__DIR__.'/../app/Http/Controllers/*.php');
$results = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'postAutoTransaction') !== false) {
        if (preg_match_all('/postAutoTransaction\s*\([^;]+;/', $content, $matches)) {
            foreach ($matches[0] as $match) {
                $hasCurrency = strpos($match, "'currency_code'") !== false;
                $hasExchange = strpos($match, "'exchange_rate'") !== false;
                $results[] = [
                    'controller' => basename($file),
                    'method' => '...',
                    'has_currency' => $hasCurrency,
                    'has_exchange' => $hasExchange,
                    'snippet' => substr($match, 0, 150)
                ];
            }
        }
    }
}

foreach($results as $r) {
    $status = ($r['has_currency'] && $r['has_exchange']) ? "✅ GOOD" : "❌ MISSING";
    echo str_pad($r['controller'], 40) . " | " . $status . "\n";
}
