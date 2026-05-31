<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Controllers\UsuarioController;
use App\Controllers\LivroController;
use App\Controllers\SolicitacaoController;
use App\Controllers\MensagemController;
use App\Controllers\SeguidorController;
use App\Controllers\AvaliacaoController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ================== CABEÇALHOS CORS (ADICIONE ESTE BLOCO) ==================
header("Access-Control-Allow-Origin: *"); // Permite requisições de qualquer origem
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Se for uma requisição de pré-vôo (OPTIONS), responde com 200 e encerra
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// ===========================================================================

// ================== HELPERS ==================
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/* function get_json_input() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}
*/

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
$solicitacaoCtrl = new SolicitacaoController();
$mensagemCtrl = new MensagemController();
$seguidorCtrl = new SeguidorController();
$avaliacaoCtrl = new AvaliacaoController();

// ================== ROTAS ==================

// 1. Atualize a função para limpar o JSON de qualquer caractere invisível
function get_json_input() {
    $input = file_get_contents('php://input');
    
    // Remove caracteres de controle invisíveis que quebram o json_decode no Linux
    $input = trim($input);
    
    if (empty($input)) {
        return [];
    }
    
    $decoded = json_decode($input, true);
    
    // Se falhar na decodificação padrão, tenta limpar caracteres UTF-8 inválidos
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }
    
    return $decoded;
}

// ================== ROTAS ==================

// -------- PÚBLICAS --------
if ($uri === 'cadastrar' && $method === 'POST') {
    $dados = get_json_input();
    
    // Fallback: Se o JSON falhar completamente, tenta ler do $_POST tradicional
    if (empty($dados)) {
        $dados = $_POST;
    }
    
    $usuarioCtrl->registrar($dados);
    exit; // Garante que encerra aqui
}

/*if ($uri === 'cadastrar' && $method === 'POST') {
    $usuarioCtrl->registrar(get_json_input());
}
*/

if ($uri === 'login' && $method === 'POST') {
    $usuarioCtrl->login(get_json_input());
}

// -------- USUÁRIO --------
if ($uri === 'perfil' && $method === 'GET') {
    $user_id = auth();
    $usuarioCtrl->perfil($user_id);
}

