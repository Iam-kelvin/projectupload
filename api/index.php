<?php

$storagePath = $_ENV['APP_STORAGE_PATH'] ?? $_SERVER['APP_STORAGE_PATH'] ?? '/tmp/laravel_storage';
$bootstrapCache = '/tmp/laravel_bootstrap_cache';

foreach ([
    $storagePath,
    $storagePath.'/app',
    $storagePath.'/app/projects',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
    $bootstrapCache,          // 👈 added
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

putenv('APP_STORAGE_PATH='.$storagePath);
$_ENV['APP_STORAGE_PATH'] = $storagePath;
$_SERVER['APP_STORAGE_PATH'] = $storagePath;

// 👇 Redirect bootstrap cache to writable /tmp
putenv('APP_PACKAGES_CACHE='.$bootstrapCache.'/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = $bootstrapCache.'/packages.php';
$_SERVER['APP_PACKAGES_CACHE'] = $bootstrapCache.'/packages.php';

putenv('APP_SERVICES_CACHE='.$bootstrapCache.'/services.php');
$_ENV['APP_SERVICES_CACHE'] = $bootstrapCache.'/services.php';
$_SERVER['APP_SERVICES_CACHE'] = $bootstrapCache.'/services.php';

require __DIR__.'/../public/index.php';