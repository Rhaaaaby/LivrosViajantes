// api-config.js - Configuração centralizada para todas as requisições API

// Detecta a base URL de forma robusta
function getAPIBase() {
    const pathname = window.location.pathname;
    
    // Se está em uma página como /livrosViajantes/public/pages/notificacao.html
    if (pathname.includes('/pages/')) {
        return pathname.split('/pages/')[0];
    }
    
    // Se está na raiz /livrosViajantes/public/
    if (pathname.endsWith('/')) {
        return pathname.replace(/\/$/, '');
    }
    
    // Fallback - retorna tudo antes do último segmento
    return pathname.substring(0, pathname.lastIndexOf('/'));
}

// API_BASE será usado em todos os fetch
const API_BASE = getAPIBase();

console.log('[API Config] Base URL:', API_BASE);
