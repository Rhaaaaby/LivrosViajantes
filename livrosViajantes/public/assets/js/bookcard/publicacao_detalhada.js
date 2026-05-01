// publicacao_detalhada.js

const API_BASE = window.location.pathname.includes('/pages/') ? window.location.pathname.split('/pages/')[0] : '';

document.addEventListener("DOMContentLoaded", async () => {
    const params = new URLSearchParams(window.location.search);
    const id = Number(params.get("id"));
    const container = document.querySelector(".detalhes-container");

    if (!id) {
        container.innerHTML = "<p>ID do livro não informado.</p>";
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/livros/${id}`);
        const data = await response.json();

        if (!response.ok) {
            container.innerHTML = `<p>${data.erro || 'Livro não encontrado'}</p>`;
            return;
        }

        const livro = data.livro;

        document.getElementById("detalhe-titulo").textContent = livro.titulo;
        document.getElementById("detalhe-categoria").textContent = livro.categoria_nome || 'Sem categoria';
        document.getElementById("detalhe-descricao").textContent = livro.descricao || 'Sem descrição.';

        // Imagem
        const img = document.getElementById("detalhe-capa");
        img.src = livro.imagem 
            ? `/livrosViajantes/public/${livro.imagem}` 
            : `/livrosViajantes/public/assets/img/bookcard/livro-sonho.webp`;

    } catch (error) {
        console.error(error);
        container.innerHTML = "<p>Erro ao carregar os detalhes do livro.</p>";
    }
});