<?php
require_once '../../app/database/Connection.php';

header('Content-Type: application/json');

$conn = Connection::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = json_decode(file_get_contents("php://input"), true);

    // Validação mínima
    if (!isset($dados['titulo'], $dados['categoria_id'], $dados['autor_id'])) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos']);
        exit;
    }

    $sql = "INSERT INTO livro (titulo, descricao, imagem, autor_id, categoria_id, status)
            VALUES (:titulo, :descricao, :imagem, :autor_id, :categoria_id, :status)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':titulo' => $dados['titulo'],
        ':descricao' => $dados['descricao'] ?? '',
        ':imagem' => $dados['imagem'] ?? null,
        ':autor_id' => $dados['autor_id'],
        ':categoria_id' => $dados['categoria_id'],
        ':status' => $dados['status'] ?? true
    ]);

    echo json_encode(["status" => "sucesso"]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM livro ORDER BY criado_em DESC";
    $stmt = $conn->query($sql);
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($livros);
}