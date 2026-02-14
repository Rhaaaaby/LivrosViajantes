async function carregar(id, arquivo) {

  const BASE_COMPONENTS = "/livrosViajantes/public/pages/components/";

  try {
    const res = await fetch(`${BASE_COMPONENTS}${arquivo}`);

    if (!res.ok) {
      throw new Error(`Erro ao carregar ${arquivo}: ${res.status}`);
    }

    const conteudo = await res.text();

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