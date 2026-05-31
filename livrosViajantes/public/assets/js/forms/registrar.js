// registrar.js

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
        const usuario = {
            nome_usuario: form.nome.value.trim(),
            email: form.email.value.trim(),
            senha: form.senha.value
        };

        try {
            const response = await fetch(`${API_BASE}/api/cadastrar`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(usuario)
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