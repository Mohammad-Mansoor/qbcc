<?php
$seeder = file_get_contents("database/seeds/PermissionSeeder.php");
preg_match_all("/'([a-zA-Z0-9_]+)'/", $seeder, $matches);
$seeder_perms = array_unique($matches[1]);

$code_perms = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    if (!preg_match('/\.(php|blade\.php)$/', $path)) continue;
    if (strpos($path, './vendor') === 0 || strpos($path, './storage') === 0) continue;

    $content = file_get_contents($path);
    if (preg_match_all("/can\('([a-zA-Z0-9_]+)'\)/", $content, $m)) {
        foreach($m[1] as $p) $code_perms[] = $p;
    }
    if (preg_match_all("/hasPermissionTo\('([a-zA-Z0-9_]+)'\)/", $content, $m)) {
        foreach($m[1] as $p) $code_perms[] = $p;
    }
    if (preg_match_all("/permission:([a-zA-Z0-9_\|]+)/", $content, $m)) {
        foreach($m[1] as $p) {
            foreach(explode('|', $p) as $p2) $code_perms[] = $p2;
        }
    }
}
$code_perms = array_unique($code_perms);

$missing = array_diff($code_perms, $seeder_perms);
print_r(array_values($missing));
