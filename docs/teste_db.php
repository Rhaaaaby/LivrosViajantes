<?php

require __DIR__ . '/../app/database/Connection.php';

try {
    $conn = Connection::connect();
    echo "🔥 Conectado ao PostgreSQL com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}