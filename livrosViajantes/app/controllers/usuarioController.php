<?php
// public/api/router.php
// Este é o único arquivo que gerencia as rotas da API

// 1. Carrega tudo necessário (bootstrap traz .env, conexão, autoload, etc.)
require_once __DIR__ . '/../../app/bootstrap.php';

// 2. Usa namespaces corretos
use App\Controllers\UsuarioController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 3. Função auxiliar para responder JSON (reutilizável)
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 4. Pega o caminho da URL depois de /api/
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method      = $_SERVER['REQUEST_METHOD'];

// Remove o prefixo /api/ e limpa barras extras
$path = trim(str_replace('/api', '', $request_uri), '/');
$path_parts = explode('/', $path);

// 5. Rotas públicas (não precisam de token)
if ($path === 'usuario' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    (new UsuarioController())->registrar($data);
    exit;
}

if ($path === 'login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    (new UsuarioController())->login($data);
    exit;
}

// 6. Middleware simples de autenticação JWT (para rotas protegidas)
$headers = getallheaders();
$auth_header = $headers['Authorization'] ?? '';

$token = null;
if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
    $token = $matches[1];
}

$user_id = null;
if ($token) {
    try {
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        $user_id = $decoded->sub ?? null;
    } catch (Exception $e) {
        json_response(['erro' => 'Token inválido ou expirado'], 401);
        exit;
    }
}

// Se chegou aqui e a rota precisa de login → erro
$rotas_protegidas = ['livros']; // adicione mais rotas aqui conforme criar
if (in_array($path_parts[0] ?? '', $rotas_protegidas) && !$user_id) {
    json_response(['erro' => 'Autenticação necessária'], 401);
    exit;
}

// 7. Rotas protegidas (exemplo – você vai adicionar mais)
if ($path === 'livros' && $method === 'GET') {
    // Aqui virá o LivroController->listar()
    json_response([
        'mensagem' => 'Listagem de livros (ainda em desenvolvimento)',
        'user_logado' => $user_id
    ]);
    exit;
}

// 8. Rota não encontrada
json_response(['erro' => 'Rota não encontrada'], 404);