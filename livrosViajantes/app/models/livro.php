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

    /**
     * Lista todos os livros disponíveis
     */
    public function listarTodos()
    {
        $sql = "SELECT l.id, l.titulo, l.descricao, l.imagem, l.status, 
                       l.criado_em, u.nome_usuario as autor_nome, c.nome as categoria_nome
                FROM livro l
                JOIN usuario u ON l.autor_id = u.id
                LEFT JOIN categoria c ON l.categoria_id = c.id
                WHERE l.status = true
                ORDER BY l.criado_em DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cria um novo livro
     */
    public function criar(array $dados, int $autor_id): bool
    {
        $sql = "INSERT INTO livro (titulo, descricao, imagem, autor_id, categoria_id, status, criado_em)
                VALUES (:titulo, :descricao, :imagem, :autor_id, :categoria_id, true, CURRENT_TIMESTAMP)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':titulo'       => trim($dados['titulo']),
            ':descricao'    => trim($dados['descricao'] ?? ''),
            ':imagem'       => $dados['imagem'] ?? null,
            ':autor_id'     => $autor_id,
            ':categoria_id' => (int) $dados['categoria_id']
        ]);
    }

    /**
     * Busca um livro por ID (útil depois)
     */
    public function buscarPorId(int $id)
    {
        $sql = "SELECT * FROM livro WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}