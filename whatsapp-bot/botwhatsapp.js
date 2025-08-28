// whatsapp-bot/botwhatsapp.js

/**
 * Bot WhatsApp (WPPConnect) + Integração n8n/Laravel
 * - Lê .env da pasta do bot (ou cwd/../) com fallback
 * - Modo n8n (USE_N8N=true): envia {client,text} para o Webhook (Production URL)
 * - Modo direto (USE_N8N=false): chama API do Laravel (/api/products, /api/carts, /api/orders)
 */

const path = require('path');
const fs = require('fs');
const dotenv = require('dotenv');

// Procura o .env em múltiplos caminhos (prioridade nessa ordem)
const candidateEnvPaths = [
  path.resolve(__dirname, '.env'),           // whatsapp-bot/.env
  path.resolve(process.cwd(), '.env'),       // cwd/.env
  path.resolve(__dirname, '..', '.env'),     // raiz/.env
];

let ENV_PATH = null;
for (const p of candidateEnvPaths) {
  if (fs.existsSync(p)) { ENV_PATH = p; break; }
}
// Carrega .env (se achou); override para dar prioridade ao arquivo
if (ENV_PATH) dotenv.config({ path: ENV_PATH, override: true }); else dotenv.config();

const express = require('express');
const cors = require('cors');
const wppconnect = require('@wppconnect-team/wppconnect');
const axios = require('axios');
const { google } = require('googleapis');
const dashboardRoutes = require('./dashboard');
const setupSettingsRoutes = require('./settings');
let setupLabelRoutes = null;
try { setupLabelRoutes = require('./labels'); } catch { /* opcional */ }

const app = express();

/* ========================= CONFIG / PATHS ========================= */
const SCOPES = ['https://www.googleapis.com/auth/contacts.readonly'];
const CREDENTIALS_PATH = path.resolve(__dirname, './credentials.json');
const TOKEN_PATH = path.resolve(__dirname, './token.json');

/* ========================= ENV VARS ========================= */
const SESSION      = process.env.WPP_SESSION || 'sessionName';
const USE_N8N      = (process.env.USE_N8N || 'true').toLowerCase() === 'true';
const API_BASE_URL = (process.env.API_BASE_URL || '').trim();
const N8N_API_KEY  = (process.env.N8N_API_KEY || '').trim();
const PORT         = Number(process.env.PORT || 3001);

