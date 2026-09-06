<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions to ensure we get a fresh state
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create the user
        $user = User::updateOrCreate(
            ['email' => env('EMAIL', 'qbc1@live.com')],
            [
                'name' => env('NAME', 'QBIC'),
                'last_name' => env('LASTNAME', 'QBIC'),
                'role' => 'SP',
                'password' => bcrypt(env('PASSWORD', 'QBIC22000@1500af')),
            ]
        );

        // 2. Create the Super Admin role
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // 3. Assign ALL available permissions to this role
        $role->syncPermissions(Permission::all());

        // 4. Assign the role to the user
        $user->assignRole($role);
    }
}
