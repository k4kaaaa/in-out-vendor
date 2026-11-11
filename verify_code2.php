<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

foreach (Route::getRoutes() as $route) {
    if (str_starts_with($route->uri(), 'api')) {
        echo $route->methods()[0] . ' ' . $route->uri() . PHP_EOL;
    }
}
echo 'Total API routes: ' . collect(Route::getRoutes())->filter(fn ($r) => str_starts_with($r->uri(), 'api'))->count() . PHP_EOL;
