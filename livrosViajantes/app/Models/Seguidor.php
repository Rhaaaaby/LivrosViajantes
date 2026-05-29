<?php

namespace App\Models;

use PDO;
use App\Database\Connection;

class Seguidor
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::connect();
    }

    public function seguir(int $seguidor_id, int $seguido_id): bool
    {
        if ($seguidor_id === $seguido_id) {
            return false;
        }

        try {
            $sql = "INSERT INTO seguidores (seguidor_id, seguido_id) VALUES (:seguidor, :seguido)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':seguidor' => $seguidor_id,
                ':seguido' => $seguido_id
            ]);
        } catch (\PDOException $e) {
            return false; // already following
        }
    }

    public function deixarDeSeguir(int $seguidor_id, int $seguido_id): bool
    {
        $sql = "DELETE FROM seguidores WHERE seguidor_id = :seguidor AND seguido_id = :seguido";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':seguidor' => $seguidor_id,
            ':seguido' => $seguido_id
        ]);
    }

    public function listarSeguindo(int $usuario_id): array
    {
        $sql = "SELECT u.id, u.nome_usuario, u.foto 
                FROM seguidores s
                JOIN usuario u ON s.seguido_id = u.id
                WHERE s.seguidor_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    public function verificaSeSegue(int $seguidor_id, int $seguido_id): bool
    {
        $sql = "SELECT id FROM seguidores WHERE seguidor_id = :seguidor AND seguido_id = :seguido";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':seguidor' => $seguidor_id,
            ':seguido' => $seguido_id
        ]);
        return $stmt->fetch() !== false;
    }
}
