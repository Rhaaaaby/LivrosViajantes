<?php

namespace App\Controllers;

use App\Models\Livro;

class LivroController
{
    private Livro $model;

    public function __construct()
    {
        $this->model = new Livro();
    }

    public function listar()
    {
        $livros = $this->model->listarTodos();
        json_response(['livros' => $livros]);
    }

    public function criar(array $data, int $user_id)
    {
        if (empty($data['titulo']) || empty($data['categoria_id'])) {
            json_response(['erro' => 'Título e categoria são obrigatórios'], 400);
        }

        $sucesso = $this->model->criar($data, $user_id);

        if ($sucesso) {
            json_response(['mensagem' => 'Livro cadastrado com sucesso!'], 201);
        } else {
            json_response(['erro' => 'Erro ao cadastrar livro'], 500);
        }
    }
}