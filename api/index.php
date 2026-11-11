<?php

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

// Pastikan direktori storage/framework/views, cache, sessions ada dan dapat ditulis
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
try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath('/tmp/storage');
    
    // Override exception handler untuk mengembalikan JSON error tanpa merender view
    $app->singleton(
        \Illuminate\Contracts\Debug\ExceptionHandler::class,
        function ($app) {
            return new class implements \Illuminate\Contracts\Debug\ExceptionHandler {
                public function report(\Throwable $e) {}
                public function shouldReport(\Throwable $e) { return false; }
                public function render($request, \Throwable $e) {
                    return new \Illuminate\Http\JsonResponse([
                        'message' => $e->getMessage(),
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => explode("\n", $e->getTraceAsString())
                    ], 500);
                }
                public function renderForConsole($output, \Throwable $e) {}
            };
        }
    );

    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString())
    ], JSON_PRETTY_PRINT);
}// deploy-trigger 
