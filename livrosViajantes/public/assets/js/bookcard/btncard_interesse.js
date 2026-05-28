// btncard_interesse.js - Botao "Tenho Interesse" da pagina detalhada

document.addEventListener("click", async function (e) {
    if (!e.target.classList.contains("btn-interesse")) {
        return;
    }

    const livroId = e.target.dataset.id;
    const donoId = e.target.dataset.donoId;
    const token = localStorage.getItem("token");
    const textoOriginal = e.target.textContent;

    if (e.target.dataset.enviando === "1") {
        return;
    }

    if (!livroId) {
        mostrarMensagem("Abra os detalhes do livro antes de demonstrar interesse.", "erro");
        return;
    }

    if (!token) {
        mostrarMensagem("Voce precisa estar logado para demonstrar interesse!", "erro");
        return;
    }

    try {
        e.target.dataset.enviando = "1";
        e.target.textContent = "Enviando...";

        const response = await fetch(`${API_BASE}/api/solicitacoes`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify({
                livro_id: livroId,
                tipo: "interesse"
            })
        });

        const respostaTexto = await response.text();
        let resultado;

        try {
            resultado = JSON.parse(respostaTexto);
        } catch (parseError) {
            console.error("Resposta inesperada da API:", respostaTexto);
            throw new Error("Resposta invalida da API");
        }

        if (response.ok && resultado.sucesso !== false) {
            const parceiroId = resultado.dados?.dono_id || donoId;

            mostrarMensagem(resultado.mensagem || "Interesse registrado! Chat aberto com o dono.", "sucesso");
            e.target.textContent = "Interesse enviado";
            e.target.disabled = true;

            if (parceiroId) {
                setTimeout(() => {
                    window.location.href = `./pages/mensagem.html?destino=${parceiroId}`;
                }, 900);
            }
            return;
        }

        e.target.dataset.enviando = "0";
        e.target.textContent = textoOriginal;
        mostrarMensagem(resultado.erro || resultado.mensagem || "Erro ao registrar interesse", "erro");
    } catch (error) {
        console.error(error);
        e.target.dataset.enviando = "0";
        e.target.textContent = textoOriginal;
        mostrarMensagem("Erro ao abrir chat. Tente novamente em instantes.", "erro");
    }
});
