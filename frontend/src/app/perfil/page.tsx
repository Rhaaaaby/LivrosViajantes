export default function AreaUser() {
    const user = {
        photoUrl: null, //por enqunato deixar null ja que nao ha dados
        name: "Usuário Exemplo",
    }
    
    return(
        <div>

            <section>
                <img
                    src={user.photoUrl || "/user-default.png"}
                    alt="Foto do Usuário"
                />

                <nav> 
                    <a href="/perfil/minhasPublicacoes"> Minhas Publicações </a>

                    <a href="/perfil/configuracoes"> Configurações  </a>
                </nav>
                
            </section>
        </div>
    );
}