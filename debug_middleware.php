<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Check static state of PreventRequestForgery
$ref = new ReflectionClass(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
$prop = $ref->getProperty('except');
$prop->setAccessible(true);
$neverVerify = $ref->getStaticPropertyValue('neverVerify');
echo "Static neverVerify: " . print_r($neverVerify, true) . "\n";

// Check instance property
$inst = new \Illuminate\Foundation\Http\Middleware\PreventRequestForgery($app, $app->make(Illuminate\Contracts\Encryption\Encrypter::class));
$val = $prop->getValue($inst);
echo "Instance except: " . print_r($val, true) . "\n";

// Call getExcludedPaths to see merged paths
$paths = $inst->getExcludedPaths();
echo "Combined excluded paths: " . print_r($paths, true) . "\n";

// Test matching
$request = Illuminate\Http\Request::create('https://localhost/api/login', 'POST');
echo "Request path: " . $request->path() . "\n";
echo "is('api/*'): " . ($request->is('api/*') ? 'true' : 'false') . "\n";
echo "fullUrlIs('api/*'): " . ($request->fullUrlIs('api/*') ? 'true' : 'false') . "\n";
