<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rc = app('App\Http\Controllers\RoleController');
$rc_class = new ReflectionClass($rc);
$method = $rc_class->getMethod('getPermissionGroups');
$method->setAccessible(true);

$perms = Spatie\Permission\Models\Permission::where('name', 'like', '%employee%')->orWhere('name', 'like', '%payroll%')->get();
$groups = $method->invoke($rc, $perms);

foreach($groups as $name => $data) {
    if(count($data['perms']) > 0) {
        echo strtoupper($name) . ":\n";
        foreach($data['perms'] as $p) {
            echo "  - " . $p->name . "\n";
        }
    }
}
