<?php
require __DIR__ . '/vendor/autoload.php';

// Check before bootstrapping
$ref = new ReflectionClass(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
$before = $ref->getStaticPropertyValue('neverVerify');
echo "BEFORE load bootstrap - neverVerify: "; print_r($before);

$app = require_once __DIR__ . '/bootstrap/app.php';

$after = $ref->getStaticPropertyValue('neverVerify');
echo "AFTER load bootstrap - neverVerify: "; print_r($after);

// Directly call except and check again
\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::except(['api/*']);
$direct = $ref->getStaticPropertyValue('neverVerify');
echo "After direct call - neverVerify: "; print_r($direct);
