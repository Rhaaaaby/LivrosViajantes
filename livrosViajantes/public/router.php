<?php
/**
 * PHP Built-in Server Router
 * This file is automatically loaded by the PHP built-in server to route requests
 * Place it in the public/ directory and run:
 *   php -S localhost:8080 router.php -t public
 */

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname(__FILE__);

// API routes - forward to api/router.php
if (strpos($requestUri, '/api/') === 0) {
    $_SERVER['REQUEST_URI'] = $requestUri;
    require __DIR__ . '/api/router.php';
    return true;
}

// Static files - serve as-is
$file = __DIR__ . $requestUri;
if (is_file($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml',
        'gif'  => 'image/gif',
        'ico'  => 'image/x-icon'
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
    }
    readfile($file);
    return true;
}

// Front pages
if (is_file(__DIR__ . '/pages/index.php')) {
    $_SERVER['REQUEST_URI'] = $requestUri;
    require __DIR__ . '/pages/index.php';
    return true;
}

return false;
