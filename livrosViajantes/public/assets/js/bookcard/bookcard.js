// FUNÇÃO PARA SALVAR LIVROS NO localStorage
function salvarLivro(livro) {
    let livros = JSON.parse(localStorage.getItem("livros")) || [];
    livros.push(livro);
    localStorage.setItem("livros", JSON.stringify(livros));
}

// FUNÇÃO PARA CRIAR OS CARDS DE LIVROS
function criarCard(livro) {
    const card = document.createElement("div");
    card.classList.add("book-card");

    card.innerHTML = `
        <img 
            src="assets/img/bookcard/livro-sonho.webp" 
            alt="capa do livro" 
            class="capa-livro"
        >
        <div class="info-livro">
            <h1 class="titulo-livro">${livro.titulo}</h1>
            <span class="categoria">${livro.categoria}</span>
            <p class="descricao-publicacao">${livro.descricao}</p>
            <button class="btn-interesse" data-id="${livro.id}">
                Tenho interesse
            </button>
        </div>
    `;

    card.addEventListener("click", () => {
        window.location.href = `/livrosViajantes/public/pages/publicacao_detalhada.html?id=${livro.id}`;
    });

    document.querySelector("#lista-livros").appendChild(card);
}

// FUNÇÃO PARA RENDERIZAR LIVROS
function renderizarLivros(livros) {
    const lista = document.getElementById("lista-livros");
    if (!lista) return;

    lista.innerHTML = "";
    livros.forEach(livro => criarCard(livro));
}

// CARREGA LIVROS DO localStorage
///function carregarLivros() {
///    const livros = JSON.parse(localStorage.getItem("livros")) || [];
///    renderizarLivros(livros);
///}

// nova função para fetch com php
livros.forEach(livro => {
    fetch('/livrosViajantes/public/api/livros.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            titulo: livro.titulo,
            descricao: livro.descricao,
            imagem: livro.imagem || null,
            autor_id: 1,       // ID do usuário que será autor
            categoria_id: 1,   // ID da categoria que o livro pertence
            status: true
        })
    })
    .then(res => res.json())
    .then(data => console.log(data));
});

// INICIA FORMULÁRIO PARA ADICIONAR NOVOS LIVROS
function iniciarFormularioLivro() {
    const form = document.getElementById("formLivro");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const livro = {
            id: Date.now(),
            titulo: document.getElementById("titulo").value,
            categoria: document.getElementById("categoria").value,
            descricao: document.getElementById("descricao").value
        };

        salvarLivro(livro);
        form.reset();
        carregarLivros(); // Atualiza lista de cards sem recarregar a página
    });
}

// INICIALIZA QUANDO DOM ESTÁ PRONTO
document.addEventListener("DOMContentLoaded", () => {
    carregarLivros();
    iniciarFormularioLivro();
});