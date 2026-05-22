<?php

namespace App\Controllers;

use App\Models\Seguidor;
use App\Models\Usuario;
use App\Models\Livro;
use App\Utils\Response;

class SeguidorController
{
    private Seguidor $seguidorModel;
    private Usuario $usuarioModel;
    private Livro $livroModel;

    public function __construct()
    {
        $this->seguidorModel = new Seguidor();
        $this->usuarioModel = new Usuario();
        $this->livroModel = new Livro();
    }

    public function seguir(int $seguido_id, int $user_id): array
    {
        if ($this->seguidorModel->seguir($user_id, $seguido_id)) {
            return Response::success('Usuário seguido com sucesso', 200);
        }
        return Response::error('Não foi possível seguir este usuário (talvez você já siga)', 400);
    }

    public function deixarDeSeguir(int $seguido_id, int $user_id): array
    {
        if ($this->seguidorModel->deixarDeSeguir($user_id, $seguido_id)) {
            return Response::success('Você deixou de seguir este usuário', 200);
        }
        return Response::error('Não foi possível deixar de seguir', 400);
    }

    public function listarSeguindo(int $user_id): array
    {
        $seguindo = $this->seguidorModel->listarSeguindo($user_id);
        return Response::success('Usuários que você segue', 200, ['seguindo' => $seguindo]);
    }

    public function perfilPublico(int $perfil_id, int $user_id = null): array
    {
        $usuario = $this->usuarioModel->buscarPorId($perfil_id);
        if (!$usuario) {
            return Response::error('Usuário não encontrado', 404);
        }

        unset($usuario['senha_hash'], $usuario['email']);

        $livros = $this->livroModel->listarMeusLivros($perfil_id);

        $segue = false;
        if ($user_id) {
            $segue = $this->seguidorModel->verificaSeSegue($user_id, $perfil_id);
        }

        return Response::success('Perfil público', 200, [
            'usuario' => $usuario,
            'livros' => $livros,
            'segue' => $segue
        ]);
    }
}
