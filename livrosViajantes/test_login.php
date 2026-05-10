<?php

require_once 'app/bootstrap.php';

use App\Models\Usuario;

$u = new Usuario();
$user = $u->buscarPorEmail('Usuário Teste');

if ($user) {
    echo 'Usuário encontrado. Testando senhas...\n';

    $senhas = ['123456', 'teste', 'password', 'admin'];

    foreach ($senhas as $senha) {
        if (password_verify($senha, $user['senha_hash'])) {
            echo "✅ Senha encontrada: $senha\n";
            break;
        }
    }
} else {
    echo "Usuário não encontrado\n";
}