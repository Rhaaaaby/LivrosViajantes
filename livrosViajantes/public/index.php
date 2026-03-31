<?php
// public/index.php – ponto de entrada único do projeto

require_once __DIR__ . '/../app/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($uri, '/api/') === 0) {
    // Todas as rotas /api/* vão para o router da API
    require_once __DIR__ . '/api/router.php';
} else {
    // Aqui você pode servir páginas HTML estáticas ou um SPA no futuro
    // Por enquanto, exemplo simples:
    echo "Bem-vindo ao Livros Viajantes! Use /api para a API.";
}