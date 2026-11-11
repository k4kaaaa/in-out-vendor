<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$routes = app(\Illuminate\Routing\RouteRegistrar::class)->getRoutes()->getRoutes();
foreach ($routes as $route) {
    if (str_starts_with($route->uri(), 'api')) {
        echo $route->methods()[0] . ' ' . $route->uri() . " => " . ($route->action['uses'] ?? 'closure') . "\n";
    }
}
