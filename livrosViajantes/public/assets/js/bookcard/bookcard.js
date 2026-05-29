// bookcard.js - Integrado com API real

let todosLivros = [];

// Criar card de livro
function criarCard(livro) {

    const card = document.createElement("div");
    card.classList.add("book-card");

    const imagem = livro.imagem
        ? `/${livro.imagem}`
        : `/assets/img/bookcard/livro-sonho.webp`;

    card.innerHTML = `
        <img src="${imagem}" alt="${livro.titulo}" class="capa-livro">

        <div class="info-livro">
            <h1 class="titulo-livro">${livro.titulo}</h1>

            <span class="categoria">
                ${livro.categoria_nome || 'Sem categoria'}
            </span>

            <p class="descricao-publicacao">
                ${livro.descricao
            ? livro.descricao.substring(0, 120) + '...'
            : 'Sem descrição'
        }
            </p>

            <button class="btn-detalhes" data-id="${livro.id}">
                Ver Detalhes
            </button>
        </div>
    `;

    // Clique no card
    card.addEventListener("click", (e) => {

        // O botao tem a propria navegacao abaixo.
        if (e.target.classList.contains("btn-detalhes")) {
            return;
        }

        window.location.href =
            `/pages/publicacao_detalhada.html?id=${livro.id}`;
    });

    const btnDetalhes = card.querySelector(".btn-detalhes");
    btnDetalhes.addEventListener("click", () => {
        window.location.href =
            `/pages/publicacao_detalhada.html?id=${livro.id}`;
    });

    return card;
}

// Renderizar livros
function renderizarLivros(livros) {

    const lista = document.getElementById("lista-livros");

    if (!lista) return;

    lista.innerHTML = "";

    todosLivros = livros;

    if (livros.length === 0) {

        lista.innerHTML = `
            <p class="lista-vazia">
                Nenhum livro encontrado 📭
            </p>
        `;

        return;
    }

    livros.forEach((livro) => {
        lista.appendChild(criarCard(livro));
    });
}

// Carregar livros
async function carregarLivros() {

    try {

        const response = await fetch(`${window.API_BASE}/api/listar`);

        const data = await response.json();

        if (response.ok) {

            renderizarLivros(data.livros || []);

        } else {

            console.error("Erro ao carregar livros:", data.erro);
        }

    } catch (error) {

        console.error("Erro de conexão:", error);
    }
}

// Inicialização
document.addEventListener("DOMContentLoaded", () => {
    // Só carrega livros automaticamente se não estiver na página de pesquisa
    if (!document.querySelector(".pesquisar")) {
        carregarLivros();
    }
});
