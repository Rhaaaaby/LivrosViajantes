export default function Footer() {
    return(
        <footer>
          <h2> Mapa do Site</h2>
          <nav>
            <a href="/"> Home</a>
            <a href="/"> Pesquisar</a>
            <a href="/"> Nova Publicação </a>
            <a href="/"> Área do Usuário </a>
            <a href="/"> Mensagens </a>
          </nav>
          © {new Date().getFullYear()} Livros Viajantes — Incentivando a cultura através da tecnologia 🌍
        </footer>
    );
}