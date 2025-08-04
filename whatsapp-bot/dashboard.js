// routes/dashboard.js
const express = require('express');
const router = express.Router();

/**
 * Espera receber como parâmetro o client do wppconnect já conectado.
 * Exemplo de uso no seu app:
 *   const dashboardRoutes = require('./routes/dashboard');
 *   app.use('/dashboard', dashboardRoutes(clientGlobal));
 */

module.exports = (clientGlobal) => {

  // Utilitário para data 30 dias atrás
  function dateNDaysAgo(days) {
    const d = new Date();
    d.setDate(d.getDate() - days);
    return d;
  }

  // 1. Mensagens enviadas nos últimos 30 dias
  router.get('/stats/sent-messages', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

      // Busca mensagens enviadas pelo número conectado nos últimos 30 dias
      const chats = await clientGlobal.listChats();
      let sentMessages = 0;
      let contactsActive = new Set();

      for (const chat of chats) {
        // Busca histórico de mensagens só do próprio número (fromMe)
        const messages = await clientGlobal.loadAndGetAllMessagesInChat(chat.id.user + '@c.us', true, false);
        for (const msg of messages) {
          if (msg.fromMe && new Date(msg.t) > dateNDaysAgo(30)) {
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

  // 2. Taxa de resposta (mensagens recebidas em até 24h após o envio)
  router.get('/stats/response-rate', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

      // Para cada chat, calcula quantas respostas ocorreram até 24h após mensagem enviada
      const chats = await clientGlobal.listChats();
      let totalSent = 0;
      let totalResponses = 0;

      for (const chat of chats) {
        const messages = await clientGlobal.loadAndGetAllMessagesInChat(chat.id.user + '@c.us', true, false);
        // Ordena por data
        messages.sort((a, b) => a.t - b.t);

        for (let i = 0; i < messages.length; i++) {
          const msg = messages[i];
          // Só mensagens enviadas pelo bot
          if (msg.fromMe && new Date(msg.t) > dateNDaysAgo(30)) {
            totalSent++;
            // Procura por resposta do contato até 24h após essa mensagem
            for (let j = i + 1; j < messages.length; j++) {
              const reply = messages[j];
              if (!reply.fromMe && (reply.t - msg.t) < 86400) { // 86400 segundos = 24h
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

  // 3. Conversas recentes (últimas 10 conversas)
  router.get('/chats/recent', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const chats = await clientGlobal.listChats();
      // Ordena por último msg
      const sorted = chats.sort((a, b) => b.timestamp - a.timestamp).slice(0, 10);

      // Busca detalhes das últimas mensagens
      const recent = [];
      for (const chat of sorted) {
        const messages = await clientGlobal.loadAndGetAllMessagesInChat(chat.id.user + '@c.us', true, false);
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

  // 4. Atividade recente (histórico das ações do bot)
  router.get('/activity/recent', async (req, res) => {
    // Você pode armazenar logs de ações no backend e retornar aqui.
    // Se não existe log no banco/local, pode retornar últimos envios/agendamentos (exemplo abaixo)
    // TODO: Substituir por seu repositório de logs caso exista
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      // Exemplo: últimos envios agendados/campanhas (busca num arquivo/camapanhas.json, etc)
      // Aqui é só um placeholder, ajuste para sua lógica real
      const logs = []; // Carregue do seu storage
      res.json(logs);
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  // 5. Dados do número conectado
  router.get('/account/info', async (req, res) => {
    try {
      if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
      const info = await clientGlobal.getHostDevice();
      res.json({
        wid: info.wid, // número conectado
        pushname: info.pushname, // nome do perfil
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

  // 6. Contatos reais (retorna todos os contatos salvos)
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

  // 7. Campanhas recentes/agendadas
  router.get('/campaigns', async (req, res) => {
    // Carregue de um storage real, ex: banco/csv/json
    // Placeholder: []
    res.json([]); // Implemente usando seu banco/arquivo de campanhas
  });

  // 8. Preferências/configurações (você pode salvar no backend)
  router.get('/settings', async (req, res) => {
    // Exemplo: carregue preferências do usuário do storage/backend
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

  // 9. Integrações (status real do seu sistema)
  router.get('/integrations', async (req, res) => {
    // Retorne status das integrações ativas do backend
    res.json({
      whatsapp: true,
      googleSheets: false,
      crm: false,
      customAPI: false
    });
  });

  return router;
};
