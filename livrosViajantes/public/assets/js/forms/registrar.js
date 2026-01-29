configurarFormulario({
    formId: "formRegistrar",

    validar: (form) => {
        if(!form.email.value || !form.senha.value) {
            mostrarMensagem("Preencha todos os campos!", "erro");
            return false;
        }

        return true;
    },

    aoEnviar: async () => {
        await new Promise(r => setTimeout(r, 800));
        localStorage.setItem("usuarioregistrado", "true");
    },

    sucessoMsg: "Usuário cadastrado com sucesso!"
})