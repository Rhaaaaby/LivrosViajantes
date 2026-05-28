<?php

require_once __DIR__ . '/../app/bootstrap.php';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Identifica rotas de API
if (strpos($request, '/api/') !== false || strpos($request, '/api') === 0) {
    require_once __DIR__ . '/api/router.php';
    exit;
}

// O PULO DO GATO: Descobre o basePath dinamicamente
// Se o domínio NÃO for localhost, o basePath na Render é apenas vazio ''
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
$basePath = $isLocalhost ? '/livrosViajantes/public' : '';

$relativePath = $request;
if (!empty($basePath) && strpos($request, $basePath) === 0) {
    $relativePath = substr($request, strlen($basePath));
}

$file = __DIR__ . $relativePath;

if ($relativePath !== '/' && file_exists($file) && !is_dir($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml'
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
    }
    readfile($file);
    exit;
}

// Front (SPA ou páginas)
require_once __DIR__ . '/pages/index.php';