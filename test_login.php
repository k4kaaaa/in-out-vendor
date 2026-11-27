<?php
$url = 'https://in-out-vendor-tau.vercel.app/api/auth/login';
echo ">> login: $url\n";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => 'admin@example.com', 'password' => 'password']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Status: $code\n";
echo "Body: " . $resp . "\n\n";