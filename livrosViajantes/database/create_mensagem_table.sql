-- Cria a tabela de mensagens entre usuários para o sistema de chat
CREATE TABLE IF NOT EXISTS mensagem (
    id SERIAL PRIMARY KEY,
    remetente_id INTEGER NOT NULL REFERENCES usuario(id),
    destinatario_id INTEGER NOT NULL REFERENCES usuario(id),
    conteudo TEXT NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