// N8N webhook com fallback: tenta ler direto do arquivo se não veio pelas envs
let N8N_WEBHOOK = (process.env.N8N_WEBHOOK_URL || '').trim();
if (!N8N_WEBHOOK && ENV_PATH && fs.existsSync(ENV_PATH)) {
  try {
    const raw = fs.readFileSync(ENV_PATH, 'utf8');
    const m = raw.match(/^\s*N8N_WEBHOOK_URL\s*=\s*(.+)\s*$/m);
    if (m && m[1]) N8N_WEBHOOK = m[1].trim().replace(/^["']|["']$/g, '');
  } catch {}
}

/* ========================= UTILS ========================= */
const hasFn = (obj, name) => obj && typeof obj[name] === 'function';
const mask  = (v) => (v ? v.slice(0, 4) + '…' + v.slice(-4) : '');
const clientFrom = (jid) => String(jid || '').replace('@c.us', '');

function validateWebhookOrThrow() {
  if (!N8N_WEBHOOK) {
    throw new Error(
      `N8N_WEBHOOK_URL vazio. Defina no .env (Webhook node → Production URL). Arquivo: ${ENV_PATH || '(não encontrado)'}`
    );
  }
  if (!/^https?:\/\/.+\/webhook\/.+/i.test(N8N_WEBHOOK)) {
    throw new Error(
      `N8N_WEBHOOK_URL inválido: ${N8N_WEBHOOK}\nUse a Production URL do nó Webhook (termina com /webhook/...)`
    );
  }
}

function loadCredentials() {
  if (!fs.existsSync(CREDENTIALS_PATH)) {
    throw new Error('credentials.json não encontrado em ./whatsapp-bot/credentials.json');
  }
  const credentials = JSON.parse(fs.readFileSync(CREDENTIALS_PATH, 'utf8'));
  const data = credentials.installed || credentials.web;
  if (!data?.client_id || !data?.client_secret || !(data.redirect_uris && data.redirect_uris.length)) {
    throw new Error('credentials.json inválido: faltam client_id/client_secret/redirect_uris.');
  }
  return data;
}

function buildOAuthClient() {
  const { client_id, client_secret, redirect_uris } = loadCredentials();
  return new google.auth.OAuth2(client_id, client_secret, redirect_uris[0]);
}

function haveTokenFile() { return fs.existsSync(TOKEN_PATH); }
function loadTokens() { return JSON.parse(fs.readFileSync(TOKEN_PATH, 'utf8')); }

/* ========================= GOOGLE AUTH HELPERS ========================= */
async function getGoogleAuthOrThrowNeedAuth() {
  const oAuth2Client = buildOAuthClient();
  if (!haveTokenFile()) {
    const url = oAuth2Client.generateAuthUrl({ access_type: 'offline', scope: SCOPES, prompt: 'consent' });
    const err = new Error('NEED_AUTH'); err.needAuth = true; err.authUrl = url; throw err;
  }
  const tokens = loadTokens();
  if (!tokens.refresh_token) {
    const url = oAuth2Client.generateAuthUrl({ access_type: 'offline', scope: SCOPES, prompt: 'consent' });
    const err = new Error('NEED_AUTH_NO_REFRESH'); err.needAuth = true; err.authUrl = url; throw err;
  }
  oAuth2Client.setCredentials(tokens);
  oAuth2Client.on('tokens', (t) => {
    const merged = { ...oAuth2Client.credentials, ...t };
    fs.writeFileSync(TOKEN_PATH, JSON.stringify(merged, null, 2));
  });
  return oAuth2Client;
}

/* ========================= LOG / STATE ========================= */
let qrCodeBase64 = null;
let isConnected = false;
let clientGlobal = null;

const sendLogs = [];
function log(type, message, meta = {}) {
  const entry = { ts: new Date().toISOString(), type, message, meta };
  sendLogs.push(entry);
  console.log(`[${type}] ${message}`, Object.keys(meta).length ? meta : '');
  if (sendLogs.length > 1000) sendLogs.shift();
}

/* ========================= EXPRESS APP ========================= */
app.use(cors({ origin: '*', methods: ['GET', 'POST'], allowedHeaders: ['Content-Type'] }));
app.use(express.json());

// Health para diagnosticar .env no ar
app.get('/verdurao/bot/whatsapp/healthz', (req, res) => {
  res.json({
    ok: true,
    envPath: ENV_PATH,
    envExists: !!ENV_PATH && fs.existsSync(ENV_PATH),
    useN8n: USE_N8N,
    n8nWebhook: N8N_WEBHOOK,
    apiBaseUrl: API_BASE_URL,
    hasApiKey: !!N8N_API_KEY,
    cwd: process.cwd(),
    dirname: __dirname,
  });
});

/* ---- Google OAuth ---- */
app.get('/verdurao/bot/whatsapp/google-auth', (req, res) => {
  try {
    const oAuth2Client = buildOAuthClient();
    const url = oAuth2Client.generateAuthUrl({ access_type: 'offline', scope: SCOPES, prompt: 'consent' });
    res.json({ url });
  } catch (e) {
    res.status(500).json({ error: String(e.message || e) });
  }
});

app.get('/verdurao/bot/whatsapp/google-auth/callback', async (req, res) => {
  try {
    const code = req.query.code;
    const oAuth2Client = buildOAuthClient();
    const { tokens } = await oAuth2Client.getToken(code);
    fs.writeFileSync(TOKEN_PATH, JSON.stringify(tokens, null, 2));
    res.send('Autenticado! Pode fechar esta janela e voltar ao app.');
  } catch (e) {
    res.status(500).send('Falha ao autenticar: ' + (e?.message || e));
  }
});

/* ---- Info sessão / QR ---- */
app.get('/verdurao/bot/whatsapp/qrcode', (req, res) => {
  if (isConnected) return res.json({ connected: true });
  return res.json({ connected: false, qrcode: qrCodeBase64 || null });
});

app.get('/verdurao/bot/whatsapp/session', async (req, res) => {
  try {
    if (!clientGlobal) return res.json({ connected: false });
    let phone = null, pushname = null;

    if (isConnected) {
      if (hasFn(clientGlobal, 'getHostDevice')) {
        try {
          const host = await clientGlobal.getHostDevice();
          pushname = host?.pushname || host?.name || null;
          const candHost =
            host?.wid?._serialized ||
            (host?.wid?.user && `${host.wid.user}@${host.wid.server || 'c.us'}`) ||
            host?.id?._serialized ||
            (host?.id?.user && `${host.id.user}@${host.id.server || 'c.us'}`) ||
            null;
          if (candHost) phone = String(candHost).replace(/@.*/, '');
        } catch {}
      }
      if (!phone && hasFn(clientGlobal, 'getWid')) {
        try { const wid = await clientGlobal.getWid(); if (wid) phone = String(wid).replace(/@.*/, ''); } catch {}
      }
      if ((!phone || !pushname) && hasFn(clientGlobal, 'getMe')) {
        try {
          const me = await clientGlobal.getMe();
          const candMe = me?.wid?._serialized || me?.id?._serialized || me;
          if (!phone && candMe) phone = String(candMe).replace(/@.*/, '');
          if (!pushname) pushname = me?.pushname || me?.name || null;
        } catch {}
      }
      if (!phone && hasFn(clientGlobal, 'getAllContacts')) {
        try {
          const all = await clientGlobal.getAllContacts();
          const meC =
            (all || []).find(c => c?.isMyContact || c?.isMe || c?.isMyNumber) ||
            (all || []).find(c => c?.id?._serialized?.endsWith('@c.us') && c?.isBusiness === false);
          const id = meC?.id?._serialized || meC?.id || null;
          if (id) phone = String(id).replace(/@.*/, '');
          if (!pushname) pushname = meC?.pushname || meC?.name || null;
        } catch {}
      }
    }

    return res.json({ connected: !!isConnected, phone, pushname });
  } catch {
    return res.json({ connected: !!isConnected, phone: null, pushname: null });
  }
});

app.post('/verdurao/bot/whatsapp/logout', async (req, res) => {
  try {
    if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
    if (hasFn(clientGlobal, 'logout')) await clientGlobal.logout();
    else if (hasFn(clientGlobal, 'close')) await clientGlobal.close();
    isConnected = false; qrCodeBase64 = null;
    log('info', 'Sessão desconectada via API');
    res.json({ ok: true });
  } catch (e) {
    log('error', 'Falha no logout', { err: String(e) });
    res.status(500).json({ error: e?.message || String(e) });
  }
});

/* ---- Labels / Chats ---- */
async function listChatsSmart(client) {
  if (hasFn(client, 'listChats')) { try { return await client.listChats(); } catch {} }
  if (hasFn(client, 'getAllChats')) { try { return await client.getAllChats(); } catch {} }
  return [];
}

app.get('/verdurao/bot/whatsapp/labels/:id/chats', async (req, res) => {
  try {
    if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
    const labelId = String(req.params.id || '').trim();
    if (!labelId) return res.status(400).json({ error: 'labelId inválido' });

    let chats = [];
    if (setupLabelRoutes && hasFn(clientGlobal, 'getChatsByLabelId')) {
      chats = await clientGlobal.getChatsByLabelId(labelId);
    } else {
      const all = await listChatsSmart(clientGlobal) || [];
      chats = all.filter(ch => {
        const lbls = ch?.labels;
        if (!lbls) return false;
        const arr = Array.isArray(lbls) ? lbls : Object.values(lbls);
        return arr.some(l => String(l?.id ?? l?._serialized ?? l) === labelId);
      });
    }

    const items = (chats || []).map(ch => {
      const rawId = typeof ch?.id === 'object'
        ? (ch.id._serialized || (ch.id.user && `${ch.id.user}@${ch.id.server}`) || String(ch.id))
        : String(ch?.id || '');
      const m = rawId.match(/^(\d+)@/);
      const phone = m ? m[1] : null;
      const name = ch?.formattedTitle || ch?.name || ch?.contact?.name || ch?.contact?.pushname || phone;
      return phone ? { name, phone, jid: rawId } : null;
    }).filter(Boolean);

    res.json(items);
  } catch (e) {
    log('error', 'labels/:id/chats error', { err: String(e) });
    res.status(500).json({ error: e?.message || e });
  }
});

/* ---- Contacts (App + Google) ---- */
function normalizeSavedContact(c) {
  try {
    const server = c?.id?.server;
    const user = c?.id?.user;
    if (server !== 'c.us' || !user) return null;
    return { name: c?.formattedName || c?.name || c?.pushname || user, phone: user, saved: true };
  } catch { return null; }
}

function normalizeChat(ch) {
  try {
    const server = ch?.id?.server;
    const user = ch?.id?.user;
    if (server !== 'c.us' || !user) return null;
    return { name: ch?.formattedTitle || ch?.name || user, phone: user, saved: false };
  } catch { return null; }
}

app.get('/verdurao/bot/whatsapp/all-contacts', async (req, res) => {
  try {
    if (!clientGlobal) {
      log('warn', 'Tentativa de listar contatos sem client conectado');
      return res.status(500).json({ error: 'Cliente não conectado' });
    }

    const [saved, chats] = await Promise.all([
      clientGlobal.getAllContacts(),
      clientGlobal.listChats(),
    ]);

    const map = new Map();
    for (const c of saved || []) { const e = normalizeSavedContact(c); if (e) map.set(e.phone, e); }
    for (const ch of chats || []) { const e = normalizeChat(ch); if (e && !map.has(e.phone)) map.set(e.phone, e); }

    return res.json(Array.from(map.values()));
  } catch (err) {
    const detail = err?.stack || err?.message || String(err);
    log('error', 'Erro ao listar contatos', { detail });
    return res.status(500).json({ error: 'Erro ao listar contatos', detail });
  }
});

async function getGoogleContacts(auth) {
  const service = google.people({ version: 'v1', auth });
  const r = await service.people.connections.list({
    resourceName: 'people/me',
    personFields: 'names,phoneNumbers',
    pageSize: 1000,
  });
  const connections = r.data.connections || [];
  return connections
    .map(p => ({ name: p.names?.[0]?.displayName || 'Sem Nome', phone: p.phoneNumbers?.[0]?.value || '', saved: true }))
    .filter(c => c.phone);
}

app.get('/verdurao/bot/whatsapp/google-contacts', async (req, res) => {
  try {
    const auth = await getGoogleAuthOrThrowNeedAuth();
    const contacts = await getGoogleContacts(auth);
    res.json(contacts);
  } catch (err) {
    if (err.needAuth) return res.status(401).json({ needAuth: true, url: err.authUrl });
    const details = err?.response?.data || err?.message || String(err);
    log('error', 'Erro ao listar contatos do Google', { details });
    return res.status(500).json({ error: details });
  }
});

/* ---- Logs + Envio em massa ---- */
app.get('/verdurao/bot/whatsapp/logs', (req, res) => {
  const limit = Math.min(parseInt(req.query.limit || '200', 10), 1000);
  res.json(sendLogs.slice(-limit));
});

app.post('/verdurao/bot/whatsapp/send-mass', async (req, res) => {
  const { contacts, message, intervalSeconds = 1, randomInterval = false, simulateTyping = false } = req.body || {};
  if (!clientGlobal || !Array.isArray(contacts) || !contacts.length || !message) {
    return res.status(400).json({ error: 'Client não conectado ou dados inválidos.' });
  }

  log('info', 'Iniciando envio em massa', { total: contacts.length });

  const results = [];
  for (let i = 0; i < contacts.length; i++) {
    const contact = contacts[i];
    const raw = (contact.phone || '').replace(/\D/g, '');
    const jid = raw.endsWith('@c.us') ? raw : `${raw}@c.us`;
    const personalized = message
      .replace(/{nome}/g, contact.name || '')
      .replace(/{telefone}/g, contact.phone || '');

    try {
      if (simulateTyping) await new Promise(r => setTimeout(r, 800));
      await clientGlobal.sendText(jid, personalized);
      log('sent', 'Mensagem enviada', { to: jid });
      results.push({ phone: contact.phone, status: 'enviado' });
    } catch (err) {
      log('error', 'Falha ao enviar', { to: jid, err: String(err) });
      results.push({ phone: contact.phone, status: 'erro', error: String(err) });
    }

    const base = Math.max(1, Number(intervalSeconds)) * 1000;
    const wait = randomInterval ? Math.round(base * (0.7 + Math.random() * 0.6)) : base;
    if (i < contacts.length - 1) await new Promise(r => setTimeout(r, wait));
  }

  res.json(results);
});

/* ========================= E-COMMERCE: n8n / API ========================= */
async function callN8n(client, text) {
  validateWebhookOrThrow();
  const payload = { client, text };
  log('n8n', 'POST webhook', { url: N8N_WEBHOOK, payload });
  const res = await axios.post(N8N_WEBHOOK, payload, {
    timeout: 15000,
    headers: { 'Content-Type': 'application/json' },
  });
  const data = res.data;
  return typeof data === 'string' ? data : JSON.stringify(data);
}

async function callApiDirect(client, text) {
  const parts = (text || '').trim().split(/\s+/);
  const cmd = (parts.shift() || '').toUpperCase();
  const headers = { 'X-API-KEY': N8N_API_KEY, 'Content-Type': 'application/json' };

  if (cmd === 'CATALOGO') {
    const q = parts.join(' ');
    const r = await axios.get(`${API_BASE_URL}/api/products/search`, {
      params: { q },
      headers: { 'X-API-KEY': N8N_API_KEY },
      timeout: 15000
    });
    const rows = Array.isArray(r.data) ? r.data : [];
    if (!rows.length) return 'Nenhum produto encontrado.';
    const top = rows.slice(0, 5).map((it) =>
      `• ${it.cod_produto} – ${it.nome_produto} (R$ ${Number(it.preco_venda || 0).toFixed(2)}) [${it.qtd_disponivel} un]`
    ).join('\n');
    return `Resultados:\n${top}\n\nUse: ADD <SKU> <QTD>`;
  }

  if (cmd === 'ADD') {
    const sku = parts[0];
    const qty = Number(parts[1] || 1);
    if (!sku) return 'Uso: ADD <SKU> <QTD>';
    const r = await axios.post(`${API_BASE_URL}/api/carts/upsert`,
      { client, items: [{ sku, qty }] }, { headers, timeout: 15000 });
    const cart = r.data || {};
    const lines = (cart.items || []).map(i =>
      `• ${i.cod_produto} x${i.quantidade} = R$ ${Number(i.subtotal_valor).toFixed(2)}`
    ).join('\n');
    return `Carrinho #${cart.id}\n${lines}\nTotal: R$ ${Number(cart.total_valor || 0).toFixed(2)}\n\nEnvie: CARRINHO ou FINALIZAR`;
  }

  if (cmd === 'CARRINHO') {
    const r = await axios.get(`${API_BASE_URL}/api/carts/by-client/${client}`, {
      headers: { 'X-API-KEY': N8N_API_KEY }, timeout: 15000
    });
    const cart = r.data || {};
    if (!cart.items || !cart.items.length) return 'Seu carrinho está vazio.';
    const lines = cart.items.map(i =>
      `• ${i.cod_produto} x${i.quantidade} = R$ ${Number(i.subtotal_valor).toFixed(2)}`
    ).join('\n');
    return `Carrinho #${cart.id}\n${lines}\nTotal: R$ ${Number(cart.total_valor || 0).toFixed(2)}\n\nEnvie: FINALIZAR`;
  }

  if (cmd === 'FINALIZAR') {
    const r = await axios.post(`${API_BASE_URL}/api/orders`,
      { client }, { headers, timeout: 15000 });
    const o = r.data || {};
    const lines = (o.items || []).map(i =>
      `• ${i.cod_produto} x${i.quantidade} = R$ ${Number(i.subtotal_valor).toFixed(2)}`
    ).join('\n');
    return `Pedido #${o.id}\n${lines}\nTotal: R$ ${Number(o.total_valor || 0).toFixed(2)}\nStatus: ${o.status || 'created'}`;
  }

  return 'Comando inválido. Use: CATALOGO <termo> | ADD <sku> <qtd> | CARRINHO | FINALIZAR';
}

/* ========================= WPPConnect ========================= */
wppconnect.create({
  session: SESSION,
  catchQR: (base64Qr) => { qrCodeBase64 = base64Qr; isConnected = false; log('qr', 'QR atualizado'); },
  statusFind: (status) => {
    log('status', `Status da sessão: ${status}`);
    const okStatuses = ['isLogged', 'inChat', 'chatsAvailable', 'phoneConnected'];
    isConnected = okStatuses.includes(status);
    if (isConnected) qrCodeBase64 = null;
  },
  logQR: false,
  headless: 'new',
  puppeteerOptions: { args: ['--no-sandbox','--disable-setuid-sandbox'] },
})
.then((client) => {
  clientGlobal = client;

  // Rotas auxiliares do seu projeto (se existirem)
  app.use('/dashboard', dashboardRoutes(clientGlobal));
  if (setupLabelRoutes) setupLabelRoutes(app, clientGlobal, log);
  setupSettingsRoutes(app, clientGlobal);

  log('info', 'Bot WhatsApp iniciado!');

  // Ouve mensagens e responde via n8n ou API
  client.onMessage(async (message) => {
    try {
      if (!message?.from || !message?.body) return;
      if (message.isGroupMsg) return; // ignora grupos

      const client = clientFrom(message.from);
      const text   = String(message.body || '').trim();

      // teste rápido
      if (text.toUpperCase() === 'PING') {
        await client.sendText(message.from, 'PONG ✅');
        return;
      }

      log('rx', 'msg recebida', { client, text });

      let reply;
      if (USE_N8N) {
        reply = await callN8n(client, text);
      } else {
        if (!API_BASE_URL) throw new Error('API_BASE_URL não configurada no .env.');
        reply = await callApiDirect(client, text);
      }

      if (reply) {
        await client.sendText(message.from, String(reply));
        log('tx', 'resposta enviada', { client });
      }
    } catch (err) {
      const detail = err?.response?.data || err?.message || String(err);
      log('error', 'onMessage erro', { detail });
      try { await client.sendText(message.from, 'Erro ao processar sua mensagem. ' + String(detail).slice(0, 300)); } catch {}
    }
  });

})
.catch((err) => {
  log('error', 'Falha ao iniciar wppconnect', { err: String(err) });
});

/* ========================= START SERVER ========================= */
app.listen(PORT, () => {
  console.log('=== BOOT ===');
  console.log('ENV_PATH    :', ENV_PATH || '(não encontrado)', 'exists:', !!ENV_PATH && fs.existsSync(ENV_PATH));
  console.log('CWD         :', process.cwd());
  console.log('DIRNAME     :', __dirname);
  console.log('SESSION     :', SESSION);
  console.log('USE_N8N     :', USE_N8N);
  console.log('N8N_WEBHOOK :', N8N_WEBHOOK || '(vazio)');
  console.log('API_BASE_URL:', API_BASE_URL || '(vazio)');
  console.log('N8N_API_KEY :', mask(N8N_API_KEY));
  console.log(`API do WhatsApp rodando na porta ${PORT}`);
});
