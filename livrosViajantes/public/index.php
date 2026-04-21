<?php
// public/index.php – ponto de entrada único do projeto

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/api/router.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


require_once __DIR__ . '/pages/index.php';

//if (strpos($uri, '/api/') === 0) {
    
//} else {
// echo "Bem-vindo ao Livros Viajantes! Use /api para a API.";
//}