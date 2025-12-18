async function carregar(id, arquivo) {
  try {

    //a variável é chamada para evitar escrever os caminhos absolutos das páginas
    //e diminuir os erros de path
    const res = await fetch(`${BASE_COMPONENTS}${arquivo}`);

    // try/catch para garantir que as páginas foram carregadas ou
    // enviar mensagem se der erro
    if (!res.ok) {
      throw new Error(`Erro ao carregar ${arquivo}: ${res.status}`);
    }

    //chamando o conteúdo
    const conteudo = await res.text();

    //chamando apenas a página específica no HTML com container
    const container = document.getElementById(id);
    if (!container) return;

    container.innerHTML = conteudo;

  } catch (erro) {
    console.error(erro);
  }
}

carregar("header", "header.html");
carregar("footer", "footer.html");
carregar("tab_menu", "tab_menu.html");
carregar("branding", "branding.html");