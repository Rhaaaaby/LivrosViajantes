// publicacao_detalhada.js

document.addEventListener("DOMContentLoaded", async () => {
    const params = new URLSearchParams(window.location.search);
    const id = Number(params.get("id"));
    const container = document.querySelector(".detalhes-container");

    if (!container) return;

    if (!id || isNaN(id)) {
        container.innerHTML = "<p>ID do livro não informado.</p>";
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/livros/${id}`);

        // Checa se o servidor respondeu com erro antes de tentar ler JSON
        if (!response.ok) {
            const textoErro = await response.text();
            console.error("Erro retornado pelo servidor (HTML/Texto):", textoErro);
            container.innerHTML = `<p>Livro não encontrado (Erro ${response.status})</p>`;
            return;
        }

        const data = await response.json();
        const livro = data.livro;
        
        if (!livro) {
            container.innerHTML = "<p>Dados do livro não encontrados na resposta.</p>";
            return;
        }

        const btnInteresse = document.querySelector(".btn-interesse");

        document.getElementById("detalhe-titulo").textContent = livro.titulo;
        document.getElementById("detalhe-categoria").textContent = livro.categoria_nome || "Sem categoria";
        document.getElementById("detalhe-descricao").textContent = livro.descricao || "Sem descrição.";

        if (btnInteresse) {
            btnInteresse.dataset.id = livro.id;
            btnInteresse.dataset.donoId = livro.autor_id;
        }

        const img = document.getElementById("detalhe-capa");
        img.src = livro.imagem
            ? `/${livro.imagem}`
            : "/assets/img/bookcard/livro-sonho.webp";

        // Busca os dados do autor do livro
        const autorResponse = await fetch(`${API_BASE}/api/perfil-publico/${livro.autor_id}`);
        
        // VAMOS LER COMO TEXTO PRIMEIRO PARA PEGAR O PHP NO PULO
        const textoBruto = await autorResponse.text();
        console.log("CONTEÚDO REAL QUE O PHP RESPONDEU:", textoBruto);

        // Agora tentamos converter manualmente para ver se funciona
        try {
            const autorData = JSON.parse(textoBruto);
            const autor = autorData.dados?.usuario;
            const nomeAutor = autor?.nome_usuario || `Usuário ${livro.autor_id}`;
            document.getElementById("detalhe-usuario").innerHTML = `Publicado por: <a href="/pages/perfil_publico.html?id=${livro.autor_id}" style="color: var(--texto-principal); text-decoration: underline;">${nomeAutor}</a>`;
        } catch (err) {
            console.error("O JSON.parse quebrou porque o texto acima não é um JSON válido!");
            document.getElementById("detalhe-usuario").textContent = "Publicado por: Erro no formato dos dados do autor";
        }

        if (btnInteresse) {
            btnInteresse.disabled = false;
        }

    } catch (error) {
        console.error("Erro na requisição dos detalhes:", error);
        container.innerHTML = "<p>Erro ao carregar os detalhes do livro.</p>";
    }
});