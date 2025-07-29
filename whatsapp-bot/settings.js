// whatsapp-bot/settings.js
// settings.js
const express = require('express');
const schedule = require('node-schedule');

function setupSettingsRoutes(app, clientGlobal) {
    app.post('/verdurao/bot/whatsapp/send-scheduled', async (req, res) => {
        const { contacts, message, intervalSeconds, scheduledTime } = req.body;

        if (!clientGlobal || !Array.isArray(contacts) || !message) {
            return res.status(400).json({ error: 'Dados inválidos ou client não conectado.' });
        }

        const sendAllMessages = async () => {
            const results = [];
            for (let i = 0; i < contacts.length; i++) {
                const contact = contacts[i];
                const number = contact.phone.replace(/\D/g, '') + '@c.us';
                const personalized = message
                    .replace(/{nome}/g, contact.name)
                    .replace(/{telefone}/g, contact.phone);

                try {
                    await clientGlobal.sendText(number, personalized);
                    results.push({ phone: contact.phone, status: 'enviado' });
                } catch (err) {
                    results.push({ phone: contact.phone, status: 'erro', error: err.message });
                }

                if (i < contacts.length - 1) {
                    await new Promise(resolve => setTimeout(resolve, intervalSeconds * 1000));
                }
            }

            console.log('Envio finalizado:', results.length);
        };

        if (scheduledTime) {
            schedule.scheduleJob(new Date(scheduledTime), sendAllMessages);
            return res.json({ status: 'agendado', total: contacts.length, scheduledTime });
        } else {
            sendAllMessages();
            return res.json({ status: 'enviando', total: contacts.length });
        }
    });
}

module.exports = setupSettingsRoutes;

