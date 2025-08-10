// whatsapp-bot/dashboard.js
const express = require('express');
const router = express.Router();

module.exports = (clientGlobal) => {
  // =========================
  // Helpers
  // =========================
  function dateNDaysAgo(days) {
    const d = new Date();
    d.setDate(d.getDate() - days);
    return d;
  }
  function secsNDaysAgo(days) {
    return Math.floor(dateNDaysAgo(days).getTime() / 1000);
  }
  function normalizePhone(p) {
    return String(p || '').replace(/\D/g, '');
  }
  function hasFn(obj, name) {
    return obj && typeof obj[name] === 'function';
  }

  /**
   * Aceita id como string ('55119...@c.us') ou objeto ({ user, server, _serialized }).
   * Retorna { user, server, jid } ou null.
   */
  function parseJid(anyId) {
    try {
      let serialized = '';
      if (!anyId) return null;

      if (typeof anyId === 'string') {
        serialized = anyId;
      } else if (typeof anyId === 'object') {
        if (anyId._serialized) serialized = anyId._serialized;
        else if (anyId.user && anyId.server) serialized = `${anyId.user}@${anyId.server}`;
        else if (anyId.id) serialized = String(anyId.id);
      }

      if (!serialized) return null;

      const m = serialized.match(/^(.+?)@([a-z0-9.\-]+)$/i);
      if (!m) return null;

      const user = normalizePhone(m[1]);
      const server = m[2];
      if (!user || !server) return null;

      return { user, server, jid: `${user}@${server}` };
    } catch {
      return null;
    }
  }

  /** Extrai JID c.us de um chat (ou null). */
  function jidFromChat(chat) {
    const parsed = parseJid(chat?.id);
    if (!parsed || parsed.server !== 'c.us' || !parsed.user) return null;
    return parsed.jid;
  }

  /** Timestamp (segundos) robusto para mensagens. */
  function msgTsSec(msg) {
    if (typeof msg?.t === 'number') return msg.t;
    if (typeof msg?.timestamp === 'number') {
      return msg.timestamp > 1e12 ? Math.round(msg.timestamp / 1000) : msg.timestamp;
    }
    if (typeof msg?.messageTimestamp === 'number') return msg.messageTimestamp;
    if (msg?.date instanceof Date) return Math.round(msg.date.getTime() / 1000);
    return Math.floor(Date.now() / 1000);
  }

  /** Nome amigável do chat. */
  function chatDisplayName(chat, fallbackUser) {
    return (
      chat?.formattedTitle ||
      chat?.name ||
      chat?.contact?.name ||
      chat?.contact?.pushname ||
      fallbackUser ||
      'Contato'
    );
  }

  /** Tenta pegar mensagens do chat com vários fallbacks. */
  async function getMessagesSmart(client, chat) {
    // 1) tenta com id _serialized
    const parsed = parseJid(chat?.id);
    const jid = parsed?.jid || null;
    const serialized = (typeof chat?.id === 'object' && chat.id._serialized) ? chat.id._serialized : null;

    // se o chat já trouxe a última, use direto
    if (Array.isArray(chat?.msgs) && chat.msgs.length) {
      return chat.msgs;
    }

    // ordem de tentativas (mais completo -> mais básico)
    const candidates = [];
    if (serialized) candidates.push(serialized);
    if (jid && jid !== serialized) candidates.push(jid);

    for (const chatId of candidates) {
      // loadAndGetAllMessagesInChat (se existir) força carregar histórico
      if (hasFn(client, 'loadAndGetAllMessagesInChat')) {
        try {
          const msgs = await client.loadAndGetAllMessagesInChat(chatId, true, false);
          if (Array.isArray(msgs) && msgs.length) return msgs;
        } catch (_) {}
      }
      // getAllMessagesInChat padrão
      if (hasFn(client, 'getAllMessagesInChat')) {
        try {
          const msgs = await client.getAllMessagesInChat(chatId, true, false);
          if (Array.isArray(msgs) && msgs.length) return msgs;
        } catch (_) {}
      }
      // getMessages (algumas versões expõem)
      if (hasFn(client, 'getMessages')) {
        try {
          const msgs = await client.getMessages(chatId, { count: 50, direction: 'backward' });
          if (Array.isArray(msgs) && msgs.length) return msgs;
        } catch (_) {}
      }
    }

    return []; // nada encontrado
  }

  // =========================
  // /stats/sent-messages
  // =========================
  router.get('/stats/sent-messages', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

      const chats = await clientGlobal.listChats();
      if (!Array.isArray(chats) || chats.length === 0) {
        return res.json({ sent: 0, contactsActive: 0 });
      }

      const since = secsNDaysAgo(30);
      let sentMessages = 0;
      const contactsActive = new Set();

      for (const chat of chats) {
        const jid = jidFromChat(chat);
        if (!jid) continue;

        const messages = await getMessagesSmart(clientGlobal, chat);
        if (!messages.length) continue;

        for (const msg of messages) {
          const ts = msgTsSec(msg);
          if (msg?.fromMe && ts > since) {
            sentMessages++;
            contactsActive.add(jid);
          }
        }
      }

      res.json({
        sent: sentMessages,
        contactsActive: contactsActive.size,
      });
    } catch (error) {
      res.status(500).json({ error: error?.message || String(error) });
    }
  });

  // =========================
  // /stats/response-rate
  // =========================
  router.get('/stats/response-rate', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

      const chats = await clientGlobal.listChats();
      if (!Array.isArray(chats) || chats.length === 0) {
        return res.json({ totalSent: 0, totalResponses: 0, rate: 0 });
      }

      const since = secsNDaysAgo(30);
      let totalSent = 0;
      let totalResponses = 0;

      for (const chat of chats) {
        const jid = jidFromChat(chat);
        if (!jid) continue;

        const messages = await getMessagesSmart(clientGlobal, chat);
        if (!messages.length) continue;

        messages.sort((a, b) => msgTsSec(a) - msgTsSec(b));

        for (let i = 0; i < messages.length; i++) {
          const msg = messages[i];
          const ts = msgTsSec(msg);

          if (msg?.fromMe && ts > since) {
            totalSent++;
            for (let j = i + 1; j < messages.length; j++) {
              const reply = messages[j];
              const tsReply = msgTsSec(reply);
              if (!reply?.fromMe && (tsReply - ts) < 86400) {
                totalResponses++;
                break;
              }
              if ((tsReply - ts) > 86400) break;
            }
          }
        }
      }

      const rate = totalSent > 0 ? Math.round((totalResponses / totalSent) * 100) : 0;
      res.json({ totalSent, totalResponses, rate });
    } catch (error) {
      res.status(500).json({ error: error?.message || String(error) });
    }
  });

  // =========================
  // /chats/recent
  // =========================
  router.get('/chats/recent', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

      const chats = await clientGlobal.listChats();
      if (!Array.isArray(chats) || chats.length === 0) {
        return res.json([]);
      }

      const recent = [];

      for (const chat of chats) {
        const parsed = parseJid(chat?.id);
        if (!parsed || parsed.server !== 'c.us' || !parsed.user) continue;

        // tenta pegar última do próprio chat (mais barato)
        let lastMsg = chat?.lastMessage || (Array.isArray(chat?.msgs) && chat.msgs.length ? chat.msgs[chat.msgs.length - 1] : null);

        if (!lastMsg) {
          const messages = await getMessagesSmart(clientGlobal, chat);
          if (!messages.length) continue;
          lastMsg = messages.reduce((acc, cur) => (msgTsSec(cur) >= msgTsSec(acc || cur) ? cur : acc), null);
        }

        const lastTs = lastMsg ? msgTsSec(lastMsg) : null;

        recent.push({
          name: chatDisplayName(chat, parsed.user),
          phone: parsed.user,
          lastMessage: lastMsg?.body || lastMsg?.content || null,
          timestamp: lastTs ? new Date(lastTs * 1000).toISOString() : null,
          unreadCount: chat?.unreadCount || 0,
        });
      }

      const sorted = recent
        .sort((a, b) => {
          const ta = a.timestamp ? new Date(a.timestamp).getTime() : 0;
          const tb = b.timestamp ? new Date(b.timestamp).getTime() : 0;
          return tb - ta;
        })
        .slice(0, 10);

      res.json(sorted);
    } catch (error) {
      res.status(500).json({ error: error?.message || String(error) });
    }
  });

  // =========================
  // Diagnóstico
  // =========================
  router.get('/diag/chats', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const chats = await clientGlobal.listChats();
      if (!Array.isArray(chats)) return res.json({ count: 0, items: [] });

      const items = (chats || []).slice(0, 20).map((c) => {
        const parsed = parseJid(c?.id);
        return {
          rawId: typeof c?.id === 'object' ? c.id._serialized || c.id : c?.id,
          parsed,
          name: chatDisplayName(c, parsed?.user),
          unreadCount: c?.unreadCount || 0,
          hasLastMessage: Boolean(c?.lastMessage),
          msgsLen: Array.isArray(c?.msgs) ? c.msgs.length : 0,
        };
      });
      res.json({ count: chats.length, items });
    } catch (error) {
      res.status(500).json({ error: error?.message || String(error) });
    }
  });

  // =========================
  // /activity/recent (stub)
  // =========================
  router.get('/activity/recent', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const logs = [];
      res.json(logs);
    } catch (error) {
      res.status(500).json({ error: error?.message || String(error) });
    }
  });

  // =========================
  // /account/info
  // =========================
  router.get('/account/info', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const info = await clientGlobal.getHostDevice();
      res.json({
        wid: info?.wid,
        pushname: info?.pushname,
        battery: info?.battery,
        plugged: info?.plugged,
        platform: info?.platform,
        device_manufacturer: info?.device_manufacturer,
        device_model: info?.device_model,
        os_version: info?.os_version,
      });
    } catch (error) {
      res.status(500).json({ error: error?.message || String(error) });
    }
  });

  // =========================
  // /contacts (somente c.us)
  // =========================
  router.get('/contacts', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const contacts = await clientGlobal.getAllContacts();
      if (!Array.isArray(contacts)) return res.json([]);

      const formatted = contacts
        .map((c) => {
          const parsed = parseJid(c?.id || c?._id);
          if (!parsed || parsed.server !== 'c.us' || !parsed.user) return null;
          return {
            name: c?.formattedName || c?.name || c?.pushname || parsed.user,
            phone: parsed.user,
            isBusiness: Boolean(c?.isBusiness),
          };
        })
        .filter(Boolean);

      res.json(formatted);
    } catch (error) {
      res.status(500).json({ error: error?.message || String(error) });
    }
  });

  // =========================
  // Outros stubs
  // =========================
  router.get('/campaigns', async (req, res) => {
    res.json([]);
  });

  router.get('/settings', async (req, res) => {
    res.json({
      notifications: {
        email: true,
        whatsapp: false,
        campaignAlerts: true,
        responseAlerts: true,
      },
      timezone: 'America/Sao_Paulo',
    });
  });

  router.get('/integrations', async (req, res) => {
    res.json({
      whatsapp: true,
      googleSheets: false,
      crm: false,
      customAPI: false,
    });
  });

  return router;
};
