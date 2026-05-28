// publicar.js - Cadastro de livro com imagem

let editId = null;

document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    if (!token) {
        mostrarMensagem("Você precisa estar logado para acessar esta página.", "erro");
        window.location.href = "./pages/login.html";
        return;
    }

    const params = new URLSearchParams(window.location.search);
    editId = params.get('id');

    if (editId) {
        document.querySelector('h1').textContent = 'Editar Publicação';
        document.querySelector('button[type="submit"]').textContent = 'Salvar Alterações';
        try {
            const res = await fetch(`${API_BASE}/api/livros/${editId}`);
            if (res.ok) {
                const data = await res.json();
                const livro = data.livro;
                const form = document.getElementById('formPublicar');
                form.titulo.value = livro.titulo;
                form.categoria_id.value = livro.categoria_id;
                form.descricao.value = livro.descricao;
            }
        } catch(e) {
            console.error(e);
        }
    }
});

const formPublicar = document.getElementById('formPublicar');
if (formPublicar) {
    formPublicar.action = `${API_BASE}/api/publicar`;
    formPublicar.method = 'POST';
}

configurarFormulario({
    formId: "formPublicar",
    validar: (form) => {
        if (!form.titulo.value.trim()) {
            mostrarMensagem("Título é obrigatório!", "erro");
            return false;
        }
        if (!form.categoria_id.value) {
            mostrarMensagem("Categoria é obrigatória!", "erro");
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
        const url = editId ? `${API_BASE}/api/livros/${editId}` : `${API_BASE}/api/publicar`;
        // Para upload de arquivos via PUT, o FormData não funciona bem. O PHP precisa ler php://input e isso é complexo. 
        // Portanto, usaremos POST e adicionaremos _method=PUT para simular.
        if (editId) {
            formData.append('_method', 'PUT');
        }

        try {
            const response = await fetch(editId ? `${API_BASE}/api/atualizar-livro/${editId}` : url, {
                method: "POST", // Vamos usar POST no fetch e no router vamos tratar a rota de atualizar com POST com imagem
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            const resultado = await response.json();

            if (response.ok) {
                mostrarMensagem(editId ? "Livro atualizado com sucesso!" : "Livro publicado com sucesso!", "sucesso");
                setTimeout(() => {
                    window.location.href = "./pages/area_usuario.html";
                }, 1500);
            } else {
                mostrarMensagem(resultado.erro || "Erro ao salvar livro", "erro");
            }
        } catch (erro) {
            console.error(erro);
            mostrarMensagem("Erro de conexão com o servidor", "erro");
        }
    },
    sucessoMsg: ""
});