export default function RegistrarLayout({ children }) {
    return (
        <html lang="pt-BR">
            <body>
                <header>
                    <img src="/logo-LV-removebg-preview.png" alt="logo Livros Viajantes"></img>

                    <h1> Livros Viajantes </h1>
                </header>

                <section> {children} </section>
            </body>

        </html>
    )
}