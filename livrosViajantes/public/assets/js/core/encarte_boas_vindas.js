/**
 * encarte_boas_vindas.js
 * Exibe um modal de boas-vindas apenas no primeiro acesso.
 * Usa localStorage para não exibir novamente após o primeiro fechamento.
 */

(function () {
    const CHAVE_STORAGE = 'lv_boas_vindas_visto';

    // Se o usuário já viu o encarte, não exibe novamente
    if (localStorage.getItem(CHAVE_STORAGE)) return;

    // ----------------------------------------------------------------
    // Criação da estrutura HTML do encarte
    // ----------------------------------------------------------------
    function criarEncarte() {
        const overlay = document.createElement('div');
        overlay.id = 'encarte-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'encarte-titulo');

        overlay.innerHTML = `
            <div id="encarte-card">

                <!-- Faixa do topo com logo -->
                <div id="encarte-topo">
                    <button id="btn-encarte-fechar" aria-label="Fechar boas-vindas">✕</button>
                    <img
                        id="encarte-logo"
                        src="/livrosViajantes/public/assets/img/logo.png"
                        alt="Logo Livros Viajantes"
                    />
                    <h1 id="encarte-titulo">Livros Viajantes</h1>
                    <p id="encarte-subtitulo">
                        Incentivando a cultura através da tecnologia! ✈️
                    </p>
                </div>

                <!-- Corpo com features e ações -->
                <div id="encarte-corpo">

                    <div class="encarte-features">
                        <div class="encarte-feature">
                            <span class="encarte-feature-icon">📚</span>
                            <div class="encarte-feature-texto">
                                <strong>Compartilhe seus livros</strong>
                                Publique obras que você já leu e quer ver viajar pelo mundo.
                            </div>
                        </div>
                        <div class="encarte-feature">
                            <span class="encarte-feature-icon">🔍</span>
                            <div class="encarte-feature-texto">
                                <strong>Descubra novas leituras</strong>
                                Encontre livros próximos e solicite um exemplar.
                            </div>
                        </div>
                        <div class="encarte-feature">
                            <span class="encarte-feature-icon">💬</span>
                            <div class="encarte-feature-texto">
                                <strong>Conecte-se com leitores</strong>
                                Troque mensagens e combine a entrega do seu próximo livro.
                            </div>
                        </div>
                    </div>

                    <hr class="encarte-divisor" />

                    <div class="encarte-acoes">
                        <a
                            id="btn-encarte-criar"
                            href="/livrosViajantes/public/pages/registrar.html"
                        >
                            Criar minha conta 🚀
                        </a>
                        <a id="btn-encarte-pular" href="/livrosViajantes/public/pages/sobre.html">
                            Leia sobre o projeto ❤️
                        </a>
                    </div>

                    <p id="encarte-rodape">
                        Já tem uma conta?
                        <a href="/livrosViajantes/public/pages/login.html">Entrar</a>
                    </p>
                </div>

            </div>
        `;

        return overlay;
    }

    // ----------------------------------------------------------------
    // Lógica de fechamento
    // ----------------------------------------------------------------
    function fecharEncarte(overlay) {
        overlay.classList.add('saindo');
        // Aguarda a animação de saída antes de remover do DOM
        overlay.addEventListener('animationend', () => {
            overlay.remove();
            // Re-habilita scroll
            document.body.style.overflow = '';
        }, { once: true });

        // Marca no localStorage para não exibir mais
        localStorage.setItem(CHAVE_STORAGE, '1');
    }

    // ----------------------------------------------------------------
    // Montagem e eventos
    // ----------------------------------------------------------------
    function montar() {
        const overlay = criarEncarte();
        document.body.appendChild(overlay);

        // Bloqueia scroll enquanto o modal está aberto
        document.body.style.overflow = 'hidden';

        // Botão ✕
        overlay.querySelector('#btn-encarte-fechar')
            .addEventListener('click', () => fecharEncarte(overlay));

        // Botão "pular"
        overlay.querySelector('#btn-encarte-pular')
            .addEventListener('click', () => fecharEncarte(overlay));

        // Clique fora do card (no overlay)
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) fecharEncarte(overlay);
        });

        // Tecla Escape
        document.addEventListener('keydown', function onEsc(e) {
            if (e.key === 'Escape') {
                fecharEncarte(overlay);
                document.removeEventListener('keydown', onEsc);
            }
        });

        // Ao clicar em "Criar conta" também registra no storage
        overlay.querySelector('#btn-encarte-criar')
            .addEventListener('click', () => {
                localStorage.setItem(CHAVE_STORAGE, '1');
            });
    }

    // Aguarda o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', montar);
    } else {
        montar();
    }
})();
