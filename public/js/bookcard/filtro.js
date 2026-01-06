document.addEventListener("DOMContentLoaded", () => {
    const inputTitulo = document.getElementById("filtro-titulo");
    const selectCategoria = document.getElementById("filtro-categoria");
    const botaoBuscar = document.getElementById("btn-buscar");
    const lista = document.getElementById("lista-livros");

    let categoriaAtiva = "";

    document.querySelectorAll(".filtro-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.querySelectorAll(".filtro-btn")
                .forEach(b => b.classList.remove("ativo"));

            btn.classList.add("ativo");
            categoriaAtiva = btn.dataset.categoria;

            aplicarFiltros();
        });
    });


    function obterLivros() {
        return JSON.parse(localStorage.getItem("livros")) || [];
    }

    function aplicarFiltros() {
        const termo = inputTitulo.value.toLowerCase().trim();
        const categoria = categoriaAtiva;

        const livros = obterLivros();

        const filtrados = livros.filter(livro => {
            const matchTitulo =
                !termo || livro.titulo?.toLowerCase().includes(termo);

            const matchCategoria =
                !categoria || livro.categoria === categoria;

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

        livros.forEach(livro => criarCard(livro));
    }

    // eventos
    inputTitulo.addEventListener("input", aplicarFiltros);
    selectCategoria.addEventListener("change", aplicarFiltros);
    botaoBuscar.addEventListener("click", aplicarFiltros);

    inputTitulo.addEventListener("keydown", e => {
        if (e.key === "Enter") aplicarFiltros();
    });

    // primeira renderização
    aplicarFiltros();
});