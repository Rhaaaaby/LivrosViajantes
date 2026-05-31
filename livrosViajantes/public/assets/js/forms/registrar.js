// registrar.js
// registrar.js

// Garante que o API_BASE sempre terá o domínio correto, independente de arquivos externos
const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const API_BASE = isLocalhost ? '/livrosViajantes/public' : window.location.origin;

configurarFormulario({
    formId: "formRegistrar",
    validar: (form) => {
        if (!form.nome.value || !form.email.value || !form.senha.value) {
            mostrarMensagem("Preencha todos os campos!", "erro");
            return false;
        }
        if (form.senha.value.length < 8) {
            mostrarMensagem("A senha deve ter no mínimo 8 caracteres", "erro");
            return false;
        }
        return true;
    },
    aoEnviar: async (form) => {
        try {
            // Transformamos o objeto em formato de formulário padrão (chave=valor&chave2=valor2)
            const URLDados = new URLSearchParams();
            URLDados.append('nome_usuario', form.nome.value.trim());
            URLDados.append('email', form.email.value.trim());
            URLDados.append('senha', form.senha.value);

            const response = await fetch(`${API_BASE}/api/cadastrar`, {
                method: "POST",
                headers: { 
                    // Formato tradicional que a Render preserva sem corromper
                    "Content-Type": "application/x-www-form-urlencoded" 
                },
                body: URLDados
            });

            const resultado = await response.json();

            if (response.ok) {
                mostrarMensagem("Conta criada com sucesso! Faça login.", "sucesso");
                setTimeout(() => {
                    window.location.href = "/pages/login.html";
                }, 2000);
            } else {
                mostrarMensagem(resultado.erro || "Erro ao criar conta", "erro");
            }
        } catch (erro) {
            console.error(erro);
            mostrarMensagem("Erro de conexão", "erro");
        }
    }
});