<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('https://localhost/api/login', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
], json_encode(['email' => 'a@b.c', 'password' => 'x']));

try {
    $response = $kernel->handle($request);
    echo "STATUS: " . $response->getStatusCode() . "\n";
    echo substr($response->getContent(), 0, 500) . "\n";
} catch (Throwable $e) {
    echo "THROWN: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
