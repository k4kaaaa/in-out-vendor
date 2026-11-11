<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate GET /api/debug/csrf
$request = Illuminate\Http\Request::create('http://localhost/api/debug/csrf', 'GET');
try {
    $response = $kernel->handle($request);
    echo "HTTP Status: " . $response->getStatusCode() . "\n";
    echo substr($response->getContent(), 0, 500) . "\n";
} catch (Throwable $e) {
    echo "Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
}

// Also simulate POST /api/login
$loginReq = Illuminate\Http\Request::create('http://localhost/api/login', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
], json_encode(['email' => 'admin@example.com', 'password' => 'password']));
try {
    $loginResp = $kernel->handle($loginReq);
    echo "\nLOGIN HTTP Status: " . $loginResp->getStatusCode() . "\n";
    echo substr($loginResp->getContent(), 0, 300) . "\n";
} catch (Throwable $e) {
    echo "LOGIN Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
