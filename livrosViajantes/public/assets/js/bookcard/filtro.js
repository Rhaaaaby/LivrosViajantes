// filtro.js - Filtros com dados da API

let todosLivros = [];

document.addEventListener("DOMContentLoaded", () => {
    const inputTitulo = document.getElementById("filtro-titulo");
    const selectCategoria = document.getElementById("filtro-categoria");
    const botaoBuscar = document.getElementById("btn-buscar");
    const lista = document.getElementById("lista-livros");

// Carregar livros da API
    async function carregarLivros() {
        try {
            const res = await fetch(`${API_BASE}/api/listar`);
            const data = await res.json();
            if (res.ok) {
                todosLivros = data.livros || [];
                aplicarFiltros();
            }
        } catch (err) {
            console.error("Erro ao carregar livros para filtro:", err);
        }
    }

    function aplicarFiltros() {
        const termo = inputTitulo.value.toLowerCase().trim();
        const categoria = selectCategoria ? selectCategoria.value : "";

        const filtrados = todosLivros.filter(livro => {
            const matchTitulo = !termo || livro.titulo.toLowerCase().includes(termo);
            const matchCategoria = !categoria || livro.categoria_nome === categoria;
            return matchTitulo && matchCategoria;
        });

        renderizarResultado(filtrados);
    }

    function renderizarResultado(livros) {
        lista.innerHTML = "";
        if (livros.length === 0) {
            lista.innerHTML = "<p class='lista-vazia'>Nenhum livro encontrado 📭</p>";
            return;
        }
        livros.forEach(livro => lista.appendChild(criarCard(livro)));
    }

    // Eventos
    if (inputTitulo) inputTitulo.addEventListener("input", aplicarFiltros);
    if (selectCategoria) selectCategoria.addEventListener("change", aplicarFiltros);
    if (botaoBuscar) botaoBuscar.addEventListener("click", aplicarFiltros);

    // Carregar ao iniciar
    carregarLivros();
});