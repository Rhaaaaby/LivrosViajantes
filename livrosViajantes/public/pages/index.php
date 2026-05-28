<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Livros Viajantes</title>
    <meta name="description" content="Compartilhe e descubra livros que viajam pelo mundo!">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/sistema_base/basestyle.css">
    <link rel="stylesheet" href="./assets/css/sistema_base/headerstyle.css">
    <link rel="stylesheet" href="./assets/css/sistema_base/bookcardstyle.css">
    <link rel="stylesheet" href="./assets/css/sistema_base/footerstyle.css">
    <link rel="stylesheet" href="./assets/css/sistema_base/tab_menustyle.css">
    <link rel="stylesheet" href="./assets/css/sistema_base/responsive.css">
    <link rel="stylesheet" href="./assets/css/sistema_base/cabecalho.css">
    <link rel="stylesheet" href="./assets/css/sistema_base/menu_hamburger.css">
    <link rel="stylesheet" href="./assets/css/sistema_base/encarte_boas_vindas.css">

    <link rel="icon" href="./assets/img/logo.png">
</head>

<body>
    <!-- Header Global -->
    <header id="header"></header>
    
    <div id="cabecalho"></div>

    <div class="layout-com-sidebar">
        <main class="conteudo-principal">
            <div id="lista-livros"></div>

            <nav id="tab_menu"></nav>
        </main>

        <aside class="sidebar-mensagens" 
        style="
            display: flex;  
            margin-top: 90px;
            position: sticky;
            top: 90px;
            height: calc(100vh - 90px);
        "                                                                                                   
        >
            <iframe src="./pages/mensagem.html" frameborder="0"></iframe>
        </aside>
    </div>

    <!-- Footer Global -->
    <footer id="footer"></footer>

    <!-- carregando as páginas -->
    <script src="./assets/js/core/api-config.js?v=1"></script>
    <script src="./assets/js/core/carregar_paginas.js?v=1"></script>
    <script src="./assets/js/core/menu_hamburger.js?v=1"></script>
    <script src="./assets/js/bookcard/bookcard.js?v=123456789"></script>
    <script src="./assets/js/core/encarte_boas_vindas.js?v=1"></script>

</body>
</html>