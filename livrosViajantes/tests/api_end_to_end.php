<?php

function request(string $method, string $path, array $headers = [], $data = null, array $files = []): array
{
    $url = 'http://localhost:8080' . $path;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, ['Accept: application/json']));

    if ($method === 'POST' || $method === 'PUT') {
        if (!empty($files)) {
            $payload = [];
            foreach ($data as $key => $value) {
                $payload[$key] = $value;
            }
            foreach ($files as $field => $filepath) {
                if (!file_exists($filepath)) {
                    throw new RuntimeException("File not found: $filepath");
                }
                $payload[$field] = new CURLFile(realpath($filepath));
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } else {
            $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, ['Content-Type: application/json']));
        }
    }

    $body = curl_exec($ch);
    $info = curl_getinfo($ch);

    if ($body === false) {
        $error = curl_error($ch);
        throw new RuntimeException("Curl error: $error");
    }

    $data = json_decode($body, true);
    return [
        'status' => $info['http_code'],
        'raw' => $body,
        'json' => $data,
    ];
}

function printResult(string $title, array $result)
{
    echo "=== $title ===\n";
    echo "Status: {$result['status']}\n";
    echo "Body: {$result['raw']}\n\n";
}

$random = random_int(100000, 999999);
$userAEmail = "api_user_a_{$random}@example.com";
$userBEmail = "api_user_b_{$random}@example.com";
$dummyImage = __DIR__ . '/dummy.png';

try {
    echo "Starting API flow test...\n\n";

    // Create user A
    $result = request('POST', '/api/cadastrar', [], [
        'nome_usuario' => 'API User A',
        'email' => $userAEmail,
        'senha' => '12345678'
    ]);
    printResult('Register User A', $result);

    // Create user B
    $result = request('POST', '/api/cadastrar', [], [
        'nome_usuario' => 'API User B',
        'email' => $userBEmail,
        'senha' => '12345678'
    ]);
    printResult('Register User B', $result);

    // Login user A
    $result = request('POST', '/api/login', [], [
        'email' => $userAEmail,
        'senha' => '12345678'
    ]);
    printResult('Login User A', $result);
    $tokenA = $result['json']['token'] ?? null;
    if (!$tokenA) {
        throw new RuntimeException('Failed to obtain token for User A');
    }

    // Login user B
    $result = request('POST', '/api/login', [], [
        'email' => $userBEmail,
        'senha' => '12345678'
    ]);
    printResult('Login User B', $result);
    $tokenB = $result['json']['token'] ?? null;
    $userB = $result['json']['usuario'] ?? null;
    if (!$tokenB) {
        throw new RuntimeException('Failed to obtain token for User B');
    }

    // User A get perfil
    $result = request('GET', '/api/perfil', ["Authorization: Bearer $tokenA"]);
    printResult('User A Perfil', $result);

    // User A update profile with photo upload via POST
    $result = request('POST', '/api/atualizar', ["Authorization: Bearer $tokenA"], [
        'nome_usuario' => 'API User A Updated'
    ], [
        'foto' => $dummyImage
    ]);
    printResult('Update User A Profile Photo', $result);

    // User A publish a book with image upload
    $result = request('POST', '/api/publicar', ["Authorization: Bearer $tokenA"], [
        'titulo' => 'Livro de Teste A',
        'descricao' => 'Descrição de teste para publicação com imagem',
        'categoria_id' => '1'
    ], [
        'imagem' => $dummyImage
    ]);
    printResult('User A Publish Book', $result);

    // User A list own books
    $result = request('GET', '/api/meus-livros', ["Authorization: Bearer $tokenA"]);
    printResult('User A Meus Livros', $result);

    // Find a book owned by user B by creating one
    $result = request('POST', '/api/publicar', ["Authorization: Bearer $tokenB"], [
        'titulo' => 'Livro de Teste B',
        'descricao' => 'Livro de teste do usuário B',
        'categoria_id' => '1'
    ], [
        'imagem' => $dummyImage
    ]);
    printResult('User B Publish Book', $result);

    $result = request('GET', '/api/meus-livros', ["Authorization: Bearer $tokenB"]);
    printResult('User B Meus Livros', $result);

    $livroBId = null;
    foreach ($result['json']['meus_livros'] ?? [] as $livro) {
        if ($livro['titulo'] === 'Livro de Teste B') {
            $livroBId = $livro['id'];
            break;
        }
    }
    if (!$livroBId) {
        throw new RuntimeException('Could not find Livro B id');
    }

    // User A create a solicitation for Livro B
    $result = request('POST', '/api/solicitacoes', ["Authorization: Bearer $tokenA"], [
        'livro_id' => $livroBId,
        'tipo' => 'interesse'
    ]);
    printResult('User A Create Solicitation', $result);

    // User B list received solicitations
    $result = request('GET', '/api/solicitacoes/recebidas', ["Authorization: Bearer $tokenB"]);
    printResult('User B Received Solicitations', $result);

    $solicitacaoId = $result['json']['dados'][0]['id'] ?? null;
    if (!$solicitacaoId) {
        throw new RuntimeException('Could not find solicitation id');
    }

    // User B respond to solicitation (accept)
    $result = request('PUT', "/api/solicitacoes/$solicitacaoId/responder?acao=aceitar", ["Authorization: Bearer $tokenB"]);
    printResult('User B Respond Solicitation Accept', $result);

    // User A list sent solicitations
    $result = request('GET', '/api/solicitacoes/enviadas', ["Authorization: Bearer $tokenA"]);
    printResult('User A Sent Solicitations', $result);

    // List all solicitations for User A
    $result = request('GET', '/api/solicitacoes', ["Authorization: Bearer $tokenA"]);
    printResult('User A All Solicitations', $result);

    // Verify user lookup route
    $partnerId = $userB['id'] ?? null;
    if (!$partnerId) {
        throw new RuntimeException('Could not determine partner id for chat');
    }
    $result = request('GET', "/api/usuarios/$partnerId", ["Authorization: Bearer $tokenA"]);
    printResult('User A Get User B Profile', $result);

    // Create a second solicitation and then cancel it
    $result = request('POST', '/api/solicitacoes', ["Authorization: Bearer $tokenA"], [
        'livro_id' => $livroBId,
        'tipo' => 'interesse'
    ]);
    printResult('User A Create Second Solicitation', $result);

    $result = request('GET', '/api/solicitacoes/enviadas', ["Authorization: Bearer $tokenA"]);
    printResult('User A Sent Solicitations After Second Request', $result);

    $cancelId = null;
    foreach ($result['json']['dados'] ?? [] as $solic) {
        if ($solic['status'] === 'pendente') {
            $cancelId = $solic['id'];
            break;
        }
    }

    if ($cancelId) {
        $result = request('DELETE', "/api/solicitacoes/$cancelId/cancelar", ["Authorization: Bearer $tokenA"]);
        printResult('User A Cancel Solicitation', $result);
    }

    $result = request('POST', "/api/conversas/$partnerId/mensagens", ["Authorization: Bearer $tokenA"], [
        'conteudo' => 'Olá, esta é uma mensagem de teste'
    ]);
    printResult('User A Send Message to User B', $result);

    $result = request('GET', '/api/conversas', ["Authorization: Bearer $tokenA"]);
    printResult('User A Conversas', $result);

    $result = request('GET', "/api/conversas/$partnerId", ["Authorization: Bearer $tokenA"]);
    printResult('User A Messages With Partner', $result);

    echo "API flow test completed.\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
