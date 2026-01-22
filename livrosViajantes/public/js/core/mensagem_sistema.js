function mostrarMensagem(texto, tipo ="sucesso") {
    const msg = document.createElement("div");
    msg.className = `msg $ {tipo}`;
    msg.innerText = texto;

    document.body.appendChild(msg);

    setTimeout(() => msg.remove(), 3000);
}