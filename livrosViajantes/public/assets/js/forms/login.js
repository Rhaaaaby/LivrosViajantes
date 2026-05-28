// login.js

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
                localStorage.setItem("usuario_id", resultado.usuario.id);

                mostrarMensagem("Login realizado com sucesso!", "sucesso");

                setTimeout(() => {
                    window.location.href = "./pages/index.php";
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