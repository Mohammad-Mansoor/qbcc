<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

$admin = User::firstOrCreate(
    ['email' => 'admin@admin.com'],
    [
        'name' => 'Super Admin',
        'last_name' => 'User',
        'password' => Hash::make('password'),
        'role' => 'SP'
    ]
);

$role = Role::findByName('Super Admin');
$admin->assignRole($role);

echo "Super Admin user created/updated successfully. Email: admin@admin.com Password: password\n";
