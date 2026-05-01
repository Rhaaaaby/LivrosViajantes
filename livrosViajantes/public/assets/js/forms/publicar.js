// publicar.js - Cadastro de livro com imagem

const API_BASE = window.location.pathname.includes('/pages/') ? window.location.pathname.split('/pages/')[0] : '';

configurarFormulario({
    formId: "formPublicar",
    validar: (form) => {
        if (!form.titulo.value.trim()) {
            mostrarMensagem("Título é obrigatório!", "erro");
            return false;
        }
        return true;
    },
    aoEnviar: async (form) => {
        const token = localStorage.getItem("token");

        if (!token) {
            mostrarMensagem("Você precisa estar logado para publicar!", "erro");
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(`${API_BASE}/api/publicar`, {
                method: "POST",
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            const resultado = await response.json();

            if (response.ok) {
                mostrarMensagem("Livro publicado com sucesso!", "sucesso");
                // Recarrega a lista de livros após publicar
                setTimeout(() => {
                    window.location.href = "/livrosViajantes/public/pages/index.php"; // ou página de listagem
                }, 1500);
            } else {
                mostrarMensagem(resultado.erro || "Erro ao publicar livro", "erro");
            }
        } catch (erro) {
            console.error(erro);
            mostrarMensagem("Erro de conexão com o servidor", "erro");
        }
    },
    sucessoMsg: "" // controlar a mensagem dentro do aoEnviar
});