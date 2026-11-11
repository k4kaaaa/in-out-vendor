<?php

// Pastikan autoload ada
require __DIR__ . '/../vendor/autoload.php';

// Vercel serverless hanya punya izin tulis di /tmp
// Kita pindahkan folder storage ke /tmp agar Laravel bisa menulis cache/views
$storagePath = '/tmp/storage/framework';
$folders = ['views', 'cache', 'sessions'];

foreach ($folders as $folder) {
    if (!is_dir($storagePath . '/' . $folder)) {
        mkdir($storagePath . '/' . $folder, 0777, true);
    }
}

// Set environment variable untuk storage Laravel
putenv("VIEW_COMPILED_PATH=/tmp/storage/framework/views");

// Jalankan Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');

$app->handleRequest(Illuminate\Http\Request::capture());
