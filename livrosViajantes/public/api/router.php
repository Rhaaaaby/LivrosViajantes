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
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// remove /livrosViajantes/public do caminho
$base = '/livrosViajantes/public';
$uri = str_replace($base, '', $uri);

// remove /api
$uri = preg_replace('#^/api#', '', $uri);

// limpa barras
$uri = trim($uri, '/');

//debug
//json_response(['debug_uri' => $uri]);

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

// -------- PROTEGIDAS --------

$user_id = null;

if (str_starts_with($uri, 'perfil') || str_starts_with($uri, 'livros')) {
    $user_id = auth();
}

// -------- USUÁRIO --------
if ($uri === 'perfil' && $method === 'GET') {
    $usuarioCtrl->perfil($user_id);
}

if ($uri === 'atualizar' && $method === 'PUT') {
    $user_id = auth();
    $usuarioCtrl->atualizar($user_id, get_json_input());
}

if ($uri === 'deletar' && $method === 'DELETE') {
    $user_id = auth();
    $usuarioCtrl->deletar($user_id, get_json_input());
}

// -------- LIVROS --------
if ($uri === 'livros' && $method === 'GET') {
    $livroCtrl->listar();
}

if ($uri === 'livros' && $method === 'POST') {
    $livroCtrl->criar(get_json_input(), $user_id);
}

// -------- 404 --------
json_response(['erro' => 'Rota não encontrada'], 404);