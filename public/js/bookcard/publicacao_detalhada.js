document.addEventListener("DOMContentLoaded", () => {
  console.log("JS da página de detalhe carregou 🚀");

  const params = new URLSearchParams(window.location.search);
  const id = Number(params.get("id"));

  const container = document.querySelector(".detalhes-container");
  const livros = JSON.parse(localStorage.getItem("livros")) || [];

  if (!livros.length) {
    container.innerHTML = "<p>Nenhuma publicação disponível ainda 📚</p>";
    return;
  }

  if (!id) return;

  const livro = livros.find(l => l.id === id);

  if (!livro) {
    container.innerHTML = "<p>Livro não encontrado 😢</p>";
    return;
  }

  // textos
  document.getElementById("detalhe-titulo").textContent =
    livro.titulo || "Título não encontrado.";

  document.getElementById("detalhe-categoria").textContent =
    livro.categoria || "Categoria não encontrada.";

  document.getElementById("detalhe-descricao").textContent =
    livro.descricao || "Sem descrição.";

  // 📸 carrossel
  const imagens = livro.imagens?.length
    ? livro.imagens
    : ["../img/bookcard/livro-sonho.webp"];

  let indiceAtual = 0;
  const img = document.getElementById("detalhe-capa");

  img.src = imagens[indiceAtual];

  document.getElementById("next").addEventListener("click", () => {
    indiceAtual = (indiceAtual + 1) % imagens.length;
    img.src = imagens[indiceAtual];
  });

  document.getElementById("prev").addEventListener("click", () => {
    indiceAtual = (indiceAtual - 1 + imagens.length) % imagens.length;
    img.src = imagens[indiceAtual];
  });
});