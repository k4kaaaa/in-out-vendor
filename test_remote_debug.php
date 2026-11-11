<?php
$endpoints = [
    '/api/debug/simple',
    '/api/debug/path',
    '/api/products',
    '/api/login',
    '/api/vendors',
];
foreach ($endpoints as $ep) {
    $url = 'https://in-out-vendor-tau.vercel.app' . $ep;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    if (strpos($ep, 'login') === 0 && substr($ep, -5) !== '.json') {
        // POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => 'a@b.c', 'password' => 'x']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($resp, 0, $headerSize);
    $body = substr($resp, $headerSize);
    echo "=== $ep [$httpCode] ===\n";
    // show first 200 chars of body
    echo "Body: " . substr(trim($body), 0, 200) . "\n\n";
    curl_close($ch);
}