if ($uri === 'atualizar' && ($method === 'PUT' || $method === 'POST')) {
    $user_id = auth();

    $data = $_POST;
    if (empty($data)) {
        $data = get_json_input();
    }

    $usuarioCtrl->atualizar($user_id, $data);
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

if (preg_match('#^atualizar-livro/(\d+)$#', $uri, $matches) && $method === 'POST') {
    $livro_id = (int)$matches[1];
    $user_id = auth();
    $data = $_POST;
    if (empty($data)) {
        $data = get_json_input();
    }
    $livroCtrl->atualizar($livro_id, $data, $user_id); // LivroController->atualizar should handle $_FILES
}

// -------- SOLICITAÇÕES --------

// criar solicitação de interesse
if ($uri === 'solicitacoes' && $method === 'POST') {
    $user_id = auth();
    $result = $solicitacaoCtrl->criar(get_json_input(), $user_id);
    json_response($result);
}

// listar todas as solicitações do usuário
if ($uri === 'solicitacoes' && $method === 'GET') {
    $user_id = auth();
    $result = $solicitacaoCtrl->listarTodas($user_id);
    json_response($result);
}

// listar solicitações recebidas
if ($uri === 'solicitacoes/recebidas' && $method === 'GET') {
    $user_id = auth();
    $result = $solicitacaoCtrl->listarRecebidas($user_id);
    json_response($result);
}

// listar solicitações enviadas
if ($uri === 'solicitacoes/enviadas' && $method === 'GET') {
    $user_id = auth();
    $result = $solicitacaoCtrl->listarEnviadas($user_id);
    json_response($result);
}

// responder solicitação
if (preg_match('#^solicitacoes/(\d+)/responder$#', $uri, $matches)) {
    $solicitacao_id = (int)$matches[1];
    $user_id = auth();

    if ($method === 'PUT') {
        $acao = $_GET['acao'] ?? '';
        $result = $solicitacaoCtrl->responder($solicitacao_id, $acao, $user_id);
        json_response($result);
    } else {
        json_response(['sucesso' => false, 'erro' => 'Método não permitido. Use PUT'], 405);
    }
}

// cancelar solicitação (apenas pelo solicitante)
if (preg_match('#^solicitacoes/(\d+)/cancelar$#', $uri, $matches)) {
    $solicitacao_id = (int)$matches[1];
    $user_id = auth();

    if ($method === 'DELETE') {
        $result = $solicitacaoCtrl->cancelar($solicitacao_id, $user_id);
        json_response($result);
    } else {
        json_response(['sucesso' => false, 'erro' => 'Método não permitido. Use DELETE'], 405);
    }
}

// -------- MENSAGENS --------
if ($uri === 'mensagens/notificacoes' && $method === 'GET') {
    $user_id = auth();
    $result = $mensagemCtrl->listarNotificacoes($user_id);
    json_response($result);
}

if ($uri === 'conversas' && $method === 'GET') {
    $user_id = auth();
    $result = $mensagemCtrl->listarConversas($user_id);
    json_response($result);
}

if (preg_match('#^conversas/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $user_id = auth();
    $partner_id = (int)$matches[1];
    $result = $mensagemCtrl->listarMensagens($user_id, $partner_id);
    json_response($result);
}

if (preg_match('#^conversas/(\d+)/mensagens$#', $uri, $matches)) {
    $partner_id = (int)$matches[1];
    $user_id = auth();

    if ($method === 'POST') {
        $dados = get_json_input();
        $conteudo = $dados['conteudo'] ?? '';
        $result = $mensagemCtrl->enviarMensagem($user_id, $partner_id, $conteudo);
        json_response($result);
    }

    json_response(['sucesso' => false, 'erro' => 'Método não permitido. Use POST'], 405);
}

// buscar dados de usuário por id para conversas
if (preg_match('#^usuarios/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $user_id = auth();
    $target_id = (int)$matches[1];
    $usuarioCtrl->buscarPorId($target_id);
}

// -------- SEGUIDORES --------
if ($uri === 'seguidores' && $method === 'GET') {
    $user_id = auth();
    $result = $seguidorCtrl->listarSeguindo($user_id);
    json_response($result);
}

if (preg_match('#^seguidores/(\d+)$#', $uri, $matches)) {
    $user_id = auth();
    $seguido_id = (int)$matches[1];

    if ($method === 'POST') {
        $result = $seguidorCtrl->seguir($seguido_id, $user_id);
        json_response($result);
    }
    
    if ($method === 'DELETE') {
        $result = $seguidorCtrl->deixarDeSeguir($seguido_id, $user_id);
        json_response($result);
    }
}

// -------- PERFIL PÚBLICO --------
if (preg_match('#^perfil-publico/(\d+)$#', $uri, $matches) && $method === 'GET') {
    // Pode ser acessado por logados ou não logados, então tentamos auth opcional
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';
    $user_id = null;
    
    if (preg_match('/Bearer\s(\S+)/', $auth, $auth_matches)) {
        try {
            $decoded = JWT::decode($auth_matches[1], new Key($_ENV['JWT_SECRET'], 'HS256'));
            $user_id = (int) $decoded->sub;
        } catch (\Exception $e) {}
    }
    
    // CORREÇÃO: Pegamos o ID do perfil da URL e chamamos o controller para preencher o $result
    $perfil_id = (int)$matches[1];
    $result = $seguidorCtrl->perfilPublico($perfil_id, $user_id);
    
    json_response($result);
}

// -------- AVALIAÇÃO DO SITE --------
if ($uri === 'avaliacoes' && $method === 'POST') {
    $user_id = auth();
    $data = $_POST;
    if (empty($data)) {
        $data = get_json_input();
    }
    $result = $avaliacaoCtrl->criar($data, $user_id);
    json_response($result);
}

// -------- 404 --------
json_response(['erro' => 'Rota não encontrada'], 404);