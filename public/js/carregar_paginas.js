async function carregar(id, arquivo) {
  const conteudo = await fetch(`html/${arquivo}`).then(res => res.text());
  document.getElementById(id).innerHTML = conteudo;
}

carregar("header", "header.html");
carregar("menu", "menu.html");
carregar("footer", "footer.html");