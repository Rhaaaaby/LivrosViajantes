export default function Publicar() {
    return (
        <section>
            <form>
                <h1> Nova Publicação </h1>

                <label> Título </label>
                <input 
                    type="title"
                    placeholder="Digite o título da publicação"
                />

                <label> Fazer upload de Arquivos</label>
                <input 
                type="file"
                />

                <label> Categorias </label>
                <input
                type="radio"
                placeholder="Selecione: Empréstimo, Troca, Doação" 
                />
                
                <label> Descrição </label>
                <input 
                type="text"
                placeholder="Escreva o conteúdo aqui"/>

                <button type="submit">
                    Publicar
                </button>

            </form>
        </section>
    )
}