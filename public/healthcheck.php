<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
http_response_code(200);

echo json_encode([
    'status' => 'ok',
    'app'    => env('APP_NAME', 'laravel'),
    'time'   => (new DateTime())->format('Y-m-d H:i:s'),
]);
