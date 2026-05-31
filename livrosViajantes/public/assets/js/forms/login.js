// login.js

// 1. Torna a busca da API autossuficiente (corrige o problema do api-config.js ausente)
const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const API_BASE = isLocalhost ? '/livrosViajantes/public' : window.location.origin;

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
        try {
            // 2. Transforma em formato de formulário tradicional que a Render aceita nativamente
            const URLDados = new URLSearchParams();
            URLDados.append('email', form.email.value.trim());
            URLDados.append('senha', form.senha.value);

            const response = await fetch(`${API_BASE}/api/login`, {
                method: "POST",
                headers: { 
                    "Content-Type": "application/x-www-form-urlencoded" 
                },
                body: URLDados
            });

            const resultado = await response.json();

            if (response.ok) {
                // 3. Preserva todas as suas gravações cruciais de estado de login
                localStorage.setItem("token", resultado.token);
                localStorage.setItem("usuarioLogado", "true");
                localStorage.setItem("usuarioNome", resultado.usuario.nome_usuario);
                localStorage.setItem("usuario_id", resultado.usuario.id);

                mostrarMensagem("Login realizado com sucesso!", "sucesso");

                setTimeout(() => {
                    window.location.href = "/pages/index.php";
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