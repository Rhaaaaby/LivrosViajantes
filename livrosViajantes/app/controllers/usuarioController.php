<?php

namespace App\Controllers;

use App\Models\Usuario;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsuarioController
{
    private Usuario $model;

    public function __construct()
    {
        $this->model = new Usuario();
    }

    public function registrar(array $data)
    {
        if (empty($data['nome_usuario']) || empty($data['email']) || empty($data['senha'])) {
            json_response(['erro' => 'Nome de usuário, email e senha são obrigatórios'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            json_response(['erro' => 'Email inválido'], 400);
        }

        if (strlen($data['senha']) < 8) {
            json_response(['erro' => 'A senha deve ter no mínimo 8 caracteres'], 400);
        }

        // Verifica se email já existe
        if ($this->model->buscarPorEmail($data['email'])) {
            json_response(['erro' => 'Este email já está cadastrado'], 409);
        }

        $sucesso = $this->model->criar($data);

        if ($sucesso) {
            json_response(['mensagem' => 'Usuário criado com sucesso'], 201);
        } else {
            json_response(['erro' => 'Erro ao criar usuário'], 500);
        }
    }

    public function login(array $data)
    {
        if (empty($data['email']) || empty($data['senha'])) {
            json_response(['erro' => 'Email e senha são obrigatórios'], 400);
        }

        $usuario = $this->model->buscarPorEmail($data['email']);

        ///var_dump([
        ///'senha_digitada' => $data['senha'],
        ///'senha_tipo' => gettype($data['senha']),
        ///'hash' => $usuario['senha_hash'],
        ///'verify' => password_verify($data['senha'], $usuario['senha_hash'])
        ///]);
        ///exit;

        if (
        !isset($data['email'], $data['senha']) ||
        !$usuario ||
        !isset($usuario['senha_hash']) ||
        !password_verify($data['senha'], $usuario['senha_hash'])
        ) {
        json_response(['erro' => 'Credenciais inválidas'], 401);
        }

        //if (!$usuario || !password_verify($data['senha_hash'], $usuario['senha_hash'])) {
          //  json_response(['erro' => 'Credenciais inválidas'], 401);
        //}

        $payload = [
            'sub'          => $usuario['id'],
            'nome_usuario' => $usuario['nome_usuario'],
            'iat'          => time(),
            'exp'          => time() + (60 * 60 * 24)   // 24 horas
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        json_response([
            'mensagem' => 'Login realizado com sucesso',
            'token'    => $jwt,
            'usuario'  => [
                'id'           => $usuario['id'],
                'nome_usuario' => $usuario['nome_usuario']
            ]
        ]);
    }

    public function perfil(int $user_id)
    {
        $usuario = $this->model->buscarPorId($user_id);
        if (!$usuario) {
            json_response(['erro' => 'Usuário não encontrado'], 404);
        }
        json_response(['usuario' => $usuario]);
    }

    public function buscarPorId(int $user_id)
    {
        $usuario = $this->model->buscarPorId($user_id);
        if (!$usuario) {
            json_response(['sucesso' => false, 'erro' => 'Usuário não encontrado'], 404);
        }
        json_response(['sucesso' => true, 'usuario' => $usuario]);
    }

    public function atualizar(int $user_id, array $data)
    {
        $fotoPath = $this->uploadFoto();
        if ($fotoPath !== null) {
            $data['foto'] = $fotoPath;
        }

        $sucesso = $this->model->atualizar($user_id, $data);
        if ($sucesso) {
            json_response(['mensagem' => 'Perfil atualizado com sucesso']);
        } else {
            json_response(['erro' => 'Erro ao atualizar perfil'], 500);
        }
    }

    private function uploadFoto(): ?string
    {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/img/usuarios/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            json_response(['erro' => 'Formato de imagem inválido. Use JPG, PNG ou WEBP'], 400);
        }

        if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            json_response(['erro' => 'A imagem não pode exceder 5MB'], 400);
        }

        $novoNome = uniqid('usuario_') . '.' . $ext;
        $caminhoCompleto = $uploadDir . $novoNome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoCompleto)) {
            return 'assets/img/usuarios/' . $novoNome;
        }

        return null;
    }

    public function deletar(int $user_id)
    {
        $sucesso = $this->model->deletar($user_id);
        if ($sucesso) {
            json_response(['mensagem' => 'Conta deletada com sucesso']);
        } else {
            json_response(['erro' => 'Erro ao deletar conta'], 500);
        }
    }
}