<?php
// Verify deployed-like state: load app, count API routes, print first few URIs
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$routes = app('router')->getRoutes();
$count = 0;
foreach ($routes as $route) {
    if (str_starts_with($route->uri(), 'api')) {
        $count++;
        if ($count <= 10) {
            echo $route->methods()[0] . ' ' . $route->uri() . "\n";
        }
    }
}
echo "Total API routes: $count\n";
