configurarFormulario({
  formId: "formPublicar",

  validar: (form) => {
    if (!form.titulo.value.trim()) {
      mostrarMensagem("Título é obrigatório!", "erro");
      return false;
    }
    return true;
  },

  aoEnviar: async (form) => {
    const livro = {
      id: Date.now(),
      titulo: form.titulo.value,
      categoria: form.categoria.value,
      descricao: form.descricao.value
    };

    let livros = JSON.parse(localStorage.getItem("livros")) || [];
    livros.push(livro);
    localStorage.setItem("livros", JSON.stringify(livros));
  },

  sucessoMsg: "Publicação criada com sucesso!"
});