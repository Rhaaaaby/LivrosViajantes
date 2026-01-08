//documento exclusivo pro botão "tenho interesse" dos bookcards

document.addEventListener("click", function (e) {
  if (e.target.classList.contains("btn-interesse")) {
    const idLivro = e.target.dataset.id;

    console.log("Interesse no livro:", idLivro);

    // logica futura
  }
});
