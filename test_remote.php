<?php
$endpoints = [
    'products' => 'https://in-out-vendor-tau.vercel.app/api/products',
    'login' => 'https://in-out-vendor-tau.vercel.app/api/login'
];

foreach ($endpoints as $name => $url) {
    echo "Testing $name: $url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($name === 'login') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => 'admin@example.com', 'password' => 'password']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Status: $code\n";
    echo "Body: " . substr($resp, 0, 500) . "\n\n";
}
