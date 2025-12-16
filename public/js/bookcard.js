//função para salvar livros no localStorage

function salvarLivro(livro) {
    let livros = JSON.parse(localStorage.getItem("livros")) || [];
    livros.push(livro);
    localStorage.setItem("livros", JSON.stringify(livros));
}


//função para criar os cards de livros (publicação)
function criarCard(livro) {
    const card = document.createElement("div");
    card.classList.add("book-card");

    card.innerHTML = `
        <h3 class="titulo-livro">${livro.titulo}</h3>
        <img src="${livro.arquivo}" alt="capa do livro" class="capa-livro">
        <span class="categoria">${livro.categoria}</span>
        <p class="descricao-publicacao">${livro.descricao}</p>
    `;

    document.querySelector("#lista-livros").appendChild(card);
}

//função para renderizar as publicações na pagina HTML

function carregarLivros() {
    const livros = JSON.parse(localStorage.getItem("livros")) || [];
    livros.forEach(livro => criarCard(livro));
}

//recebendo e salvando os cards que serão criados

function iniciarFormularioLivro() {
    const form = document.getElementById("formLivro");

    if (!form) {
        console.log("form ainda não existe");
        return;
    }

    console.log("form encontrado");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const livro = {
            id: Date.now(),
            titulo: document.getElementById("titulo").value,
            categoria: document.getElementById("categoria").value,
            descricao: document.getElementById("descricao").value
        };

        salvarLivro(livro);
        criarCard(livro);
        form.reset();
    });
}

document.addEventListener("DOMContentLoaded", () => {
    carregarLivros();
    iniciarFormularioLivro();
});