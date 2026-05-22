// mensagem.js - Sistema de conversas e envio de mensagens

let conversaAtual = null;
let usuarioLogadoId = null;

function formatarData(dataString) {
    if (!dataString) {
        return '';
    }
    const data = new Date(dataString);
    return data.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
}

async function carregarPerfil() {
    const token = localStorage.getItem('token');
    if (!token) {
        return null;
    }

    try {
        const response = await fetch(`${API_BASE}/api/perfil`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        const data = await response.json();

        if (!response.ok || data.erro) {
            return null;
        }

        usuarioLogadoId = data.usuario?.id || null;
        if (usuarioLogadoId) {
            localStorage.setItem('usuario_id', usuarioLogadoId);
        }
        return data.usuario || null;
    } catch (error) {
        console.error(error);
        return null;
    }
}

function criarElementoChat(conversa) {
    const li = document.createElement('li');
    li.className = 'chat-item';
    li.dataset.parceiroId = conversa.parceiro_id;

    li.innerHTML = `
        <div class="chat-item-textos">
            <strong>${conversa.parceiro_nome}</strong>
            <span class="chat-item-subtitle">${conversa.ultima_mensagem || 'Sem mensagens ainda'}</span>
        </div>
        <span class="chat-item-time">${formatarData(conversa.criado_em || conversa.enviada_em)}</span>
    `;

    li.addEventListener('click', () => selecionarConversa(conversa.parceiro_id, conversa.parceiro_nome));
    return li;
}

function mostrarPainelVazio(texto) {
    const chatArea = document.querySelector('.chat-area');
    if (!chatArea) return;

    chatArea.innerHTML = `<div class="chat-vazio"><p>${texto}</p></div>`;
    habilitarInput(false);
}

function habilitarInput(ativo) {
    const input = document.querySelector('.input-area input');
    const button = document.querySelector('.input-area button');
    if (!input || !button) return;

    input.disabled = !ativo;
    button.disabled = !ativo;
    input.placeholder = ativo ? 'Digite sua mensagem...' : 'Selecione uma conversa para enviar mensagem';
}

async function carregarConversas() {
    const token = localStorage.getItem('token');
    const listaChats = document.querySelector('.lista-chats');

    if (!token || !listaChats) {
        mostrarPainelVazio('Faça login para ver suas conversas.');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/conversas`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        const data = await response.json();

        if (!response.ok || data.sucesso === false) {
            mostrarPainelVazio(data.erro || 'Erro ao carregar conversas.');
            return;
        }

        listaChats.innerHTML = '';

        const conversas = data.dados.conversas || [];
        if (conversas.length === 0) {
            listaChats.innerHTML = '<li class="chat-item">Nenhuma conversa disponível.</li>';
            mostrarPainelVazio('Ainda não há mensagens. Abra uma conversa ou inicie um chat.');
            return;
        }

        conversas.forEach(conversa => listaChats.appendChild(criarElementoChat(conversa)));

        const params = new URLSearchParams(window.location.search);
        const destino = Number(params.get('destino')) || null;
        if (destino) {
            selecionarConversa(destino);
        }
    } catch (error) {
        console.error(error);
        mostrarPainelVazio('Erro de conexão ao carregar conversas.');
    }
}

async function selecionarConversa(parceiroId, parceiroNome = '') {
    const itens = document.querySelectorAll('.chat-item');
    itens.forEach(item => item.classList.remove('ativo'));

    const itemSelecionado = document.querySelector(`.chat-item[data-parceiro-id="${parceiroId}"]`);
    if (itemSelecionado) {
        itemSelecionado.classList.add('ativo');
    }

    conversaAtual = parceiroId;
    await carregarMensagens(parceiroId, parceiroNome);
    habilitarInput(true);
}

async function carregarUsuarioParceiro(parceiroId) {
    const token = localStorage.getItem('token');
    if (!token) {
        return null;
    }

    try {
        const response = await fetch(`${API_BASE}/api/usuarios/${parceiroId}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        const data = await response.json();
        if (!response.ok || data.sucesso === false) {
            return null;
        }
        return data.usuario || null;
    } catch (error) {
        console.error(error);
        return null;
    }
}

async function carregarMensagens(parceiroId, parceiroNome = '') {
    const token = localStorage.getItem('token');
    const chatArea = document.querySelector('.chat-area');

    if (!token || !chatArea) {
        mostrarPainelVazio('Faça login para ver suas mensagens.');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/conversas/${parceiroId}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        const data = await response.json();

        if (!response.ok || data.sucesso === false) {
            mostrarPainelVazio(data.erro || 'Erro ao carregar a conversa.');
            return;
        }

        const mensagens = data.dados.mensagens || [];

        if (!parceiroNome) {
            const item = document.querySelector(`.chat-item[data-parceiro-id="${parceiroId}"]`);
            parceiroNome = item ? item.querySelector('strong')?.innerText || '' : '';
        }

        if (!parceiroNome) {
            const parceiro = await carregarUsuarioParceiro(parceiroId);
            parceiroNome = parceiro?.nome_usuario || `Usuário ${parceiroId}`;
        }

        chatArea.innerHTML = `
            <div class="chat-conversas-topo">
                <h3>${parceiroNome}</h3>
                <span class="chat-status">${mensagens.length} mensagem(s)</span>
            </div>
            <div class="chat-mensagens"></div>
        `;

        const mensagensContainer = chatArea.querySelector('.chat-mensagens');

        if (mensagens.length === 0) {
            mensagensContainer.innerHTML = '<p class="chat-placeholder">Nenhuma mensagem nesta conversa ainda.</p>';
            return;
        }

        mensagens.forEach(mensagem => {
            const msgItem = document.createElement('div');
            const enviadoPorMim = mensagem.remetente_id === usuarioLogadoId;
            msgItem.className = `mensagem ${enviadoPorMim ? 'enviada' : 'recebida'}`;
            msgItem.innerHTML = `
                <p>${mensagem.conteudo}</p>
                <span>${formatarData(mensagem.criado_em || mensagem.enviada_em)}</span>
            `;
            mensagensContainer.appendChild(msgItem);
        });

        mensagensContainer.scrollTop = mensagensContainer.scrollHeight;
    } catch (error) {
        console.error(error);
        mostrarPainelVazio('Erro de conexão ao carregar a conversa.');
    }
}

async function enviarMensagem(event) {
    event.preventDefault();

    if (!conversaAtual) {
        return;
    }

    const input = document.querySelector('.input-area input');
    const texto = input.value.trim();

    if (!texto) {
        mostrarMensagem('Digite uma mensagem antes de enviar.', 'erro');
        return;
    }

    const token = localStorage.getItem('token');
    if (!token) {
        mostrarMensagem('Você precisa estar logado para enviar mensagem.', 'erro');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/api/conversas/${conversaAtual}/mensagens`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify({ conteudo: texto })
        });

        const data = await response.json();

        if (!response.ok || data.sucesso === false) {
            mostrarMensagem(data.erro || 'Erro ao enviar a mensagem.', 'erro');
            return;
        }

        input.value = '';
        mostrarMensagem(data.mensagem || 'Mensagem enviada.', 'sucesso');
        await carregarMensagens(conversaAtual);
        await carregarConversas();
    } catch (error) {
        console.error(error);
        mostrarMensagem('Erro de conexão ao enviar mensagem.', 'erro');
    }
}

async function inicializarChat() {
    await carregarPerfil();

    const form = document.querySelector('.input-area');
    if (form) {
        form.addEventListener('submit', enviarMensagem);
    }

    habilitarInput(false);
    carregarConversas();
}

document.addEventListener('DOMContentLoaded', inicializarChat);
