const express = require('express');
const router = express.Router();

module.exports = (clientGlobal) => {

  function dateNDaysAgo(days) {
    const d = new Date();
    d.setDate(d.getDate() - days);
    return d;
  }

  router.get('/stats/sent-messages', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

      const chats = await clientGlobal.listChats();
      let sentMessages = 0;
      let contactsActive = new Set();

      for (const chat of chats) {
        const messages = await clientGlobal.getAllMessagesInChat(chat.id.user + '@c.us', true, false);
        for (const msg of messages) {
          if (msg.fromMe && new Date(msg.t * 1000) > dateNDaysAgo(30)) {
            sentMessages++;
            contactsActive.add(chat.id.user + '@c.us');
          }
        }
      }

      res.json({
        sent: sentMessages,
        contactsActive: contactsActive.size
      });
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  router.get('/stats/response-rate', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

      const chats = await clientGlobal.listChats();
      let totalSent = 0;
      let totalResponses = 0;

      for (const chat of chats) {
        const messages = await clientGlobal.getAllMessagesInChat(chat.id.user + '@c.us', true, false);
        messages.sort((a, b) => a.t - b.t);

        for (let i = 0; i < messages.length; i++) {
          const msg = messages[i];
          if (msg.fromMe && new Date(msg.t * 1000) > dateNDaysAgo(30)) {
            totalSent++;
            for (let j = i + 1; j < messages.length; j++) {
              const reply = messages[j];
              if (!reply.fromMe && (reply.t - msg.t) < 86400) {
                totalResponses++;
                break;
              }
              if ((reply.t - msg.t) > 86400) break;
            }
          }
        }
      }

      const rate = totalSent > 0 ? Math.round((totalResponses / totalSent) * 100) : 0;

      res.json({
        totalSent,
        totalResponses,
        rate
      });
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  router.get('/chats/recent', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const chats = await clientGlobal.listChats();
      const sorted = chats.sort((a, b) => b.timestamp - a.timestamp).slice(0, 10);

      const recent = [];
      for (const chat of sorted) {
        const messages = await clientGlobal.getAllMessagesInChat(chat.id.user + '@c.us', true, false);
        const lastMsg = messages[messages.length - 1];
        recent.push({
          name: chat.formattedTitle || chat.name || chat.id.user,
          phone: chat.id.user,
          lastMessage: lastMsg ? lastMsg.body : null,
          timestamp: lastMsg ? new Date(lastMsg.t * 1000).toISOString() : null,
          unreadCount: chat.unreadCount || 0
        });
      }
      res.json(recent);
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  router.get('/activity/recent', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const logs = [];
      res.json(logs);
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  router.get('/account/info', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const info = await clientGlobal.getHostDevice();
      res.json({
        wid: info.wid,
        pushname: info.pushname,
        battery: info.battery,
        plugged: info.plugged,
        platform: info.platform,
        device_manufacturer: info.device_manufacturer,
        device_model: info.device_model,
        os_version: info.os_version
      });
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  router.get('/contacts', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const contacts = await clientGlobal.getAllContacts();
      const formatted = contacts.map(c => ({
        name: c.formattedName || c.name || c.pushname || c.id.user,
        phone: c.id.user,
        isBusiness: c.isBusiness || false
      }));
      res.json(formatted);
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  router.get('/campaigns', async (req, res) => {
    res.json([]);
  });

  router.get('/settings', async (req, res) => {
    res.json({
      notifications: {
        email: true,
        whatsapp: false,
        campaignAlerts: true,
        responseAlerts: true
      },
      timezone: 'America/Sao_Paulo'
    });
  });

  router.get('/integrations', async (req, res) => {
    res.json({
      whatsapp: true,
      googleSheets: false,
      crm: false,
      customAPI: false
    });
  });

  return router;
};
