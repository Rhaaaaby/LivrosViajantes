<?php

namespace App\Models;

use PDO;
use App\Database\Connection;

class Mensagem
{
    private PDO $pdo;

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

        $sql = "INSERT INTO mensagem (remetente_id, destinatario_id, conteudo, enviada_em)
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
        $sql = "SELECT
                    CASE WHEN remetente_id = :user_id THEN destinatario_id ELSE remetente_id END AS parceiro_id,
                    u.nome_usuario AS parceiro_nome,
                    m.conteudo AS ultima_mensagem,
                    m.enviada_em
                FROM mensagem m
                JOIN usuario u ON u.id = CASE WHEN remetente_id = :user_id THEN destinatario_id ELSE remetente_id END
                WHERE remetente_id = :user_id OR destinatario_id = :user_id
                ORDER BY m.enviada_em DESC";

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
                    'enviada_em' => $row['enviada_em']
                ];
            }
        }

        return array_values($conversas);
    }

    public function listarMensagens(int $user_id, int $partner_id): array
    {
        $sql = "SELECT m.id, m.remetente_id, m.destinatario_id, m.conteudo, m.enviada_em,
                       ur.nome_usuario AS remetente_nome,
                       ud.nome_usuario AS destinatario_nome
                FROM mensagem m
                JOIN usuario ur ON ur.id = m.remetente_id
                JOIN usuario ud ON ud.id = m.destinatario_id
                WHERE (m.remetente_id = :user_id AND m.destinatario_id = :partner_id)
                   OR (m.remetente_id = :partner_id AND m.destinatario_id = :user_id)
                ORDER BY m.enviada_em ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':partner_id' => $partner_id
        ]);

        return $stmt->fetchAll();
    }

    private function usuarioExiste(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM usuario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() !== false;
    }
}
