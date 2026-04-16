<?php
require_once '../../app/database/Connection.php';

header('Content-Type: application/json');

$conn = Connection::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!isset($dados['nome_usuario'], $dados['email'], $dados['senha_hash'])) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos']);
        exit;
    }

    $senhaHash = password_hash($dados['senha_hash'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (nome_usuario, email, senha_hash)
            VALUES (:nome_usuario, :email, :senha_hash)";

    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([
            ':nome_usuario' => $dados['nome_usuario'],
            ':email' => $dados['email'],
            ':senha_hash' => $senhaHash
        ]);

        echo json_encode(["status" => "sucesso"]);
    } catch (PDOException $e) {
    echo $e->getMessage();
    }
    
    ///catch (PDOException $e) {
        ///echo json_encode([
        ///    "status" => "erro",
        ///    "mensagem" => "Email já cadastrado ou erro no banco"
        ///]);
    ///}

    exit;
}

