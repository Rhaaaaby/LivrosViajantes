// bookcard.js - Integrado com API real

let todosLivros = [];

// Criar card de livro
    const card = document.createElement("div");
    card.classList.add("book-card");

    const imagem = livro.imagem 
        ? `/livrosViajantes/public/${livro.imagem}` 
        : `/livrosViajantes/public/assets/img/bookcard/livro-sonho.webp`;

    card.innerHTML = `
        <img src="${imagem}" alt="${livro.titulo}" class="capa-livro">
        <div class="info-livro">
            <h1 class="titulo-livro">${livro.titulo}</h1>
            <span class="categoria">${livro.categoria_nome || 'Sem categoria'}</span>
            <p class="descricao-publicacao">${livro.descricao ? livro.descricao.substring(0, 120) + '...' : 'Sem descrição'}</p>
            <button class="btn-interesse" data-id="${livro.id}">
                Tenho interesse
            </button>
        </div>
    `;

    // Clique no card (exceto no botão)
    card.addEventListener("click", (e) => {
        if (!e.target.classList.contains("btn-interesse")) {
            window.location.href = `/livrosViajantes/public/pages/publicacao_detalhada.html?id=${livro.id}`;
        }
    });

    return card;
}

// Renderizar lista de livros
function renderizarLivros(livros) {
    const lista = document.getElementById("lista-livros");
    if (!lista) return;

    lista.innerHTML = "";
    todosLivros = livros;

    if (livros.length === 0) {
        lista.innerHTML = `<p class="lista-vazia">Nenhum livro encontrado 📭</p>`;
        return;
    }

    livros.forEach(livro => lista.appendChild(criarCard(livro)));
}

// Carregar livros da API
async function carregarLivros() {
    try {
        const response = await fetch(`${API_BASE}/api/listar`);
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
    carregarLivros();
});