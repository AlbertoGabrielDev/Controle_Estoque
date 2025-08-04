// whatsapp-bot/botwhatsapp.js

const express = require('express');
const wppconnect = require('@wppconnect-team/wppconnect');
const cors = require('cors');
const setupSettingsRoutes = require('./settings');
const dashboardRoutes = require('./dashboard'); // <- já aponta para o arquivo dashboard.js

const app = express();

app.use(cors());
app.use(express.json());

let qrCodeBase64 = null;
let isConnected = false;
let clientGlobal = null;

// Inicializa o WPPConnect
wppconnect
  .create({
    session: 'sessionName',
    catchQR: (base64Qr, asciiQR) => {
      qrCodeBase64 = base64Qr;
      isConnected = false;
    },
    statusFind: (status, session) => {
      if (status === 'isLogged') {
        isConnected = true;
        qrCodeBase64 = null;
      }
    },
    logQR: false,
  })
  .then((client) => {
    clientGlobal = client;

    // Aqui adiciona as rotas que dependem do client conectado
    app.use('/dashboard', dashboardRoutes(clientGlobal)); // <-- importante: só aqui!

    setupSettingsRoutes(app, clientGlobal);

    console.log('Bot WhatsApp iniciado!');
  })
  .catch(console.log);

// Endpoint para pegar o QR Code
app.get('/verdurao/bot/whatsapp/qrcode', (req, res) => {
  if (isConnected) {
    return res.json({ connected: true });
  }
  if (!qrCodeBase64) {
    return res.json({ connected: false, qrcode: null });
  }
  res.json({ connected: false, qrcode: qrCodeBase64 });
});

// Endpoint para envio em massa
app.post('/verdurao/bot/whatsapp/send-mass', async (req, res) => {
  const { contacts, message } = req.body;
  if (!clientGlobal || !Array.isArray(contacts) || !message) {
    return res.status(400).json({ error: 'Client não conectado ou dados inválidos.' });
  }
  const results = [];
  for (const contact of contacts) {
    const number = contact.phone.replace(/\D/g, '');
    let fullNumber = number;
    if (fullNumber.length <= 13 && !fullNumber.endsWith('@c.us')) {
      fullNumber = `${fullNumber}@c.us`;
    }
    const finalMessage = message
      .replace(/{nome}/g, contact.name)
      .replace(/{telefone}/g, contact.phone);
    try {
      await clientGlobal.sendText(fullNumber, finalMessage);
      results.push({ phone: contact.phone, status: 'enviado' });
    } catch (err) {
      results.push({ phone: contact.phone, status: 'erro', error: err.message });
    }
  }
  res.json(results);
});

// (Opcional) endpoint para envio agendado, se você implementar!
app.post('/verdurao/bot/whatsapp/send-scheduled', async (req, res) => {
  // TODO: Adicione sua lógica de agendamento se quiser!
  // Apenas para evitar erro 404 no Dashboard.vue
  res.json({ status: 'agendado' });
});

// Inicia o servidor Node na porta 3001
app.listen(3001, () => console.log('API do WhatsApp rodando na porta 3001'));
