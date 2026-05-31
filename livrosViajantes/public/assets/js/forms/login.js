// login.js

const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const API_BASE = isLocalhost ? '/livrosViajantes/public' : window.location.origin;

// Seleciona o formulário na marra pelo ID
const form = document.getElementById('formLogin');

if (form) {
    form.addEventListener('submit', async (e) => {
        // IMPEDE o navegador de abrir aquela tela preta com o JSON bruto
        e.preventDefault(); 

        // Validação simples direto aqui
        if (!form.email.value || !form.senha.value) {
            mostrarMensagem("Preencha email e senha!", "erro");
            return;
        }

        try {
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
                // Guarda os dados no navegador
                localStorage.setItem("token", resultado.token);
                localStorage.setItem("usuarioLogado", "true");
                localStorage.setItem("usuarioNome", resultado.usuario.nome_usuario);
                localStorage.setItem("usuario_id", resultado.usuario.id);

                mostrarMensagem("Login realizado com sucesso!", "sucesso");

                // Redireciona após 1.5 segundos
                setTimeout(() => {
                    window.location.href = "/pages/index.php"; // mude para index.html se for o caso
                }, 1500);
            } else {
                mostrarMensagem(resultado.erro || "Email ou senha incorretos", "erro");
            }
        } catch (erro) {
            console.error(erro);
            mostrarMensagem("Erro ao fazer login", "erro");
        }
    });
} else {
    console.error("ERRO: O formulário com id 'formLogin' não foi encontrado na página HTML!");
}