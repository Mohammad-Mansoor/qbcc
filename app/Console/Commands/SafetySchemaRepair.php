<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SafetySchemaRepair extends Command
{
    protected $signature = 'safety:repair-schema';
    protected $description = 'Repair common database schema issues like status column type mismatches';

    public function handle()
    {
        $this->info("🛠️ Starting Schema Repair...");

        // Fix status columns for basic entities (these should be INT)
        $tables = [
            'customers', 'string_sellers', 'washing_teams', 'finishing_teams', 
            'agents', 'chart_of_accounts'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'status')) {
                try {
                    $this->info("Fixing entity status: $table");
                    DB::statement("ALTER TABLE `$table` MODIFY COLUMN `status` INT DEFAULT 1");
                } catch (\Exception $e) {
                    $this->warn("Could not modify status for $table: " . $e->getMessage());
                }
            }
        }

        // Fix ledger_transactions status (must be ENUM)
        if (Schema::hasTable('ledger_transactions')) {
            $this->info("Restoring ledger_transactions status to ENUM...");
            try {
                DB::statement("ALTER TABLE `ledger_transactions` MODIFY COLUMN `status` ENUM('draft','posted','reversed') DEFAULT 'posted'");
            } catch (\Exception $e) {
                $this->warn("Could not modify ledger_transactions status: " . $e->getMessage());
            }
            
            if (!Schema::hasColumn('ledger_transactions', 'journal_type')) {
                DB::statement("ALTER TABLE `ledger_transactions` ADD COLUMN `journal_type` ENUM('sales','purchase','payment','receipt','journal') DEFAULT 'journal'");
            }
        }

        $this->info("✅ Schema repair completed!");
    }
}
