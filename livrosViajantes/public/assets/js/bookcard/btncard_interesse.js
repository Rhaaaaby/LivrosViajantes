// btncard_interesse.js - Botão "Tenho Interesse"

const API_BASE = window.location.pathname.includes('/pages/') ? window.location.pathname.split('/pages/')[0] : '';

document.addEventListener("click", async function (e) {
    if (e.target.classList.contains("btn-interesse")) {
        const livroId = e.target.dataset.id;
        const token = localStorage.getItem("token");

        if (!token) {
            mostrarMensagem("Você precisa estar logado para demonstrar interesse!", "erro");
            return;
        }

        try {
            const response = await fetch(`${API_BASE}/api/solicitacoes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    livro_id: livroId,
                    tipo: 'interesse'   // ou 'troca', 'emprestimo' no futuro
                })
            });

            const resultado = await response.json();

            if (response.ok) {
                mostrarMensagem("Interesse registrado! O dono foi notificado.", "sucesso");
                e.target.textContent = "Interesse enviado ✓";
                e.target.disabled = true;
            } else {
                mostrarMensagem(resultado.erro || "Erro ao registrar interesse", "erro");
            }
        } catch (error) {
            console.error(error);
            mostrarMensagem("Erro de conexão", "erro");
        }
    }
});