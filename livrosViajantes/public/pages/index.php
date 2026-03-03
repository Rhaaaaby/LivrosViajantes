<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Livros Viajantes</title>
    <meta name="description" content="Compartilhe e descubra livros que viajam pelo mundo!">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/livrosViajantes/public/assets/css/sistema_base/basestyle.css">
    <link rel="stylesheet" href="/livrosViajantes/public/assets/css/sistema_base/headerstyle.css">
    <link rel="stylesheet" href="/livrosViajantes/public/assets/css/sistema_base/bookcardstyle.css">
    <link rel="stylesheet" href="/livrosViajantes/public/assets/css/sistema_base/footerstyle.css">
    <link rel="stylesheet" href="/livrosViajantes/public/assets/css/sistema_base/tab_menustyle.css">
    <link rel="stylesheet" href="/livrosViajantes/public/assets/css/sistema_base/responsive.css">

    <link rel="icon" href="/livrosViajantes/public/assets/img/logo.png">
</head>

<body>
    <!-- Header Global -->
    <header id="header"></header>
    
    <main>
        <div id="lista-livros"></div>

        <nav id="tab_menu"></nav>
    </main>

    <!-- Footer Global -->
    <footer id="footer"></footer>

    <!-- carregando as páginas -->
    <script>
        const BASE_COMPONENTS = "/livrosViajantes/public/assets/js/core/components/";
    </script>
    <script src="/livrosViajantes/public/assets/js/core/carregar_paginas.js"></script>
    <script src="/livrosViajantes/public/assets/js/bookcard/bookcard.js"></script>
    <script src="/livrosViajantes/public/assets/js/bookcard/publicacao_detalhada.js"></script>

</body>
</html>