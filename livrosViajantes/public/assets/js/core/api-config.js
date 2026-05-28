// api-config.js

// Detecta se está rodando localmente (localhost ou 127.0.0.1)
const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

// Se for local, usa o subdiretório. Na Render, a raiz é a própria pasta public, então fica vazio ('')
window.API_BASE = isLocalhost ? '/livrosViajantes/public' : '';

console.log('[API Config] Ambiente:', isLocalhost ? 'Localhost' : 'Produção (Render)');
console.log('[API Config] API_BASE:', window.API_BASE);