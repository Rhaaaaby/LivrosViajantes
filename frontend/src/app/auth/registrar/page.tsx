export default function Registrar() {
    return (
        <section>
            <h2> Bem Vindo :D! </h2>

            <span> Vamos Começar? </span>

            <form>
                <label> Título </label>
                <input
                    type="email"
                    placeholder="Digite o sei melhor e-mail"
                />

                <label> Nome </label>
                <input 
                    type="nome"
                    placeholder="Vamos criar seu nome de usuário!"
                />

                <label> Senha </label>
                <input
                    type="password"
                />

                <button type="submit">
                    Criar Conta
                </button>
            </form>
        </section>
    );
}