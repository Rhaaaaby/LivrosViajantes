<?php
//consertando nomes antigos no github
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
    public function listar() //já testado
    {
        $livros = $this->model->listarTodos();
        json_response(['livros' => $livros]);
    }

    // GET /api/meus-livros → Lista apenas os livros do usuário logado
    public function meusLivros(int $user_id) //já testado
    {
        $livros = $this->model->listarMeusLivros($user_id);
        json_response(['meus_livros' => $livros]);
    }

       // POST /api/livros - Criar com upload
    public function criar(array $data, int $user_id) //já testado, mas sem upload de imagens
    {
        if (empty($data['titulo']) || empty($data['categoria_id'])) {
            json_response(['erro' => 'Título e categoria são obrigatórios'], 400);
        }

        if (!$this->model->categoriaExiste((int)$data['categoria_id'])) {
            json_response(['erro' => 'Categoria inválida ou não configurada'], 400);
        }

        $imagemPath = $this->uploadImagem();

        $sucesso = $this->model->criar($data, $user_id, $imagemPath);

        if ($sucesso) {
            json_response(['mensagem' => 'Livro cadastrado com sucesso!'], 201);
        } else {
            json_response(['erro' => 'Erro ao cadastrar livro'], 500);
        }
    }

    // PUT /api/livros/{id} - Atualizar (com nova imagem opcional)
    public function atualizar(int $id, array $data, int $user_id) //já testado
    {
        if (empty($data['titulo'])) {
            json_response(['erro' => 'Título é obrigatório'], 400);
        }

        if (!empty($data['categoria_id']) && !$this->model->categoriaExiste((int)$data['categoria_id'])) {
            json_response(['erro' => 'Categoria inválida ou não configurada'], 400);
        }

        $novaImagem = $this->uploadImagem();

        $sucesso = $this->model->atualizarComImagem($id, $data, $user_id, $novaImagem);

        if ($sucesso) {
            json_response(['mensagem' => 'Livro atualizado com sucesso']);
        } else {
            json_response(['erro' => 'Não foi possível atualizar. Verifique se é o dono do livro'], 403);
        }
    }

    // Função privada para upload de imagem (reutilizável)
    private function uploadImagem(): ?string 
    {
        if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/img/livros/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            json_response(['erro' => 'Formato de imagem inválido. Use JPG, PNG ou WEBP'], 400);
        }

        if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
            json_response(['erro' => 'A imagem não pode exceder 5MB'], 400);
        }

        $novoNome = uniqid('livro_') . '.' . $ext;
        $caminhoCompleto = $uploadDir . $novoNome;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
            return 'assets/img/livros/' . $novoNome;
        }

        return null;
    }

    /**
     * PUT /api/livros/{id} → Atualiza um livro
    

    public function atualizar(int $id, array $data, int $user_id)
    {
        if (empty($data['titulo'])) {
            json_response(['erro' => 'Título é obrigatório'], 400);
        }

        $sucesso = $this->model->atualizar($id, $data, $user_id);

        if ($sucesso) {
            json_response(['mensagem' => 'Livro atualizado com sucesso']);
        } else {
            json_response(['erro' => 'Não foi possível atualizar o livro (verifique se é o dono)'], 403);
        }
    }
    */

    /**
     * DELETE /api/livros/{id} → Deleta (desativa) um livro
     */
    public function deletar(int $id, int $user_id) //já testado
    {
        $sucesso = $this->model->deletar($id, $user_id);

        if ($sucesso) {
            json_response(['mensagem' => 'Livro excluído com sucesso']);
        } else {
            json_response(['erro' => 'Não foi possível excluir o livro (verifique se é o dono)'], 403);
        }
    }

    public function buscarUm(int $id) //já testado/
    {
        $livro = $this->model->buscarPorId($id);
        if (!$livro) {
            json_response(['erro' => 'Livro não encontrado'], 404);
        }
        json_response(['livro' => $livro]);
    }
}