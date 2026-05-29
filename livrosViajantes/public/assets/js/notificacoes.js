// notificacoes.js

function formatoData(dataString) {
    try {
        const data = new Date(dataString);
        return data.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
    } catch {
        return dataString;
    }
}

function criarNotificacaoItem(item, tipo, isRecebida) {
    const li = document.createElement('li');
    li.className = 'notificacao-item';

    const icone = document.createElement('span');
    icone.className = 'icone';
    icone.textContent = isRecebida ? '📚' : '✉️';

    const conteudo = document.createElement('div');
    conteudo.className = 'conteudo';

    const mensagem = document.createElement('p');
    mensagem.className = 'mensagem';

    if (isRecebida) {
        mensagem.innerHTML = `<strong>${item.solicitante_nome}</strong> demonstrou interesse em <strong>${item.livro_titulo}</strong>.`;
    } else {
        mensagem.innerHTML = `Você demonstrou interesse em <strong>${item.livro_titulo}</strong> do dono <strong>${item.dono_nome}</strong>.`;
    }

    const tempo = document.createElement('span');
    tempo.className = 'tempo';
    tempo.textContent = formatoData(item.criada_em || item.criado_em || '');

    conteudo.appendChild(mensagem);
    conteudo.appendChild(tempo);

    li.appendChild(icone);
    li.appendChild(conteudo);

    if (isRecebida && item.status === 'pendente') {
        const acoes = document.createElement('div');
        acoes.className = 'acoes-solicitacao';

        const aceitar = document.createElement('button');
        aceitar.className = 'btn-acao btn-aceitar';
        aceitar.textContent = 'Aceitar';
        aceitar.addEventListener('click', () => responderSolicitacao(item.id, 'aceitar', li));

        const recusar = document.createElement('button');
        recusar.className = 'btn-acao btn-recusar';
        recusar.textContent = 'Recusar';
        recusar.addEventListener('click', () => responderSolicitacao(item.id, 'recusar', li));

        acoes.appendChild(aceitar);
        acoes.appendChild(recusar);
        li.appendChild(acoes);
    } else if (!isRecebida && item.status === 'pendente') {
        // Botão de cancelar para solicitações enviadas pendentes
        const acoes = document.createElement('div');
        acoes.className = 'acoes-solicitacao';

        const cancelar = document.createElement('button');
        cancelar.className = 'btn-acao btn-cancelar';
        cancelar.textContent = 'Cancelar';
        cancelar.addEventListener('click', () => cancelarSolicitacao(item.id, li));

        acoes.appendChild(cancelar);
        li.appendChild(acoes);
    } else if (item.status) {
        const statusBadge = document.createElement('div');
        statusBadge.className = `status-badge status-${item.status}`;
        statusBadge.textContent = item.status === 'pendente' ? 'Pendente' : item.status === 'aceita' ? 'Aceita' : 'Recusada';
        li.appendChild(statusBadge);
    }

    return li;
}

function criarNotificacaoMensagem(item) {
    const li = document.createElement('li');
    li.className = 'notificacao-item notificacao-mensagem-item';

    const icone = document.createElement('span');
    icone.className = 'icone';
    icone.textContent = 'MSG';

    const conteudo = document.createElement('div');
    conteudo.className = 'conteudo';

    const mensagem = document.createElement('p');
    mensagem.className = 'mensagem';

    const remetente = document.createElement('strong');
    remetente.textContent = item.remetente_nome || `Usuario ${item.remetente_id}`;

    mensagem.appendChild(remetente);
    mensagem.appendChild(document.createTextNode(` enviou: ${item.conteudo || ''}`));

    const tempo = document.createElement('span');
    tempo.className = 'tempo';
    tempo.textContent = formatoData(item.criado_em || item.enviada_em || '');

    conteudo.appendChild(mensagem);
    conteudo.appendChild(tempo);

    const acoes = document.createElement('div');
    acoes.className = 'acoes-solicitacao';

    const abrirChat = document.createElement('button');
    abrirChat.className = 'btn-acao btn-abrir-chat';
    abrirChat.textContent = 'Abrir chat';
    abrirChat.addEventListener('click', () => {
        window.location.href = `/pages/mensagem.html?destino=${item.remetente_id}`;
    });

    acoes.appendChild(abrirChat);

    li.appendChild(icone);
    li.appendChild(conteudo);
    li.appendChild(acoes);

    li.addEventListener('click', (event) => {
        if (event.target.closest('button')) return;
        window.location.href = `/pages/mensagem.html?destino=${item.remetente_id}`;
    });

    return li;
}

