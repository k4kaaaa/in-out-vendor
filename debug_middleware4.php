<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$ref = new ReflectionClass(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
echo "Before kernel: "; print_r($ref->getStaticPropertyValue('neverVerify'));

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "After kernel make: "; print_r($ref->getStaticPropertyValue('neverVerify'));

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
