<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddEditFinishingWorkPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = now();
        $perm = ['name' => 'edit_finishing_work', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now];

        $permRecord = DB::table('permissions')->where('name', $perm['name'])->first();
        if (!$permRecord) {
            $permId = DB::table('permissions')->insertGetId($perm);
        } else {
            $permId = $permRecord->id;
        }

        $roles = DB::table('roles')->whereIn('name', ['Super Admin', 'Central Office', 'Central Office & Customers'])->get();
        foreach ($roles as $role) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permId,
                'role_id'       => $role->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permId = DB::table('permissions')->where('name', 'edit_finishing_work')->value('id');
        if ($permId) {
            DB::table('role_has_permissions')->where('permission_id', $permId)->delete();
            DB::table('permissions')->where('id', $permId)->delete();
        }
    }
}
