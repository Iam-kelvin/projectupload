<?php

$storagePath = $_ENV['APP_STORAGE_PATH'] ?? $_SERVER['APP_STORAGE_PATH'] ?? '/tmp/laravel_storage';

foreach ([
    $storagePath,
    $storagePath.'/app',
    $storagePath.'/app/projects',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

putenv('APP_STORAGE_PATH='.$storagePath);
$_ENV['APP_STORAGE_PATH'] = $storagePath;
$_SERVER['APP_STORAGE_PATH'] = $storagePath;

require __DIR__.'/../public/index.php';
