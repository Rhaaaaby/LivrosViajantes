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
    e.stopImmediatePropagation();

    const botao = form.querySelector("button");
    if (botao) {
      botao.classList.add("loading");
    }

    // IF → validação
    if (validar && !validar(form)) {
      if (botao) {
        botao.classList.remove("loading");
      }
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
      if (botao) {
        botao.classList.remove("loading");
      }
    }
  });
}