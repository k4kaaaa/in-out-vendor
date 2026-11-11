<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('http://localhost/api/login', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
], json_encode(['email' => 'admin@example.com', 'password' => 'password']));

try {
    $response = $kernel->handle($request);
    echo "HTTP Status: " . $response->getStatusCode() . "\n";
    echo substr($response->getContent(), 0, 500) . "\n";
} catch (Throwable $e) {
    echo "Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
