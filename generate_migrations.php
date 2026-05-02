<?php
$tables = [
    'agent_payments' => ['status' => 'tinyInteger'],
    'different_account_payments' => ['status' => 'tinyInteger'],
    'carpets' => ['parcha_number' => 'string'],
    'kachaee_payments' => ['status' => 'tinyInteger'],
    'washing_payments' => ['status' => 'tinyInteger'],
    'finishing_team_payments' => ['status' => 'tinyInteger'],
    'purchase_materials' => ['status' => 'tinyInteger'],
    'material_sales' => ['status' => 'tinyInteger'],
    'seller_payments' => ['status' => 'tinyInteger'],
    'employee_payments' => ['status' => 'tinyInteger'],
];

foreach ($tables as $table => $columns) {
    $migrationName = "add_missing_columns_to_{$table}_table";
    echo shell_exec("/opt/lampp/bin/php artisan make:migration {$migrationName} --table={$table}");
    
    // Find the latest generated migration for this table
    $files = glob(__DIR__."/database/migrations/*_{$migrationName}.php");
    $file = end($files);
    if ($file) {
        $content = file_get_contents($file);
        
        $upCols = "";
        $downCols = [];
        foreach ($columns as $col => $type) {
            if ($type === 'tinyInteger') {
                $upCols .= "\$table->tinyInteger('{$col}')->default(1)->nullable();\n            ";
            } else {
                $upCols .= "\$table->{$type}('{$col}')->nullable();\n            ";
            }
            $downCols[] = "'{$col}'";
        }
        
        $upPattern = '/public function up\(\)\s*\{\s*Schema::table\(\''.$table.'\', function \(Blueprint \$table\) \{\s*\/\//s';
        $upReplacement = "public function up()\n    {\n        Schema::table('{$table}', function (Blueprint \$table) {\n            {$upCols}";
        
        $downPattern = '/public function down\(\)\s*\{\s*Schema::table\(\''.$table.'\', function \(Blueprint \$table\) \{\s*\/\//s';
        $downReplacement = "public function down()\n    {\n        Schema::table('{$table}', function (Blueprint \$table) {\n            \$table->dropColumn([" . implode(", ", $downCols) . "]);";
        
        $content = preg_replace($upPattern, $upReplacement, $content);
        $content = preg_replace($downPattern, $downReplacement, $content);
        
        file_put_contents($file, $content);
        echo "Updated " . basename($file) . "\n";
    }
}
