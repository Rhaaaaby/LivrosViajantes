// filtro.js - Filtros com dados da API

document.addEventListener("DOMContentLoaded", () => {
    const inputTitulo = document.getElementById("filtro-titulo");
    const botoesCategoria = document.querySelectorAll(".filtro-btn");
    const botaoBuscar = document.getElementById("btn-buscar");
    const lista = document.getElementById("lista-livros");
    
    let livrosParaFiltro = [];
    let categoriaAtual = "";

// Carregar livros da API
    async function carregarLivrosFiltro() {
        try {
            const res = await fetch(`${window.API_BASE}/api/listar`);
            const data = await res.json();
            if (res.ok) {
                livrosParaFiltro = data.livros || [];
                aplicarFiltros();
            }
        } catch (err) {
            console.error("Erro ao carregar livros para filtro:", err);
        }
    }

    function aplicarFiltros() {
        const termo = inputTitulo ? inputTitulo.value.toLowerCase().trim() : "";

        const filtrados = livrosParaFiltro.filter(livro => {
            const matchTitulo = !termo || (livro.titulo && livro.titulo.toLowerCase().includes(termo));
            
            const matchCategoria = !categoriaAtual || 
                (livro.categoria_nome && livro.categoria_nome.toLowerCase() === categoriaAtual.toLowerCase());
                
            return matchTitulo && matchCategoria;
        });

        renderizarResultado(filtrados);
    }

    function renderizarResultado(livros) {
        if (!lista) return;
        lista.innerHTML = "";
        if (livros.length === 0) {
            lista.innerHTML = "<p class='lista-vazia'>Nenhum livro encontrado 📭</p>";
            return;
        }
        livros.forEach(livro => {
            if (typeof criarCard === "function") {
                lista.appendChild(criarCard(livro));
            }
        });
    }

    // Eventos
    if (inputTitulo) inputTitulo.addEventListener("input", aplicarFiltros);
    if (botaoBuscar) botaoBuscar.addEventListener("click", aplicarFiltros);
    
    if (botoesCategoria.length > 0) {
        botoesCategoria.forEach(botao => {
            botao.addEventListener("click", (e) => {
                botoesCategoria.forEach(b => b.classList.remove("ativo"));
                e.target.classList.add("ativo");
                categoriaAtual = e.target.getAttribute("data-categoria") || "";
                aplicarFiltros();
            });
        });
    }

    // Carregar ao iniciar
    carregarLivrosFiltro();
});