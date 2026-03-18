configurarFormulario({
  formId: "formRegistrar",

  validar: (form) => {
    if (!form.nome.value || !form.email.value || !form.senha.value) {
      mostrarMensagem("Preencha todos os campos!", "erro");
      return false;
    }
    return true;
  },

  aoEnviar: async (form) => {

    const usuario = {
      nome: form.nome.value,
      email: form.email.value,
      senha: form.senha.value
    };

    const resposta = await fetch("/livrosViajantes/public/api/usuarios.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(usuario)
    });

    const resultado = await resposta.json();
    console.log(resultado);
  },

  sucessoMsg: "Conta criada com sucesso!"
});