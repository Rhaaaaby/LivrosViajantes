console.log("🔥 BASE FORMS NOVO 🔥");

function configurarFormulario({
  formId,
  validar,
  aoEnviar,
  sucessoMsg
}) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const botao = form.querySelector("button");
    botao.classList.add("loading");

    // IF → validação
    if (validar && !validar(form)) {
      botao.classList.remove("loading");
      return;
    }

    try {
      // ação principal (fetch / localStorage / PHP)
      console.log("ANTES DO ENVIO");
      await aoEnviar(form);
      console.log("DEPOIS DO ENVIO");

      mostrarMensagem(sucessoMsg || "Sucesso!", "sucesso");
      form.reset();

    } catch (erro) {
      
      console.error(erro);
      mostrarMensagem("Algo deu errado 😢", "erro");

    } finally {
      botao.classList.remove("loading");
    }
  });
}