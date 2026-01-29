const btnMenu = document.querySelector(".btn-menu");
const gaveta = document.querySelector(".chat-gaveta");

btnMenu.addEventListener("click", () => {
  gaveta.classList.toggle("aberta");
});