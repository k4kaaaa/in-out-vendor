<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$ref = new ReflectionClass(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
$neverVerify = $ref->getStaticPropertyValue('neverVerify');
echo "NeverVerify after boot: "; print_r($neverVerify);

// Check middleware aliases/state in app
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "Kernel type: " . get_class($kernel) . "\n";

// Simulate a POST to /api/login and observe behavior
$request = Illuminate\Http\Request::create('https://localhost/api/login', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
], json_encode(['email' => 'a@b.c', 'password' => 'x']));

try {
    $response = $kernel->handle($request);
    echo "HTTP Status: " . $response->getStatusCode() . "\n";
    echo substr($response->getContent(), 0, 300) . "\n";
} catch (Throwable $e) {
    echo "Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
