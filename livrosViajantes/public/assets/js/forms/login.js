// login.js

let API_BASE = '';
if (window.location.pathname.includes('/pages/')) {
    API_BASE = window.location.pathname.split('/pages/')[0];
}
API_BASE = API_BASE.replace(/\/$/, '');

const formLogin = document.getElementById('formLogin');
if (formLogin) {
    formLogin.action = `${API_BASE}/api/login`;
    formLogin.method = 'POST';
}

configurarFormulario({
    formId: "formLogin",
    validar: (form) => {
        if (!form.email.value || !form.senha.value) {
            mostrarMensagem("Preencha email e senha!", "erro");
            return false;
        }
        return true;
    },
    aoEnviar: async (form) => {
        const usuario = {
            email: form.email.value.trim(),
            senha: form.senha.value
        };

        try {
            const response = await fetch(`${API_BASE}/api/login`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(usuario)
            });

            const resultado = await response.json();

            if (response.ok) {
                // Salva o token
                localStorage.setItem("token", resultado.token);
                localStorage.setItem("usuarioLogado", "true");
                localStorage.setItem("usuarioNome", resultado.usuario.nome_usuario);

                mostrarMensagem("Login realizado com sucesso!", "sucesso");

                setTimeout(() => {
                    window.location.href = "/livrosViajantes/public/pages/index.php";
                }, 1500);
            } else {
                mostrarMensagem(resultado.erro || "Email ou senha incorretos", "erro");
            }
        } catch (erro) {
            console.error(erro);
            mostrarMensagem("Erro ao fazer login", "erro");
        }
    }
});