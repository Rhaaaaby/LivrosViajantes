async function carregar(id, arquivo) {

  const BASE_COMPONENTS = "/livrosViajantes/public/pages/components/";

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
carregar("cabecalho", "cabecalho.html");