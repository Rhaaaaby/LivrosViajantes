// publicacao_detalhada.js

document.addEventListener("DOMContentLoaded", async () => {
    const params = new URLSearchParams(window.location.search);
    const id = Number(params.get("id"));
    const container = document.querySelector(".detalhes-container");

    if (!container) return;

    if (!id || isNaN(id)) {
        container.innerHTML = "<p>ID do livro nao informado.</p>";
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/livros/${id}`);
        const data = await response.json();

        if (!response.ok) {
            container.innerHTML = `<p>${data.erro || "Livro nao encontrado"}</p>`;
            return;
        }

        const livro = data.livro;
        const btnInteresse = document.querySelector(".btn-interesse");

        document.getElementById("detalhe-titulo").textContent = livro.titulo;
        document.getElementById("detalhe-categoria").textContent = livro.categoria_nome || "Sem categoria";
        document.getElementById("detalhe-descricao").textContent = livro.descricao || "Sem descricao.";

        if (btnInteresse) {
            btnInteresse.dataset.id = livro.id;
            btnInteresse.dataset.donoId = livro.autor_id;
        }

        const img = document.getElementById("detalhe-capa");
        img.src = livro.imagem
            ? `/livrosViajantes/public/${livro.imagem}`
            : "/livrosViajantes/public/assets/img/bookcard/livro-sonho.webp";

        const autorResponse = await fetch(`${API_BASE}/api/perfil-publico/${livro.autor_id}`);
        if (autorResponse.ok) {
            const autorData = await autorResponse.json();
            const autor = autorData.dados?.usuario;
            const nomeAutor = autor?.nome_usuario || `Usuario ${livro.autor_id}`;
            document.getElementById("detalhe-usuario").innerHTML = `Publicado por: <a href="/livrosViajantes/public/pages/perfil_publico.html?id=${livro.autor_id}" style="color: var(--texto-principal); text-decoration: underline;">${nomeAutor}</a>`;
        } else {
            document.getElementById("detalhe-usuario").textContent = "Publicado por: Usuario desconhecido";
        }

        if (btnInteresse) {
            btnInteresse.disabled = false;
        }
    } catch (error) {
        console.error(error);
        container.innerHTML = "<p>Erro ao carregar os detalhes do livro.</p>";
    }
});
