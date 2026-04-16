<?php
// public/api/router.php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\UsuarioController;
use App\Controllers\LivroController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Funções auxiliares
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_json_input() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// Middleware de autenticação
function auth(): int {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
        json_response(['erro' => 'Token de autenticação obrigatório'], 401);
    }

    try {
        $decoded = JWT::decode($matches[1], new Key($_ENV['JWT_SECRET'], 'HS256'));
        return (int) $decoded->sub;
    } catch (\Exception $e) {
        json_response(['erro' => 'Token inválido ou expirado'], 401);
    }
}

// ====================== ROTAS ======================
$uri    = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];
$uri    = preg_replace('#^api/#', '', $uri);

$usuarioCtrl = new UsuarioController();
$livroCtrl   = new LivroController();

// Rotas públicas
if ($uri === 'usuario' && $method === 'POST') {
    $data = get_json_input();
    $usuarioCtrl->registrar($data);
}

if ($uri === 'login' && $method === 'POST') {
    $data = get_json_input();
    $usuarioCtrl->login($data);
}

// Rotas protegidas
if (strpos($uri, 'perfil') === 0 || strpos($uri, 'livros') === 0) {
    $user_id = auth();
}

// Rotas do Usuário protegidas
if ($uri === 'perfil' && $method === 'GET') {
    $usuarioCtrl->perfil($user_id);
}

if ($uri === 'perfil' && $method === 'PUT') {
    $data = get_json_input();
    $usuarioCtrl->atualizar($user_id, $data);
}

if ($uri === 'perfil' && $method === 'DELETE') {
    $usuarioCtrl->deletar($user_id);
}

// Rotas do Livro
if ($uri === 'livros' && $method === 'GET') {
    $livroCtrl->listar();
}

if ($uri === 'livros' && $method === 'POST') {
    $data = get_json_input();
    $livroCtrl->criar($data, $user_id);
}

// 404
json_response(['erro' => 'Rota não encontrada'], 404);