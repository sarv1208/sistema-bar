<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    header("Access-Control-Allow-Origin: {$origin}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Bar-Assistant-Bar-Id, Accept, Origin');
    http_response_code(200);
    exit(0);
}

// Override read-only storage paths and drivers for Vercel serverless environment
$_ENV['LOG_CHANNEL'] = 'stderr';
putenv('LOG_CHANNEL=stderr');
$_ENV['CACHE_STORE'] = 'array';
putenv('CACHE_STORE=array');
$_ENV['CACHE_DRIVER'] = 'array';
putenv('CACHE_DRIVER=array');

// Fix Vercel SCRIPT_NAME for Laravel route matching
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// Prepare writable /tmp storage for Vercel serverless environment
$tmpStorage = '/tmp/storage';
$directories = [
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/bootstrap/cache',
    $tmpStorage . '/logs',
    $tmpStorage . '/bar-assistant',
    $tmpStorage . '/bar-assistant/exports',
    $tmpStorage . '/bar-assistant/uploads',
    $tmpStorage . '/bar-assistant/temp',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Force writable /tmp storage path for Vercel
$app->useStoragePath($tmpStorage);

try {
    $kernel = $app->make(Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    )->send();

    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
}
