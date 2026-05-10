<?php

namespace App\Controllers;

use App\Models\Solicitacao;
use App\Models\Livro;
use App\Utils\Response;

class SolicitacaoController
{
    private Solicitacao $solicitacaoModel;
    private Livro $livroModel;

    public function __construct()
    {
        $this->solicitacaoModel = new Solicitacao();
        $this->livroModel = new Livro();
    }

    // Criar nova solicitação de interesse
    public function criar(array $data, int $user_id): array
    {
        // Validar dados obrigatórios
        if (empty($data['livro_id']) || empty($data['tipo'])) {
            return Response::error('Dados obrigatórios faltando: livro_id e tipo', 400);
        }

        // Validar tipo
        if (!in_array($data['tipo'], ['interesse', 'troca', 'emprestimo'])) {
            return Response::error('Tipo inválido. Use: interesse, troca ou emprestimo', 400);
        }

        // Verificar se o livro existe e está disponível
        $livro = $this->livroModel->buscarPorId($data['livro_id']);
        if (!$livro) {
            return Response::error('Livro não encontrado', 404);
        }

        if (!$livro['status']) {
            return Response::error('Este livro não está disponível', 400);
        }

        // Verificar se o usuário não está solicitando interesse no próprio livro
        if ($livro['autor_id'] == $user_id) {
            return Response::error('Você não pode demonstrar interesse no seu próprio livro', 400);
        }

        // Criar solicitação
        $resultado = $this->solicitacaoModel->criar($data, $user_id);

        if ($resultado) {
            return Response::success('Interesse registrado com sucesso!', 201);
        } else {
            return Response::error('Você já demonstrou interesse neste livro', 409);
        }
    }

    // Listar solicitações recebidas (para o dono do livro)
    public function listarRecebidas(int $user_id): array
    {
        $solicitacoes = $this->solicitacaoModel->listarRecebidas($user_id);
        return Response::success('Solicitações recebidas', 200, $solicitacoes);
    }

    // Listar solicitações enviadas (pelo usuário)
    public function listarEnviadas(int $user_id): array
    {
        $solicitacoes = $this->solicitacaoModel->listarEnviadas($user_id);
        return Response::success('Solicitações enviadas', 200, $solicitacoes);
    }

    // Listar todas as solicitações do usuário (recebidas e enviadas)
    public function listarTodas(int $user_id): array
    {
        $recebidas = $this->solicitacaoModel->listarRecebidas($user_id);
        $enviadas = $this->solicitacaoModel->listarEnviadas($user_id);
        return Response::success('Solicitações do usuário', 200, [
            'recebidas' => $recebidas,
            'enviadas' => $enviadas
        ]);
    }

    // Cancelar solicitação (apenas pelo solicitante)
    public function cancelar(int $solicitacao_id, int $user_id): array
    {
        $resultado = $this->solicitacaoModel->cancelar($solicitacao_id, $user_id);

        if ($resultado) {
            return Response::success('Solicitação cancelada com sucesso!', 200);
        } else {
            return Response::error('Solicitação não encontrada ou você não tem permissão para cancelar', 404);
        }
    }
}