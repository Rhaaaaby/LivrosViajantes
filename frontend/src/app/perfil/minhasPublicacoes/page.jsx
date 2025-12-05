import BookCard from "../../../components/BookCard";

export default function AreaUsuario() {
    const minhasPublicacoes = []

    return(
        <div>
            <h2> Seu Perfil </h2>

            <section>
                <h3> Minhas Publicações </h3>

                {minhasPublicacoes.length === 0 && (
                    <p> Você ainda não publicou nada. :(( </p>
                )}

                {minhasPublicacoes.map(pub => (
                    <BookCard 
                        key={pub.id}
                        titulo={pub.titulo}
                        autor={pub.autor}
                        categoria={pub.categoria}
                        descricao={pub.descricao}
                        imagem={pub.imagem}
                    />
                ))}
            </section>
        </div>
    );
}