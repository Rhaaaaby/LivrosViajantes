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
        var_dump($data);
        exit;

        var_dump($usuario);
        exit;

        if (empty($data['email']) || empty($data['senha_hash'])) {
            json_response(['erro' => 'Email e senha são obrigatórios'], 400);
        }

        $usuario = $this->model->buscarPorEmail($data['email']);

        if (!$usuario || !password_verify($data['senha_hash'], $usuario['senha_hash'])) {
            json_response(['erro' => 'Credenciais inválidas'], 401);
        }

        $payload = [
            'sub'          => $usuario['id'],
            'nome_usuario' => $usuario['nome_usuario'],
            'iat'          => time(),
            'exp'          => time() + (60 * 60 * 24)   // 24 horas
        ];

        var_dump($_ENV['JWT_SECRET']);
        exit;

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

    public function atualizar(int $user_id, array $data)
    {
        $sucesso = $this->model->atualizar($user_id, $data);
        if ($sucesso) {
            json_response(['mensagem' => 'Perfil atualizado com sucesso']);
        } else {
            json_response(['erro' => 'Erro ao atualizar perfil'], 500);
        }
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