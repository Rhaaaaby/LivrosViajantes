<?php
// public/api/router.php

require_once __DIR__ . '/../../app/bootstrap.php';

use app\controllers\usuarioController;
use app\controllers\livroController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// ==================== FUNÇÕES AUXILIARES ====================
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_json_input() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// ROTAS PÚBLICAS 

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Remove prefixo "api/"
$uri = preg_replace('#^api/#', '', $uri);

if ($uri === 'usuario' && $method === 'POST') {
    $data = get_json_input();
    (new UsuarioController())->registrar($data);
}

if ($uri === 'login' && $method === 'POST') {
    $data = get_json_input();
    (new UsuarioController())->login($data);
}

// ==================== MIDDLEWARE DE AUTENTICAÇÃO ====================
function verificarAutenticacao() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
        json_response(['erro' => 'Token de autenticação necessário'], 401);
    }

    try {
        $token = $matches[1];
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        return $decoded->sub; // retorna o user_id
    } catch (\Exception $e) {
        json_response(['erro' => 'Token inválido ou expirado'], 401);
    }
}

// ==================== ROTAS PROTEGIDAS ====================
$user_id = null;

if (strpos($uri, 'livros') === 0 && $method === 'POST') {
    $user_id = verificarAutenticacao();   // só deixa passar se estiver logado
}

// Rotas de Livro
if ($uri === 'livros' && $method === 'GET') {
    (new LivroController())->listar();
}

if ($uri === 'livros' && $method === 'POST') {
    $data = get_json_input();
    (new LivroController())->criar($data, $user_id);
}

// Rota não encontrada
json_response(['erro' => 'Rota não encontrada'], 404);