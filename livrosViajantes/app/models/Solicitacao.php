<?php

namespace App\Models;

use PDO;
use App\Database\Connection;

class Solicitacao
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::connect();
    }

    // Criar nova solicitação de interesse
    public function criar(array $data, int $solicitante_id): bool
    {
        // Verificar se já existe uma solicitação ativa para este livro por este usuário
        if ($this->verificarSolicitacaoExistente($solicitante_id, $data['livro_id'])) {
            return false; // Já existe solicitação
        }

        // Buscar o dono do livro
        $dono_id = $this->buscarDonoDoLivro($data['livro_id']);
        if (!$dono_id) {
            return false; // Livro não encontrado
        }

        $sql = "INSERT INTO solicitacao (solicitante_id, livro_id, dono_id, tipo, status, criada_em)
                VALUES (:solicitante_id, :livro_id, :dono_id, :tipo, 'pendente', CURRENT_TIMESTAMP)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':solicitante_id' => $solicitante_id,
            ':livro_id' => $data['livro_id'],
            ':dono_id' => $dono_id,
            ':tipo' => $data['tipo']
        ]);
    }

    // Buscar dono do livro
    private function buscarDonoDoLivro(int $livro_id): ?int
    {
        $stmt = $this->pdo->prepare("SELECT autor_id FROM livro WHERE id = :id");
        $stmt->execute([':id' => $livro_id]);
        $result = $stmt->fetch();
        return $result ? (int)$result['autor_id'] : null;
    }

    // Verificar se já existe solicitação ativa
    private function verificarSolicitacaoExistente(int $solicitante_id, int $livro_id): bool
    {
        $sql = "SELECT id FROM solicitacao
                WHERE solicitante_id = :solicitante_id
                AND livro_id = :livro_id
                AND status = 'pendente'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':solicitante_id' => $solicitante_id,
            ':livro_id' => $livro_id
        ]);

        return $stmt->fetch() !== false;
    }

    // Listar solicitações recebidas (para o dono do livro)
    public function listarRecebidas(int $dono_id)
    {
        $sql = "SELECT s.id, s.tipo, s.status, s.criada_em,
                       l.titulo as livro_titulo, l.imagem as livro_imagem,
                       u.nome_usuario as solicitante_nome
                FROM solicitacao s
                JOIN livro l ON s.livro_id = l.id
                JOIN usuario u ON s.solicitante_id = u.id
                WHERE l.autor_id = :dono_id
                AND s.status = 'pendente'
                ORDER BY s.criada_em DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':dono_id' => $dono_id]);
        return $stmt->fetchAll();
    }

    // Listar solicitações enviadas (pelo usuário)
    public function listarEnviadas(int $solicitante_id)
    {
        $sql = "SELECT s.id, s.tipo, s.status, s.criada_em,
                       l.titulo as livro_titulo, l.imagem as livro_imagem,
                       u.nome_usuario as dono_nome
                FROM solicitacao s
                JOIN livro l ON s.livro_id = l.id
                JOIN usuario u ON l.autor_id = u.id
                WHERE s.solicitante_id = :solicitante_id
                ORDER BY s.criada_em DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':solicitante_id' => $solicitante_id]);
        return $stmt->fetchAll();
    }

    // Atualizar status da solicitação (aceitar/recusar)
    public function atualizarStatus(int $solicitacao_id, string $status, int $dono_id): bool
    {
        // Verificar se o usuário é o dono do livro
        $sql_verificar = "SELECT s.id FROM solicitacao s
                         JOIN livro l ON s.livro_id = l.id
                         WHERE s.id = :solicitacao_id AND l.autor_id = :dono_id";

        $stmt_verificar = $this->pdo->prepare($sql_verificar);
        $stmt_verificar->execute([
            ':solicitacao_id' => $solicitacao_id,
            ':dono_id' => $dono_id
        ]);

        if (!$stmt_verificar->fetch()) {
            return false; // Não é o dono
        }

        $sql = "UPDATE solicitacao SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $solicitacao_id
        ]);
    }

    // Cancelar solicitação (apenas pelo solicitante)
    public function cancelar(int $solicitacao_id, int $solicitante_id): bool
    {
        // Verificar se o usuário é o solicitante e a solicitação está pendente
        $sql_verificar = "SELECT id FROM solicitacao
                         WHERE id = :solicitacao_id
                         AND solicitante_id = :solicitante_id
                         AND status = 'pendente'";

        $stmt_verificar = $this->pdo->prepare($sql_verificar);
        $stmt_verificar->execute([
            ':solicitacao_id' => $solicitacao_id,
            ':solicitante_id' => $solicitante_id
        ]);

        if (!$stmt_verificar->fetch()) {
            return false; // Não é o solicitante ou não está pendente
        }

        $sql = "DELETE FROM solicitacao WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $solicitacao_id]);
    }
}