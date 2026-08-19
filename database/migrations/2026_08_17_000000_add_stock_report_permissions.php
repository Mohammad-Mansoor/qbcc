<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddStockReportPermissions extends Migration
{
    public function up()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_carpet_stock_report',
            'view_raw_material_stock_report',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Assign to Super Admin and Admin roles if exists
        $adminRoles = Role::whereIn('name', ['Super Admin', 'Admin', 'SP'])->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo($permissions);
        }
    }

    public function down()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::whereIn('name', ['view_carpet_stock_report', 'view_raw_material_stock_report'])->delete();
    }
}
