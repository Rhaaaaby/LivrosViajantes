async function carregar(id, arquivo) {

  const BASE_COMPONENTS = "/pages/components/";

  try {
    const res = await fetch(`${BASE_COMPONENTS}${arquivo}`);

    if (!res.ok) {
      throw new Error(`Erro ao carregar ${arquivo}: ${res.status}`);
    }

    const conteudo = await res.text();

    const container = document.getElementById(id);
    if (!container) return;

    container.innerHTML = conteudo;

    if (id === 'footer') {
        initFooterRating();
    }

  } catch (erro) {
    console.error(erro);
  }
}

//avaliações do site
function initFooterRating() {
    const estrelas = document.querySelectorAll('.estrelas span');
    const inputEstrelas = document.getElementById('avaliacaoEstrelas');
    const formAvaliacao = document.getElementById('formAvaliacaoSite');
    const msgAvaliacao = document.getElementById('msgAvaliacao');

    if (!estrelas.length || !formAvaliacao) return;

    estrelas.forEach(estrela => {
        estrela.addEventListener('click', function() {
            const valor = this.getAttribute('data-valor');
            inputEstrelas.value = valor;
            
            // Atualizar UI
            estrelas.forEach(s => {
                if (s.getAttribute('data-valor') <= valor) {
                    s.style.color = 'gold';
                } else {
                    s.style.color = '#ccc';
                }
            });
        });
    });

    formAvaliacao.addEventListener('submit', async function(e) {
        e.preventDefault();
        const token = localStorage.getItem('token');
        if (!token) {
            msgAvaliacao.textContent = 'Você precisa estar logado para avaliar.';
            msgAvaliacao.style.color = 'red';
            msgAvaliacao.style.display = 'block';
            return;
        }

        if (inputEstrelas.value == 0) {
            msgAvaliacao.textContent = 'Por favor, selecione uma nota de 1 a 5 estrelas.';
            msgAvaliacao.style.color = 'red';
            msgAvaliacao.style.display = 'block';
            return;
        }

        const formData = new FormData(formAvaliacao);
        try {
            const res = await fetch(`${API_BASE}/api/avaliacoes`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                body: formData
            });
            const data = await res.json();
            
            msgAvaliacao.style.display = 'block';
            if (res.ok) {
                msgAvaliacao.textContent = data.mensagem || 'Obrigado pela sua avaliação!';
                msgAvaliacao.style.color = 'green';
                formAvaliacao.reset();
                inputEstrelas.value = 0;
                estrelas.forEach(s => s.style.color = '#ccc');
            } else {
                msgAvaliacao.textContent = data.erro || 'Erro ao enviar avaliação.';
                msgAvaliacao.style.color = 'red';
            }
        } catch (e) {
            console.error(e);
        }
    });
}

carregar("header", "header.html");
carregar("footer", "footer.html");
carregar("tab_menu", "tab_menu.html");
carregar("branding", "branding.html");
carregar("lista-livros", "lista_livros.html");
carregar("cabecalho", "cabecalho.html").then(() => {
    verificarNotificacoesMensagens();
    setInterval(verificarNotificacoesMensagens, 10000); // Poll a cada 10s
});

async function verificarNotificacoesMensagens() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const res = await fetch(`${API_BASE}/api/mensagens/notificacoes`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.ok) {
            const data = await res.json();
            const unreadCount = data.mensagens ? data.mensagens.length : 0;
            
            // Procura o link de mensagens no cabeçalho
            const links = document.querySelectorAll('.links-btn');
            let msgLink = null;
            links.forEach(link => {
                if (link.getAttribute('href') && link.getAttribute('href').includes('mensagem.html')) {
                    msgLink = link;
                }
            });

            if (msgLink) {
                let badge = msgLink.querySelector('.msg-badge');
                if (unreadCount > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'msg-badge';
                        badge.style.cssText = 'background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; margin-left: 5px; vertical-align: top;';
                        msgLink.appendChild(badge);
                    }
                    badge.textContent = unreadCount;
                } else if (badge) {
                    badge.remove();
                }
            }
        }
    } catch (e) {
        console.error('Erro ao buscar notificações de mensagens', e);
    }
}