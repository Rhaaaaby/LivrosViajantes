export default function RegistrarUser() {
    return (
        <section>
            <h2> Bem vindo de volta :)! </h2>

            <span> Login </span>

            <form>
                <input
                    type="email"
                    placeholder="Digite o sei melhor e-mail"
                />

                <input 
                    type="password"
                />

                <button type="submit">
                    Entrar
                </button>
            </form>
        </section>
    );
}