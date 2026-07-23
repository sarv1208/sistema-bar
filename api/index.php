<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Bar-Assistant-Bar-Id, Accept, Origin');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Prepare /tmp storage for Vercel serverless environment
$tmpStorage = '/tmp/storage';
$directories = [
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/bootstrap/cache',
    $tmpStorage . '/logs',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

require __DIR__ . '/../public/index.php';
