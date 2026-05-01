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

    /**
     * UPDATE - Atualizar livro
     */
    public function atualizar(int $id, array $data, int $user_id): bool
    {
        // Verifica se o livro pertence ao usuário antes de atualizar
        $livro = $this->buscarPorId($id);
        if (!$livro || $livro['autor_id'] != $user_id) {
            return false;
        }

        $sql = "UPDATE livro 
                SET titulo = :titulo, 
                    descricao = :descricao, 
                    categoria_id = :categoria_id
                WHERE id = :id AND autor_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':titulo'       => trim($data['titulo']),
            ':descricao'    => trim($data['descricao'] ?? ''),
            ':categoria_id' => (int)($data['categoria_id'] ?? 1),
            ':id'           => $id,
            ':user_id'      => $user_id
        ]);
    }

    /**
     * Atualizar livro com possibilidade de nova imagem
     */
    public function atualizarComImagem(int $id, array $data, int $user_id, ?string $novaImagem = null): bool
    {
        $livro = $this->buscarPorId($id);
        if (!$livro || $livro['autor_id'] != $user_id) {
            return false;
        }

        $sql = "UPDATE livro 
                SET titulo = :titulo, 
                    descricao = :descricao, 
                    categoria_id = :categoria_id";

        $params = [
            ':titulo'       => trim($data['titulo']),
            ':descricao'    => trim($data['descricao'] ?? ''),
            ':categoria_id' => (int)($data['categoria_id'] ?? 1),
            ':id'           => $id,
            ':user_id'      => $user_id
        ];

        if ($novaImagem !== null) {
            $sql .= ", imagem = :imagem";
            $params[':imagem'] = $novaImagem;
        }

        $sql .= " WHERE id = :id AND autor_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * DELETE - Excluir livro (soft delete - apenas desativa)
     */
    public function deletar(int $id, int $user_id): bool
    {
        // Segurança: só permite excluir se for o dono do livro
        $livro = $this->buscarPorId($id);
        if (!$livro || $livro['autor_id'] != $user_id) {
            return false;
        }

        $sql = "UPDATE livro SET status = false WHERE id = :id AND autor_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'      => $id,
            ':user_id' => $user_id
        ]);
    }
}