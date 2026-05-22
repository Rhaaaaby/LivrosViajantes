<?php

namespace App\Controllers;

use App\Models\Mensagem;
use App\Utils\Response;

class MensagemController
{
    private Mensagem $model;

    public function __construct()
    {
        $this->model = new Mensagem();
    }

    public function listarConversas(int $user_id)
    {
        $conversas = $this->model->listarConversas($user_id);
        return Response::success('Conversas carregadas', 200, ['conversas' => $conversas]);
    }

    public function listarMensagens(int $user_id, int $partner_id)
    {
        $mensagens = $this->model->listarMensagens($user_id, $partner_id);
        return Response::success('Mensagens carregadas', 200, ['mensagens' => $mensagens]);
    }

    public function listarNotificacoes(int $user_id)
    {
        $mensagens = $this->model->listarNotificacoesRecebidas($user_id);
        return Response::success('Notificacoes de mensagens carregadas', 200, ['mensagens' => $mensagens]);
    }

    public function enviarMensagem(int $remetente_id, int $destinatario_id, string $conteudo)
    {
        if (empty(trim($conteudo))) {
            return Response::error('Mensagem não pode estar vazia', 400);
        }

        $resultado = $this->model->enviar($remetente_id, $destinatario_id, $conteudo);

        if ($resultado) {
            return Response::success('Mensagem enviada com sucesso', 201);
        }

        return Response::error('Não foi possível enviar mensagem. Verifique o destinatário ou tente novamente.', 400);
    }
}
