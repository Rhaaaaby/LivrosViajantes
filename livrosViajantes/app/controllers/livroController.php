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

    // GET /api/livros → Lista todos os livros disponíveis
    public function listar()
    {
        $livros = $this->model->listarTodos();
        json_response(['livros' => $livros]);
    }

    // GET /api/meus-livros → Lista apenas os livros do usuário logado
    public function meusLivros(int $user_id)
    {
        $livros = $this->model->listarMeusLivros($user_id);
        json_response(['meus_livros' => $livros]);
    }

    // POST /api/livros → Cadastra novo livro (com upload de imagem básico)
    public function criar(array $data, int $user_id)
    {

        // Validações básicas
        if (empty($data['titulo'])) {
            json_response(['erro' => 'O título do livro é obrigatório'], 400);
        }

        if (empty($data['categoria_id'])) {
            json_response(['erro' => 'Categoria é obrigatória'], 400);
        }

        $imagemPath = null;

        // Tratamento de upload de imagem (se enviada)
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/img/livros/';
            
            // Cria a pasta se não existir
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = uniqid('livro_') . '.' . strtolower($extensao);
            $caminhoCompleto = $uploadDir . $nomeArquivo;

            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array(strtolower($extensao), $extensoesPermitidas)) {
                json_response(['erro' => 'Formato de imagem não permitido. Use JPG, PNG ou WEBP'], 400);
            }

            if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) { // 5MB
                json_response(['erro' => 'A imagem não pode ter mais de 5MB'], 400);
            }

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
                $imagemPath = 'assets/img/livros/' . $nomeArquivo;
            } else {
                json_response(['erro' => 'Erro ao salvar a imagem'], 500);
            }
        }

        $sucesso = $this->model->criar($data, $user_id, $imagemPath);

        if ($sucesso) {
            json_response(['mensagem' => 'Livro cadastrado com sucesso!'], 201);
        } else {
            json_response(['erro' => 'Erro ao cadastrar o livro'], 500);
        }
    }
}