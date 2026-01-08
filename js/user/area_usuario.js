const cards = document.querySelectorAll(".card");
const blocos = document.querySelectorAll(".bloco");

cards.forEach(card => {
  card.addEventListener("click", () => {
    const alvo = card.dataset.target;

    blocos.forEach(bloco => {
      bloco.classList.add("oculto");
    });

    const secao = document.getElementById(alvo);
    if (secao) {
      secao.classList.remove("oculto");
      secao.scrollIntoView({ behavior: "smooth" });
    }
  });
});