<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateInventoryReportGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update accounts to have 'Inventory' report group for consistent filtering
        DB::table('chart_of_accounts')
            ->whereIn('account_code', ['1400', '1410', '1420'])
            ->orWhereIn('account_name', ['Inventory', 'Work In Progress (WIP)', 'Finished Goods'])
            ->update(['report_group' => 'Inventory']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to 'Current Asset'
        DB::table('chart_of_accounts')
            ->where('report_group', 'Inventory')
            ->update(['report_group' => 'Current Asset']);
    }
}
