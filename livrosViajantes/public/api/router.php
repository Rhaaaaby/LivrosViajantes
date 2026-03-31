<?php
require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\UsuarioController;
use App\Router;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Cria o router
$router = new Router();

// Rotas públicas
$router->add('POST', 'usuario', function () {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    (new UsuarioController())->registrar($data);
});

$router->add('POST', 'login', function () {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    (new UsuarioController())->login($data);
});

// Middleware JWT (aplica só nas rotas abaixo)
$router->add('GET', 'test-protected', function () {
    // Middleware simples aqui (pode virar classe Middleware depois)
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';
    if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Token requerido']);
        exit;
    }

    try {
        $jwt = $matches[1];
        $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
        echo json_encode(['message' => 'Acesso autorizado', 'user_id' => $decoded->sub]);
    } catch (\Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido']);
    }
});

// Executa
$router->dispatch();