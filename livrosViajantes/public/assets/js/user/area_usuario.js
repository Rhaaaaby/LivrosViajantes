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
        window.location.href = './pages/login.html';
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
                window.location.href = './pages/login.html';
                return;
            }
            throw new Error('Erro ao carregar perfil');
        }

        const data = await response.json();
        const usuario = data.usuario;

        // Preencher dados no topo
        document.querySelector('.foto-perfil').src = usuario.foto
            ? `./${usuario.foto}`
            : './assets/img/cabecalho/icone-avatar.svg';
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
            placeholder.style.display = 'block';
            placeholder.textContent = 'Você ainda não publicou nada. :(';
            return;
        }

        placeholder.style.display = 'none';

        let lista = document.getElementById('lista-livros');
        if (!lista) {
            lista = document.createElement('div');
            lista.id = 'lista-livros';
            lista.className = 'lista-livros';
            container.appendChild(lista);
        } else {
            lista.innerHTML = '';
        }

        livros.forEach(livro => {
            const item = document.createElement('div');
            item.className = 'book-card';

            item.innerHTML = `
                <img src="${livro.imagem ? `./${livro.imagem}` : './assets/img/bookcard/livro-sonho.webp'}" alt="${livro.titulo}" class="capa-livro">
                <div class="info-livro">
                    <h1 class="titulo-livro">${livro.titulo}</h1>
                    <span class="categoria">${livro.categoria_nome || 'Sem categoria'}</span>
                    <p class="descricao-publicacao">${livro.descricao || 'Sem descrição'}</p>
                    <div style="display:flex; gap:10px; margin-top:10px;">
                        <button class="btn-editar" data-id="${livro.id}" style="padding: 5px; border-radius: 5px; cursor: pointer; flex: 1;">Editar</button>
                        <button class="btn-excluir-livro" data-id="${livro.id}" style="padding: 5px; border-radius: 5px; background: red; color: white; cursor: pointer; flex: 1;">Excluir</button>
                    </div>
                </div>
            `;

            lista.appendChild(item);
        });

        // Configurar botões de edição e exclusão
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', () => {
                window.location.href = `./pages/publicar.html?id=${btn.dataset.id}`;
            });
        });

        document.querySelectorAll('.btn-excluir-livro').forEach(btn => {
            btn.addEventListener('click', async () => {
                if(confirm('Tem certeza que deseja excluir esta publicação?')) {
                    try {
                        const res = await fetch(`${API_BASE}/api/livros/${btn.dataset.id}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (res.ok) {
                            mostrarMensagem('Livro excluído com sucesso!', 'sucesso');
                            carregarPublicacoes();
                        }
                    } catch (error) {
                        mostrarMensagem('Erro ao excluir', 'erro');
                    }
                }
            });
        });

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
                window.location.href = './pages/login.html';
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
                        window.location.href = './pages/login.html';
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

// Função para carregar a lista de usuários que sigo
async function carregarSeguindo() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const response = await fetch(`${API_BASE}/api/seguidores`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const container = document.getElementById('lista-seguindo');
        if (!container) return;

        if (response.ok) {
            const data = await response.json();
            const seguindo = data.seguindo;
            if (seguindo.length === 0) {
                container.innerHTML = '<p>Você não segue ninguém ainda.</p>';
            } else {
                container.innerHTML = '';
                seguindo.forEach(u => {
                    container.innerHTML += `
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px; padding:10px; border:1px solid #ccc; border-radius:8px;">
                            <img src="${u.foto ? './'+u.foto : './assets/img/cabecalho/icone-avatar.svg'}" style="width:50px; height:50px; border-radius:50%; object-fit:cover;">
                            <a href="./pages/perfil_publico.html?id=${u.id}" style="text-decoration:none; color:black; font-weight:bold;">${u.nome_usuario}</a>
                        </div>
                    `;
                });
            }
        } else {
            container.innerHTML = '<p>Erro ao carregar lista.</p>';
        }
    } catch (e) {
        console.error(e);
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    carregarSeguindo();
});