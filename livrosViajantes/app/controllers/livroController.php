<?php

namespace App\Controllers;

use App\Models\Livro;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LivroController
{
    private Livro $livroModel;

    public function __construct()
    {
        $this->livroModel = new Livro();
    }

    /**
     * GET /api/livros → Lista todos os livros disponíveis
     */
    public function listar()
    {
        try {
            $livros = $this->livroModel->listarTodos();
            $this->jsonResponse(['livros' => $livros]);
        } catch (\Exception $e) {
            error_log("Erro ao listar livros: " . $e->getMessage());
            $this->jsonResponse(['erro' => 'Erro ao carregar livros'], 500);
        }
    }

    /**
     * POST /api/livros → Cadastra um novo livro (precisa estar logado)
     */
    public function criar(array $data, int $user_id)
    {
        // Validações básicas
        if (empty($data['titulo'])) {
            $this->jsonResponse(['erro' => 'Título é obrigatório'], 400);
            return;
        }

        if (empty($data['categoria_id'])) {
            $this->jsonResponse(['erro' => 'Categoria é obrigatória'], 400);
            return;
        }

        try {
            $sucesso = $this->livroModel->criar($data, $user_id);

            if ($sucesso) {
                $this->jsonResponse(['mensagem' => 'Livro cadastrado com sucesso!'], 201);
            } else {
                $this->jsonResponse(['erro' => 'Não foi possível cadastrar o livro'], 500);
            }
        } catch (\Exception $e) {
            error_log("Erro ao criar livro: " . $e->getMessage());
            $this->jsonResponse(['erro' => 'Erro interno'], 500);
        }
    }

    // Função auxiliar para responder JSON
    private function jsonResponse(array $data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}