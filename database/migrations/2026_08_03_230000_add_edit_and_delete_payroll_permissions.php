<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddEditAndDeletePayrollPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = now();
        $permissions = [
            ['name' => 'edit_payroll', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'delete_payroll', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($permissions as $perm) {
            $permRecord = DB::table('permissions')->where('name', $perm['name'])->first();
            if (!$permRecord) {
                $permId = DB::table('permissions')->insertGetId($perm);
            } else {
                $permId = $permRecord->id;
            }

            $roles = DB::table('roles')->whereIn('name', ['Super Admin', 'Finance', 'Central Office', 'Central Office & Customers'])->get();
            foreach ($roles as $role) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permId,
                    'role_id'       => $role->id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permIds = DB::table('permissions')->whereIn('name', ['edit_payroll', 'delete_payroll'])->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('name', ['edit_payroll', 'delete_payroll'])->delete();
    }
}
