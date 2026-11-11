<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Access private property global via reflection
$ref = new ReflectionClass($kernel);
$prop = $ref->getProperty('middleware');
$prop->setAccessible(true);
$global = $prop->getValue($kernel);
echo "Global middleware count: " . count($global) . "\n";
foreach ($global as $m) {
    echo " - $m\n";
}