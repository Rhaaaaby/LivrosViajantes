<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\UsuarioController;
use App\Controllers\LivroController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ================== HELPERS ==================
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_json_input() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// ================== AUTH ==================
function auth(): int {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
        json_response(['erro' => 'Token obrigatório'], 401);
    }

    try {
        $decoded = JWT::decode($matches[1], new Key($_ENV['JWT_SECRET'], 'HS256'));
        return (int) $decoded->sub;
    } catch (\Exception $e) {
        json_response(['erro' => 'Token inválido'], 401);
    }
}

// ================== REQUEST ==================
$method = $_SERVER['REQUEST_METHOD'];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/api/', $uri, 2)[1] ?? '';
$uri = strtolower(trim($uri, '/'));

// ================== CONTROLLERS ==================
$usuarioCtrl = new UsuarioController();
$livroCtrl   = new LivroController();

// ================== ROTAS ==================

// -------- PÚBLICAS --------
if ($uri === 'cadastrar' && $method === 'POST') {
    $usuarioCtrl->registrar(get_json_input());
}

if ($uri === 'login' && $method === 'POST') {
    $usuarioCtrl->login(get_json_input());
}

// -------- USUÁRIO --------
if ($uri === 'perfil' && $method === 'GET') {
    $user_id = auth();
    $usuarioCtrl->perfil($user_id);
}

if ($uri === 'atualizar' && $method === 'PUT') {
    $user_id = auth();
    $usuarioCtrl->atualizar($user_id, get_json_input());
}

if ($uri === 'deletar' && $method === 'DELETE') {
    $user_id = auth();
    $usuarioCtrl->deletar($user_id);
}

// -------- LIVROS --------

// listar todos
if ($uri === 'listar' && $method === 'GET') {
    $livroCtrl->listar();
}

// listar meus livros
if ($uri === 'meus-livros' && $method === 'GET') {
    $user_id = auth();
    $livroCtrl->meusLivros($user_id);
}

// criar livro (JSON ou form-data)
if ($uri === 'publicar' && $method === 'POST') {
    $user_id = auth();

    $data = $_POST;

    if (empty($data)) {
        $data = get_json_input();
    }

    $livroCtrl->criar($data, $user_id);
}

// rotas com ID
if (preg_match('#^livros/(\d+)$#', $uri, $matches)) {
    $livro_id = (int)$matches[1];

    if ($method === 'GET') {
        $livro = $livroCtrl->buscarUm($livro_id);
        json_response(['livro' => $livro]);
    }

    if ($method === 'PUT') {
        $user_id = auth();
        $livroCtrl->atualizar($livro_id, get_json_input(), $user_id);
    }

    if ($method === 'DELETE') {
        $user_id = auth();
        $livroCtrl->deletar($livro_id, $user_id);
    }
}

// -------- 404 --------
json_response(['erro' => 'Rota não encontrada'], 404);