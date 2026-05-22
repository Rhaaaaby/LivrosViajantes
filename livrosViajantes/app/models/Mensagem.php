<?php

namespace App\Models;

use PDO;
use App\Database\Connection;

class Mensagem
{
    private PDO $pdo;
    private ?string $timestampColumn = null;

    public function __construct()
    {
        $this->pdo = Connection::connect();
    }

    public function enviar(int $remetente_id, int $destinatario_id, string $conteudo): bool
    {
        if ($remetente_id === $destinatario_id) {
            return false;
        }

        if (!$this->usuarioExiste($destinatario_id)) {
            return false;
        }

        $timestampColumn = $this->timestampColumn();

        $sql = "INSERT INTO mensagem (remetente_id, destinatario_id, conteudo, {$timestampColumn})
                VALUES (:remetente_id, :destinatario_id, :conteudo, CURRENT_TIMESTAMP)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':remetente_id' => $remetente_id,
            ':destinatario_id' => $destinatario_id,
            ':conteudo' => trim($conteudo)
        ]);
    }

    public function listarConversas(int $user_id): array
    {
        $timestampColumn = $this->timestampColumn();

        $sql = "SELECT
                    CASE WHEN remetente_id = :user_id THEN destinatario_id ELSE remetente_id END AS parceiro_id,
                    u.nome_usuario AS parceiro_nome,
                    m.conteudo AS ultima_mensagem,
                    m.{$timestampColumn} AS criado_em
                FROM mensagem m
                JOIN usuario u ON u.id = CASE WHEN remetente_id = :user_id THEN destinatario_id ELSE remetente_id END
                WHERE remetente_id = :user_id OR destinatario_id = :user_id
                ORDER BY m.{$timestampColumn} DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        $rows = $stmt->fetchAll();
        $conversas = [];

        foreach ($rows as $row) {
            if (!isset($conversas[$row['parceiro_id']])) {
                $conversas[$row['parceiro_id']] = [
                    'parceiro_id' => (int)$row['parceiro_id'],
                    'parceiro_nome' => $row['parceiro_nome'],
                    'ultima_mensagem' => $row['ultima_mensagem'],
                    'criado_em' => $row['criado_em']
                ];
            }
        }

        return array_values($conversas);
    }

    public function listarMensagens(int $user_id, int $partner_id): array
    {
        $timestampColumn = $this->timestampColumn();

        $sql = "SELECT m.id, m.remetente_id, m.destinatario_id, m.conteudo, m.{$timestampColumn} AS criado_em,
                       ur.nome_usuario AS remetente_nome,
                       ud.nome_usuario AS destinatario_nome
                FROM mensagem m
                JOIN usuario ur ON ur.id = m.remetente_id
                JOIN usuario ud ON ud.id = m.destinatario_id
                WHERE (m.remetente_id = :user_id AND m.destinatario_id = :partner_id)
                   OR (m.remetente_id = :partner_id AND m.destinatario_id = :user_id)
                ORDER BY m.{$timestampColumn} ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':partner_id' => $partner_id
        ]);

        return $stmt->fetchAll();
    }

    public function listarNotificacoesRecebidas(int $user_id): array
    {
        $timestampColumn = $this->timestampColumn();

        $sql = "SELECT m.id,
                       m.remetente_id,
                       m.destinatario_id,
                       m.conteudo,
                       m.{$timestampColumn} AS criado_em,
                       u.nome_usuario AS remetente_nome
                FROM mensagem m
                JOIN usuario u ON u.id = m.remetente_id
                WHERE m.destinatario_id = :user_id
                ORDER BY m.{$timestampColumn} DESC
                LIMIT 30";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    private function usuarioExiste(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM usuario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() !== false;
    }

    private function timestampColumn(): string
    {
        if ($this->timestampColumn !== null) {
            return $this->timestampColumn;
        }

        $sql = "SELECT column_name
                FROM information_schema.columns
                WHERE table_name = 'mensagem'
                AND column_name IN ('criado_em', 'enviada_em')
                ORDER BY CASE column_name WHEN 'criado_em' THEN 1 ELSE 2 END
                LIMIT 1";

        $column = $this->pdo->query($sql)->fetchColumn();
        $this->timestampColumn = in_array($column, ['criado_em', 'enviada_em'], true)
            ? $column
            : 'criado_em';

        return $this->timestampColumn;
    }
}
