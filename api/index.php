<?php

// Pastikan PATH_INFO berisi path lengkap (termasuk /api) agar Laravel routing benar
// Paksa SCRIPT_NAME ke /index.php agar Laravel tidak menganggap /api sebagai base path
$_SERVER['SCRIPT_NAME'] = '/index.php';
if (isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['PATH_INFO'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

// Vercel serverless hanya punya izin tulis di /tmp
// Kita pindahkan folder cache Laravel ke /tmp agar PackageManifest/PackageManifest dapat menulis file.
try {
    $tmpCacheDir = '/tmp/cache';
    if (!is_dir($tmpCacheDir)) {
        mkdir($tmpCacheDir, 0777, true);
    }
    chmod($tmpCacheDir, 0777);
    
    // Hapus cache lama agar setiap cold start mendapatkan konfigurasi terbaru
    foreach (glob($tmpCacheDir . '/*') as $file) {
        if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            @unlink($file);
        }
    }
    
    // Atur path cache melalui env vars (overwrites defaults ke /var/task/user/bootstrap/cache)
    putenv('APP_PACKAGES_CACHE=' . $tmpCacheDir . '/packages.php');
    putenv('APP_SERVICES_CACHE=' . $tmpCacheDir . '/services.php');
    putenv('APP_CONFIG_CACHE=' . $tmpCacheDir . '/config.php');
} catch (\Throwable $e) {
    // Jika gagal membuat cache dir, lanjutkan tanpa cache
}

// Pastikan direktori storage/framework views, cache, sessions ada dan dapat ditulis
$storagePath = '/tmp/storage/framework';
$folders = ['views', 'cache', 'sessions'];
foreach ($folders as $folder) {
    if (!is_dir($storagePath . '/' . $folder)) {
        mkdir($storagePath . '/' . $folder, 0777, true);
    }
}
chmod($storagePath, 0777);

// Set environment variable untuk view compiled path
putenv("VIEW_COMPILED_PATH=/tmp/storage/framework/views");

// Load composer autoloader setelah setup path agar class loading jalan
require __DIR__ . '/../vendor/autoload.php';

// Jalankan Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');
$app->handleRequest(Illuminate\Http\Request::capture());