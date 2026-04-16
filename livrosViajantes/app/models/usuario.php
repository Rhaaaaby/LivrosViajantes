<?php

namespace App\Models;

use PDO;
use App\Database\Connection;

class Usuario
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::connect();
    }

    // CREATE
    public function criar(array $data): bool
    {
        $senhaHash = password_hash($data['senha'], PASSWORD_ARGON2ID);

        $sql = "INSERT INTO usuario (nome_usuario, email, senha_hash, criado_em)
                VALUES (:nome_usuario, :email, :senha_hash, CURRENT_TIMESTAMP)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome_usuario' => trim($data['nome_usuario']),
            ':email'        => trim($data['email']),
            ':senha_hash'   => $senhaHash
        ]);
    }

    // READ - Buscar por email (usado no login)
    public function buscarPorEmail(string $email)
    {
        $stmt = $this->pdo->prepare("SELECT id, nome_usuario, email, senha_hash FROM usuario WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // READ - Buscar por ID (perfil)
    public function buscarPorId(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT id, nome_usuario, email, foto, criado_em FROM usuario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // UPDATE
    public function atualizar(int $id, array $data): bool
    {
        $sql = "UPDATE usuario SET nome_usuario = :nome_usuario, foto = :foto WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome_usuario' => trim($data['nome_usuario'] ?? ''),
            ':foto'         => $data['foto'] ?? null,
            ':id'           => $id
        ]);
    }

    // DELETE (soft delete - apenas marca como inativo)
    public function deletar(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE usuario SET ativo = false WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}