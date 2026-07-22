<?php

// Crear carpetas de almacenamiento temporal en /tmp (único directorio con permisos de escritura en Vercel)
$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Cargar la aplicación Laravel desde public/index.php
require __DIR__ . '/../public/index.php';
