<?php
$base = 'https://in-out-vendor-tau.vercel.app/api';
$tests = [
    ['name' => 'login', 'method' => 'POST', 'url' => $base . '/login', 'body' => json_encode(['email' => 'admin@example.com', 'password' => 'password']), 'headers' => ['Content-Type: application/json']],
    ['name' => 'products', 'method' => 'GET', 'url' => $base . '/products', 'body' => null, 'headers' => []],
];

foreach ($tests as $t) {
    echo ">> {$t['name']}: {$t['method']} {$t['url']}\n";
    $ch = curl_init($t['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $t['method']);
    if ($t['body']) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $t['body']);
    }
    if ($t['headers']) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $t['headers']);
    }
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Status: $code\n";
    echo "Body: " . substr($resp, 0, 500) . "\n\n";
}