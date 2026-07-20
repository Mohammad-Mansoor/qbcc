<?php
$files = glob(__DIR__.'/../app/Http/Controllers/*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'postAutoTransaction') !== false) {
        if (preg_match_all('/postAutoTransaction\([^;]+;/', $content, $matches)) {
            foreach ($matches[0] as $match) {
                if (strpos($match, "'currency_code'") === false) {
                    echo basename($file) . " misses currency_code:\n" . substr($match, 0, 100) . "...\n\n";
                }
            }
        }
    }
}
