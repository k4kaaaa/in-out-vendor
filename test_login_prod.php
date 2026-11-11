<?php
$url = 'https://in-out-vendor-tau.vercel.app/api/login';
$data = json_encode(['email' => 'admin@example.com', 'password' => 'password']);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: {$httpCode}\n";
echo "Response: " . substr($response, 0, 400) . "\n";
curl_close($ch);