// whatsapp-bot/botwhatsapp.js
require('dotenv').config();

const express = require('express');
const cors = require('cors');
const wppconnect = require('@wppconnect-team/wppconnect');
const { google } = require('googleapis');
const dashboardRoutes = require('./dashboard');
const setupSettingsRoutes = require('./settings');
const fs = require('fs');
const path = require('path');
const app = express();
const SCOPES = ['https://www.googleapis.com/auth/contacts.readonly'];
const CREDENTIALS_PATH = path.resolve(__dirname, './credentials.json');
const TOKEN_PATH = path.resolve(__dirname, './token.json');
const hasFn = (obj, name) => obj && typeof obj[name] === 'function';

function loadCredentials() {
  if (!fs.existsSync(CREDENTIALS_PATH)) {
    throw new Error('credentials.json não encontrado. Baixe do Google Cloud Console e coloque em ./whatsapp-bot/credentials.json');
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

async function getGoogleAuthOrThrowNeedAuth() {
  const oAuth2Client = buildOAuthClient();

  if (!haveTokenFile()) {
    const url = oAuth2Client.generateAuthUrl({ access_type: 'offline', scope: SCOPES, prompt: 'consent' });
    const err = new Error('NEED_AUTH');
    err.needAuth = true; err.authUrl = url;
    throw err;
  }

  const tokens = loadTokens();
  if (!tokens.refresh_token) {
    const url = oAuth2Client.generateAuthUrl({ access_type: 'offline', scope: SCOPES, prompt: 'consent' });
    const err = new Error('NEED_AUTH_NO_REFRESH');
    err.needAuth = true; err.authUrl = url;
    throw err;
  }

  oAuth2Client.setCredentials(tokens);
  oAuth2Client.on('tokens', (t) => {
    const merged = { ...oAuth2Client.credentials, ...t };
    fs.writeFileSync(TOKEN_PATH, JSON.stringify(merged, null, 2));
  });

  return oAuth2Client;
}

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

app.use(cors({ origin: '*', methods: ['GET', 'POST'], allowedHeaders: ['Content-Type'] }));
app.use(express.json());

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

wppconnect.create({
  session: process.env.WPP_SESSION || 'sessionName',
  catchQR: (base64Qr) => { qrCodeBase64 = base64Qr; isConnected = false; log('qr', 'QR atualizado'); },
  statusFind: (status) => {
    log('status', `Status da sessão: ${status}`);
    const okStatuses = ['isLogged', 'inChat', 'chatsAvailable', 'phoneConnected'];
    isConnected = okStatuses.includes(status);
    if (isConnected) qrCodeBase64 = null;
  },
  logQR: false,
})
  .then((client) => {
    clientGlobal = client;
    const setupLabelRoutes = require('./labels');
    app.use('/dashboard', dashboardRoutes(clientGlobal));
    setupLabelRoutes(app, clientGlobal, log);
    setupSettingsRoutes(app, clientGlobal);
    log('info', 'Bot WhatsApp iniciado!');
  })
  .catch((err) => {
    log('error', 'Falha ao iniciar wppconnect', { err: String(err) });
  });

app.get('/verdurao/bot/whatsapp/qrcode', (req, res) => {
  if (isConnected) return res.json({ connected: true });
  return res.json({ connected: false, qrcode: qrCodeBase64 || null });
});

app.get('/verdurao/bot/whatsapp/session', async (req, res) => {
  try {
    if (!clientGlobal) return res.json({ connected: false });

    let phone = null, pushname = null;

    if (isConnected) {
      // 1) host device
      if (typeof clientGlobal.getHostDevice === 'function') {
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
        } catch { }
      }

      // 2) getWid()
      if (!phone && typeof clientGlobal.getWid === 'function') {
        try {
          const wid = await clientGlobal.getWid();
          if (wid) phone = String(wid).replace(/@.*/, '');
        } catch { }
      }

      // 3) getMe()
      if ((!phone || !pushname) && typeof clientGlobal.getMe === 'function') {
        try {
          const me = await clientGlobal.getMe();
          const candMe = me?.wid?._serialized || me?.id?._serialized || me;
          if (!phone && candMe) phone = String(candMe).replace(/@.*/, '');
          if (!pushname) pushname = me?.pushname || me?.name || null;
        } catch { }
      }

      // 4) fallback pelos contatos (acha o "eu")
      if (!phone && typeof clientGlobal.getAllContacts === 'function') {
        try {
          const all = await clientGlobal.getAllContacts();
          const meC =
            (all || []).find(c => c?.isMyContact || c?.isMe || c?.isMyNumber) ||
            (all || []).find(c => c?.id?._serialized?.endsWith('@c.us') && c?.isBusiness === false);
          const id = meC?.id?._serialized || meC?.id || null;
          if (id) phone = String(id).replace(/@.*/, '');
          if (!pushname) pushname = meC?.pushname || meC?.name || null;
        } catch { }
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

    if (typeof clientGlobal.logout === 'function') {
      await clientGlobal.logout();
    } else if (typeof clientGlobal.close === 'function') {
      await clientGlobal.close();
    }

    isConnected = false;
    qrCodeBase64 = null;
    log('info', 'Sessão desconectada via API');
    return res.json({ ok: true });
  } catch (e) {
    log('error', 'Falha no logout', { err: String(e) });
    return res.status(500).json({ error: e?.message || String(e) });
  }
});

async function listChatsSmart(client) {
  if (hasFn(client, 'listChats')) {
    try { return await client.listChats(); } catch { }
  }
  if (hasFn(client, 'getAllChats')) {
    try { return await client.getAllChats(); } catch { }
  }
  return [];
}
app.get('/verdurao/bot/whatsapp/labels/:id/chats', async (req, res) => {
  try {
    if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
    const labelId = String(req.params.id || '').trim();
    if (!labelId) return res.status(400).json({ error: 'labelId inválido' });

    let chats = [];
    if (hasFn(clientGlobal, 'getChatsByLabelId')) {
      // caminho feliz (algumas versões do wppconnect suportam)
      chats = await clientGlobal.getChatsByLabelId(labelId);
    } else {
      // fallback: pega todos os chats e filtra quem contém a label
      const all = await listChatsSmart(clientGlobal) || [];
      chats = all.filter(ch => {
        const lbls = ch?.labels;
        if (!lbls) return false;
        // lbls pode ser array de objetos/strings; tentamos cobrir os formatos comuns
        const arr = Array.isArray(lbls) ? lbls : Object.values(lbls);
        return arr.some(l =>
          String(l?.id ?? l?._serialized ?? l) === labelId
        );
      });
    }

    const items = (chats || []).map(ch => {
      // extrai jid e phone
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
    res.status(500).json({ error: e?.message || String(e) });
  }
});

function normalizeSavedContact(c) {
  try {
    const server = c?.id?.server;
    const user = c?.id?.user;
    if (server !== 'c.us' || !user) return null;
    return {
      name: c?.formattedName || c?.name || c?.pushname || user,
      phone: user,
      saved: true,
    };
  } catch {
    return null;
  }
}

function normalizeChat(ch) {
  try {
    const server = ch?.id?.server;
    const user = ch?.id?.user;
    if (server !== 'c.us' || !user) return null;
    return {
      name: ch?.formattedTitle || ch?.name || user,
      phone: user,
      saved: false,
    };
  } catch {
    return null;
  }
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

    for (const c of saved || []) {
      const entry = normalizeSavedContact(c);
      if (entry) map.set(entry.phone, entry);
    }

    for (const ch of chats || []) {
      const entry = normalizeChat(ch);
      if (entry && !map.has(entry.phone)) map.set(entry.phone, entry);
    }

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
    .map(person => ({
      name: person.names?.[0]?.displayName || 'Sem Nome',
      phone: person.phoneNumbers?.[0]?.value || '',
      saved: true,
    }))
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

const PORT = process.env.PORT || 3001;
app.listen(PORT, () => console.log(`API do WhatsApp rodando na porta ${PORT}`));
