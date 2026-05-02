<?php
$logFile = __DIR__.'/storage/logs/missing_columns.log';
$cleanedLogFile = __DIR__.'/storage/logs/missing_columns_cleaned.log';

if (!file_exists($logFile)) {
    die("Log file not found.\n");
}

$content = file_get_contents($logFile);
// Split by the separator
$blocks = explode("-----------------------------------", $content);

$uniqueKeys = [];
$cleanedBlocks = [];
$totalEntries = 0;
$duplicates = 0;
$uniqueIssues = 0;

foreach ($blocks as $block) {
    $block = trim($block);
    if (empty($block)) continue;
    
    $totalEntries++;
    
    $table = null;
    $column = null;
    
    if (preg_match('/Table:\s*(.+)/', $block, $tableMatches)) {
        $table = trim($tableMatches[1]);
    }
    
    if (preg_match('/Missing Column:\s*(.+)/', $block, $colMatches)) {
        $column = trim($colMatches[1]);
    }
    
    if ($table && $column) {
        if ($column === 'non_existent_column') {
            $duplicates++; // Treat as filtered/duplicate
            continue;
        }
        
        $key = $table . '_' . $column;
        if (!isset($uniqueKeys[$key])) {
            $uniqueKeys[$key] = true;
            $cleanedBlocks[] = "-----------------------------------\n" . $block . "\n";
            $uniqueIssues++;
        } else {
            $duplicates++;
        }
    }
}

file_put_contents($cleanedLogFile, implode("\n", $cleanedBlocks) . "\n");

echo "Total Entries Read: $totalEntries\n";
echo "Duplicates Removed: $duplicates\n";
echo "Unique Issues Remaining: $uniqueIssues\n";
