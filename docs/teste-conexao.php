<?php
require_once __DIR__ . '/../app/bootstrap.php';

try {
    $pdo = Connection::connect();
    echo "Conexão OK! PostgreSQL versão: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}