function mostrarErro(texto) {
    const container = document.getElementById('notificacoes-mensagem');
    if (!container) return;
    container.textContent = texto;
    container.className = 'notificacoes-erro';
}

function limparLista(lista) {
    if (!lista) return;
    lista.innerHTML = '';
}

async function carregarSolicitacoes(endpoint, lista, isRecebida) {
    const token = localStorage.getItem('token');
    const vazio = document.getElementById('notificacoes-vazio');

    if (!token) {
        mostrarErro('Você precisa estar logado para ver suas solicitações.');
        if (vazio) vazio.style.display = 'block';
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/solicitacoes/${endpoint}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (!response.ok || data.sucesso === false) {
            mostrarErro(data.erro || data.mensagem || 'Erro ao carregar solicitações');
            return;
        }

        const itens = Array.isArray(data.dados) ? data.dados : [];
        limparLista(lista);

        if (itens.length === 0) {
            const vazioItem = document.createElement('li');
            vazioItem.className = 'notificacao-vazia-item';
            vazioItem.textContent = isRecebida ? 'Nenhuma solicitação recebida no momento.' : 'Nenhuma solicitação enviada ainda.';
            lista.appendChild(vazioItem);
            return;
        }

        itens.forEach(item => lista.appendChild(criarNotificacaoItem(item, endpoint, isRecebida)));

    } catch (error) {
        mostrarErro('Erro de conexão ao carregar solicitações.');
        console.error(error);
    }
}

async function carregarMensagensRecebidas(lista) {
    const token = localStorage.getItem('token');
    const vazio = document.getElementById('notificacoes-vazio');

    if (!token) {
        mostrarErro('Voce precisa estar logado para ver suas notificacoes.');
        if (vazio) vazio.style.display = 'block';
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/mensagens/notificacoes`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (!response.ok || data.sucesso === false) {
            mostrarErro(data.erro || data.mensagem || 'Erro ao carregar mensagens recebidas');
            return;
        }

        const itens = Array.isArray(data.dados?.mensagens) ? data.dados.mensagens : [];
        limparLista(lista);

        if (itens.length === 0) {
            const vazioItem = document.createElement('li');
            vazioItem.className = 'notificacao-vazia-item';
            vazioItem.textContent = 'Nenhuma mensagem recebida ainda.';
            lista.appendChild(vazioItem);
            return;
        }

        itens.forEach(item => lista.appendChild(criarNotificacaoMensagem(item)));
    } catch (error) {
        mostrarErro('Erro de conexao ao carregar mensagens recebidas.');
        console.error(error);
    }
}

async function responderSolicitacao(id, acao, itemElement) {
    const token = localStorage.getItem('token');
    if (!token) {
        mostrarMensagem('Voce precisa estar logado para responder.', 'erro');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/solicitacoes/${id}/responder?acao=${acao}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (response.ok && data.sucesso !== false) {
            mostrarMensagem(data.mensagem || 'Solicitacao respondida com sucesso.', 'sucesso');
            if (itemElement) {
                itemElement.remove();
            }
            return;
        }

        mostrarMensagem(data.erro || data.mensagem || 'Erro ao responder solicitacao.', 'erro');
    } catch (error) {
        mostrarMensagem('Erro de conexao ao responder solicitacao.', 'erro');
        console.error(error);
    }
}

async function cancelarSolicitacao(id, itemElement) {
    const token = localStorage.getItem('token');
    if (!token) {
        mostrarMensagem('Você precisa estar logado para cancelar.', 'erro');
        return;
    }

    if (!confirm('Tem certeza que deseja cancelar esta solicitação?')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/solicitacoes/${id}/cancelar`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (response.ok && data.sucesso !== false) {
            mostrarMensagem(data.mensagem || 'Solicitação cancelada com sucesso.', 'sucesso');
            if (itemElement) {
                itemElement.remove(); // Remove o item da lista
            }
        } else {
            mostrarMensagem(data.erro || data.mensagem || 'Erro ao cancelar solicitação.', 'erro');
        }
    } catch (error) {
        mostrarMensagem('Erro de conexão ao cancelar solicitação.', 'erro');
        console.error(error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const listaMensagens = document.getElementById('lista-mensagens-recebidas');
    const listaRecebidas = document.getElementById('lista-solicitacoes-recebidas');
    const listaEnviadas = document.getElementById('lista-solicitacoes-enviadas');

    if (listaMensagens) carregarMensagensRecebidas(listaMensagens);
    if (listaRecebidas) carregarSolicitacoes('recebidas', listaRecebidas, true);
    if (listaEnviadas) carregarSolicitacoes('enviadas', listaEnviadas, false);
});
