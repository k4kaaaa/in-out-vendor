<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$routes = app(\Illuminate\Routing\RouteCollection::class)->getRoutes();
foreach ($routes as $r) {
    if (str_starts_with($r->uri(), 'api')) {
        echo $r->methods()[0] . ' ' . $r->uri() . "\n";
    }
}
