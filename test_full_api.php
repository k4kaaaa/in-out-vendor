<?php
$base = 'https://in-out-vendor-tau.vercel.app/api';
echo "=== 1. TEST LOGIN ===\n";
$ch = curl_init("$base/auth/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => 'admin@inoutvendor.local', 'password' => 'password123']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Status: $code\n";
echo "Response: $resp\n\n";

$data = json_decode($resp, true);
$token = $data['token'] ?? null;
echo "Token: " . ($token ?? 'none') . "\n\n";

echo "=== 2. TEST PROTECTED ENDPOINTS ===\n";
$endpoints = ['products', 'vendors', 'transactions'];
foreach ($endpoints as $endpoint) {
    $ch = curl_init("$base/$endpoint");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    if ($token) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    }
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "GET /$endpoint -> Status: $code\n";
    echo "Response: " . substr($resp, 0, 200) . "\n\n";
}