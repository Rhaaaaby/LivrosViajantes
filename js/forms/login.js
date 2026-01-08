configurarFormulario({
  formId: "formLogin",

  validar: (form) => {
    if (!form.email.value || !form.senha.value) {
      mostrarMensagem("Preencha todos os campos!", "erro");
      return false;
    }
    return true;
  },

  aoEnviar: async () => {
    // simulação, colocar PHP depois
    await new Promise(r => setTimeout(r, 800));
    localStorage.setItem("usuarioLogado", "true");
  },

  sucessoMsg: "Login realizado!"
});