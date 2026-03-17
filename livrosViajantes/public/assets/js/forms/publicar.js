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
      titulo: form.titulo.value,
      categoria_id: parseInt(form.categoria.value),
      descricao: form.descricao.value,
      autor_id: 1 // temporário até ter sistema de autor
    };

    const resposta = await fetch("/livrosViajantes/public/api/livros.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(livro)
    });

    //trocando temporariamente para conserto de bugs entre API - Banco
    //const resultado = await resposta.json();
    //console.log(resultado);

    const texto = await resposta.text();
    console.log(texto);
  },

  sucessoMsg: "Publicação criada com sucesso!"
});