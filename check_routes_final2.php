<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Force app boot and flush caches
$app->boot();

echo "Routes loaded: " . count(app('router')->getRoutes()) . "\n";
foreach (app('router')->getRoutes() as $r) {
    if (str_starts_with($r->uri(), 'api')) {
        echo $r->methods()[0] . ' ' . $r->uri() . "\n";
    }
}