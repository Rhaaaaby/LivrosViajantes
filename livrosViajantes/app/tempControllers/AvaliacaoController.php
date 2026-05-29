<?php

namespace App\Controllers;

use App\Models\Avaliacao;
use App\Utils\Response;

class AvaliacaoController
{
    private Avaliacao $model;

    public function __construct()
    {
        $this->model = new Avaliacao();
    }

    public function criar(array $data, int $user_id): array
    {
        if (empty($data['estrelas']) || !is_numeric($data['estrelas'])) {
            return Response::error('A nota de estrelas é obrigatória e deve ser um número', 400);
        }

        $estrelas = (int)$data['estrelas'];
        if ($estrelas < 1 || $estrelas > 5) {
            return Response::error('A nota deve ser entre 1 e 5 estrelas', 400);
        }

        $comentario = $data['comentario'] ?? '';

        $sucesso = $this->model->avaliar($user_id, $estrelas, $comentario);

        if ($sucesso) {
            return Response::success('Avaliação enviada com sucesso! Muito obrigado pelo seu feedback.', 201);
        }
        
        return Response::error('Ocorreu um erro ao enviar sua avaliação', 500);
    }
}
