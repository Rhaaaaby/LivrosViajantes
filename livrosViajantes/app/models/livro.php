<?php

namespace App\Models;

use PDO;
use App\Database\Connection;

class Livro
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::connect();
    }

    public function listarTodos()
    {
        $sql = "SELECT l.id, l.titulo, l.descricao, l.imagem, l.status, l.criado_em,
                       u.nome_usuario as autor, c.nome as categoria
                FROM livro l
                JOIN usuario u ON l.autor_id = u.id
                LEFT JOIN categoria c ON l.categoria_id = c.id
                WHERE l.status = true
                ORDER BY l.criado_em DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function criar(array $data, int $autor_id): bool
    {
        $sql = "INSERT INTO livro 
                (titulo, descricao, imagem, autor_id, categoria_id, status, criado_em)
                VALUES (:titulo, :descricao, :imagem, :autor_id, :categoria_id, true, CURRENT_TIMESTAMP)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titulo'       => trim($data['titulo']),
            ':descricao'    => trim($data['descricao'] ?? ''),
            ':imagem'       => $data['imagem'] ?? null,
            ':autor_id'     => $autor_id,
            ':categoria_id' => (int)$data['categoria_id']
        ]);
    }
}