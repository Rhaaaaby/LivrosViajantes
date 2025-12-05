export default function BookCard() {
    return(
        <div className="book-card">
            <img 
                src={imagem || "/book-default.png"}
                alt={"Capa do livro ${titulo}"}
            />

            <div className="book-info">
                <h3>{titulo} </h3>
                <categoria>{categoria}</categoria>
                <p>{descricao}</p>
            </div>
        </div>
    )
}