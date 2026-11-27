<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Check route for /api/debug/simple
$route = app('router')->getRoutes()->getByName('');
$request = Illuminate\Http\Request::create('http://localhost/api/debug/simple', 'GET');
try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo substr($response->getContent(), 0, 400) . "\n";
} catch (Throwable $e) {
    echo "Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
}