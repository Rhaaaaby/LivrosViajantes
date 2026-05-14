const cards = document.querySelectorAll(".card");
const blocos = document.querySelectorAll(".bloco");

cards.forEach(card => {
  card.addEventListener("click", () => {
    const alvo = card.dataset.target;

    blocos.forEach(bloco => {
      bloco.classList.add("oculto");
    });

    const secao = document.getElementById(alvo);
    if (secao) {
      secao.classList.remove("oculto");
      secao.scrollIntoView({ behavior: "smooth" });
    }
  });
});

// Carregar dados do perfil ao carregar a página
document.addEventListener("DOMContentLoaded", async () => {
    await carregarPerfil();
    await carregarPublicacoes();
    configurarFormularioEditar();
    configurarBotoes();
});

// Função para carregar perfil do usuário
async function carregarPerfil() {
    const token = localStorage.getItem('token');
    if (!token) {
        mostrarMensagem('Você precisa estar logado para acessar esta página.', 'erro');
        window.location.href = '/livrosViajantes/public/pages/login.html';
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/perfil`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            if (response.status === 401) {
                mostrarMensagem('Sessão expirada. Faça login novamente.', 'erro');
                localStorage.removeItem('token');
                window.location.href = '/livrosViajantes/public/pages/login.html';
                return;
            }
            throw new Error('Erro ao carregar perfil');
        }

        const data = await response.json();
        const usuario = data.usuario;

        // Preencher dados no topo
        document.querySelector('.foto-perfil').src = usuario.foto
            ? `/livrosViajantes/public/${usuario.foto}`
            : '/livrosViajantes/public/assets/img/cabecalho/icone-avatar.svg';
        document.querySelector('.perfil-topo h2').textContent = usuario.nome_usuario;

        // Preencher formulário de edição
        const form = document.querySelector('.editar_perfil');
        form.querySelector('input[type="text"]').value = usuario.nome_usuario;
        form.querySelector('input[type="email"]').value = usuario.email;

    } catch (error) {
        console.error('Erro ao carregar perfil:', error);
        mostrarMensagem('Erro ao carregar perfil.', 'erro');
    }
}

// Função para carregar publicações do usuário
async function carregarPublicacoes() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const response = await fetch(`${API_BASE}/api/meus-livros`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error('Erro ao carregar publicações');
        }

        const data = await response.json();
        const livros = data.meus_livros;

        const container = document.querySelector('#publicacoes');
        const placeholder = container.querySelector('p');

        if (livros.length === 0) {
            placeholder.textContent = 'Você ainda não publicou nada. :(';
            return;
        }

        placeholder.style.display = 'none';

        const lista = document.createElement('div');
        lista.id = 'lista-livros';
        lista.className = 'lista-livros';

        livros.forEach(livro => {
            const item = document.createElement('div');
            item.className = 'book-card'; // Usando a classe padrão

            item.innerHTML = `
                <img src="${livro.imagem ? `/livrosViajantes/public/${livro.imagem}` : '/livrosViajantes/public/assets/img/bookcard/livro-sonho.webp'}" alt="${livro.titulo}" class="capa-livro">
                <div class="info-livro">
                    <h1 class="titulo-livro">${livro.titulo}</h1>
                    <span class="categoria">${livro.categoria_nome || 'Sem categoria'}</span>
                    <p class="descricao-publicacao">${livro.descricao || 'Sem descrição'}</p>
                    <button class="btn-interesse" disabled style="background:#555; cursor:default; border-color:#555;">Sua publicação</button>
                </div>
            `;

            lista.appendChild(item);
        });

        container.appendChild(lista);

    } catch (error) {
        console.error('Erro ao carregar publicações:', error);
        mostrarMensagem('Erro ao carregar publicações.', 'erro');
    }
}

// Configurar formulário de edição de perfil
function configurarFormularioEditar() {
    const form = document.querySelector('.editar_perfil');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const token = localStorage.getItem('token');
        if (!token) {
            mostrarMensagem('Você precisa estar logado.', 'erro');
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(`${API_BASE}/api/atualizar`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                mostrarMensagem(data.mensagem || 'Perfil atualizado com sucesso!', 'sucesso');
                await carregarPerfil(); // Recarregar dados
            } else {
                mostrarMensagem(data.erro || 'Erro ao atualizar perfil.', 'erro');
            }

        } catch (error) {
            console.error('Erro ao atualizar perfil:', error);
            mostrarMensagem('Erro de conexão.', 'erro');
        }
    });
}

// Configurar botões de logout e excluir conta
function configurarBotoes() {
    const btnLogout = document.querySelector('.btn-logout');
    const btnExcluir = document.querySelector('.btn-excluir');

    if (btnLogout) {
        btnLogout.addEventListener('click', () => {
            localStorage.removeItem('token');
            mostrarMensagem('Logout realizado com sucesso.', 'sucesso');
            setTimeout(() => {
                window.location.href = '/livrosViajantes/public/pages/login.html';
            }, 1000);
        });
    }

    if (btnExcluir) {
        btnExcluir.addEventListener('click', async () => {
            if (!confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) {
                return;
            }

            const token = localStorage.getItem('token');
            if (!token) return;

            try {
                const response = await fetch(`${API_BASE}/api/deletar`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    mostrarMensagem(data.mensagem || 'Conta excluída com sucesso.', 'sucesso');
                    localStorage.removeItem('token');
                    setTimeout(() => {
                        window.location.href = '/livrosViajantes/public/pages/login.html';
                    }, 2000);
                } else {
                    mostrarMensagem(data.erro || 'Erro ao excluir conta.', 'erro');
                }

            } catch (error) {
                console.error('Erro ao excluir conta:', error);
                mostrarMensagem('Erro de conexão.', 'erro');
            }
        });
    }
}