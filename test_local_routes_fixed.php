<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate GET /api/debug/simple
$request = Illuminate\Http\Request::create('http://localhost/api/debug/simple', 'GET');
try {
    $response = $kernel->handle($request);
    echo "DEBUG SIMPLE Status: " . $response->getStatusCode() . "\n";
    echo substr($response->getContent(), 0, 300) . "\n\n";
} catch (Throwable $e) {
    echo "DEBUG SIMPLE Exception: " . get_class($e) . ": " . $e->getMessage() . "\n\n";
}

// Simulate POST /api/login
$loginReq = Illuminate\Http\Request::create('http://localhost/api/login', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'admin@example.com', 'password' => 'password']));
try {
    $loginResp = $kernel->handle($loginReq);
    echo "LOGIN Status: " . $loginResp->getStatusCode() . "\n";
    echo substr($loginResp->getContent(), 0, 300) . "\n\n";
} catch (Throwable $e) {
    echo "LOGIN Exception: " . get_class($e) . ": " . $e->getMessage() . "\n\n";
}