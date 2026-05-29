<?php

namespace App\Models;

use PDO;
use App\Database\Connection;

class Avaliacao
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::connect();
    }

    public function avaliar(int $usuario_id, int $estrelas, string $comentario): bool
    {
        $sql = "INSERT INTO avaliacoes_site (usuario_id, estrelas, comentario) VALUES (:usuario_id, :estrelas, :comentario)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':estrelas' => $estrelas,
            ':comentario' => trim($comentario)
        ]);
    }
}
