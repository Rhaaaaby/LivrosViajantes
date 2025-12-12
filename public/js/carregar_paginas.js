async function carregar(id, arquivo) {
  const conteudo = await fetch(`pages/${arquivo}`).then(res => res.text());
  document.getElementById(id).innerHTML = conteudo;
}

carregar("header", "header.html");
carregar("footer", "footer.html");
carregar("tab_menu", "tab_menu.html")