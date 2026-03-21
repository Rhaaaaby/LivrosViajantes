<?php
// public/api/router.php

require_once __DIR__ . '/../../app/bootstrap.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Função auxiliar para responder JSON
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Função auxiliar para pegar JSON do body (POST/PUT)
function get_json_input() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

// Pegar o path relativo à /api/
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim(str_replace('/api', '', $uri), '/'));

// Método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// =============================================
// Rotas públicas (sem autenticação)
// =============================================
if ($uri[0] === 'usuario' && $method === 'POST' && count($uri) === 1) {
    // POST /api/usuario  → registrar usuário
    $data = get_json_input();
    // Aqui você chama um controller ou função para registrar
    // Exemplo: require_once __DIR__ . '/../../app/controllers/UsuarioController.php';
    // $ctrl = new UsuarioController();
    // $ctrl->registrar($data);
    json_response(['message' => 'Registro em desenvolvimento'], 201);
    exit;
}

if ($uri[0] === 'login' && $method === 'POST' && count($uri) === 1) {
    // POST /api/login
    $data = get_json_input();
    // Valide email/senha → gere JWT
    $payload = [
        'iss' => 'seusite.com',
        'sub' => 123,           // id do usuário
        'iat' => time(),
        'exp' => time() + 3600  // 1 hora
    ];
    $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    json_response(['token' => $jwt]);
    exit;
}

// =============================================
// Middleware de autenticação JWT (para rotas protegidas)
// =============================================
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $jwt = $matches[1];
    try {
        $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
        // Token válido → $decoded->sub tem o user_id
        define('USER_ID', $decoded->sub);
    } catch (Exception $e) {
        json_response(['error' => 'Token inválido ou expirado'], 401);
        exit;
    }
} else {
    json_response(['error' => 'Token não fornecido'], 401);
    exit;
}

// =============================================
// Rotas PROTEGIDAS (só com JWT válido)
// =============================================

if ($uri[0] === 'livros' && $method === 'GET' && count($uri) === 1) {
    // GET /api/livros  → listar livros (exemplo)
    json_response([
        'livros' => [
            ['id' => 1, 'titulo' => 'Exemplo', 'dono' => USER_ID]
        ]
    ]);
    exit;
}

if ($uri[0] === 'livros' && $method === 'POST' && count($uri) === 1) {
    // POST /api/livros  → criar publicação
    $data = get_json_input();
    json_response(['message' => 'Livro criado (em dev)', 'data' => $data], 201);
    exit;
}

// 404 para API
json_response(['error' => 'Rota não encontrada'], 404);