<?php

//chamando o bootstrap

require_once __DIR__ . '/../app/bootstrap.php';

//chamando as páginas dinamicamente

$page = $_GET['page'] ?? 'index';

$path = __DIR__ . "/pages/$page.html";

if (file_exists($path)) {
    include $path;
} else {
    include __DIR__ . "/pages/404.html";
}

//chamando o index com todo o front-end

include __DIR__ . '/pages/index.html'; ?>