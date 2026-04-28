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

    // Listar todos os livros disponíveis (público)
    public function listarTodos()
    {
        $sql = "SELECT l.id, l.titulo, l.descricao, l.imagem, l.status, l.criado_em,
                       u.nome_usuario as autor_nome, 
                       c.nome as categoria_nome
                FROM livro l
                JOIN usuario u ON l.autor_id = u.id
                LEFT JOIN categoria c ON l.categoria_id = c.id
                WHERE l.status = true
                ORDER BY l.criado_em DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    // Listar apenas os livros do usuário logado
    public function listarMeusLivros(int $user_id)
    {
        $sql = "SELECT l.id, l.titulo, l.descricao, l.imagem, l.status, l.criado_em,
                       c.nome as categoria_nome
                FROM livro l
                LEFT JOIN categoria c ON l.categoria_id = c.id
                WHERE l.autor_id = :user_id
                ORDER BY l.criado_em DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    // Criar novo livro
    public function criar(array $data, int $autor_id, ?string $imagem = null): bool
    {
        $sql = "INSERT INTO livro 
                (titulo, descricao, imagem, autor_id, categoria_id, status, criado_em)
                VALUES (:titulo, :descricao, :imagem, :autor_id, :categoria_id, true, CURRENT_TIMESTAMP)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':titulo'       => trim($data['titulo']),
            ':descricao'    => trim($data['descricao'] ?? ''),
            ':imagem'       => $imagem,
            ':autor_id'     => $autor_id,
            ':categoria_id' => (int)($data['categoria_id'] ?? 1)
        ]);
    }

    // Buscar um livro por ID
    public function buscarPorId(int $id)
    {
        $sql = "SELECT * FROM livro WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}