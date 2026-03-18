<?php
require_once '../../app/database/Connection.php';

header('Content-Type: application/json');

$conn = Connection::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!isset($dados['nome'], $dados['email'], $dados['senha'])) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos']);
        exit;
    }

    $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (nome, email, senha)
            VALUES (:nome, :email, :senha)";

    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':email' => $dados['email'],
            ':senha' => $senhaHash
        ]);

        echo json_encode(["status" => "sucesso"]);
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Email já cadastrado ou erro no banco"
        ]);
    }

    exit;
}

