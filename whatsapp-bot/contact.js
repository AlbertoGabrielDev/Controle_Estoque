// google-contacts.js (ou adicione ao seu botwhatsapp.js)

const fs = require('fs');
const { google } = require('googleapis');
const express = require('express');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const SCOPES = ['https://www.googleapis.com/auth/contacts.readonly'];
const CREDENTIALS_PATH = './credentials.json'; // coloque o caminho certo

// Função para obter OAuth2 client
//para funcionar, tem que pegar as credenciais do Google Cloud Console usando o OAuth2 desktop app
function getOAuth2Client() {
    const credentials = JSON.parse(fs.readFileSync(CREDENTIALS_PATH));
    const { client_id, client_secret, redirect_uris } = credentials.installed || credentials.web;
    return new google.auth.OAuth2(client_id, client_secret, redirect_uris[0]);
}

// Endpoint para iniciar login (primeira vez)
app.get('/google-auth', (req, res) => {
    const oAuth2Client = getOAuth2Client();
    const url = oAuth2Client.generateAuthUrl({
        access_type: 'offline',
        scope: SCOPES,
    });
    res.json({ url });
});

// Endpoint para receber o código e salvar tokens
app.get('/google-auth/callback', async (req, res) => {
    const code = req.query.code;
    const oAuth2Client = getOAuth2Client();
    const { tokens } = await oAuth2Client.getToken(code);
    fs.writeFileSync('token.json', JSON.stringify(tokens));
    res.send('Autenticado! Pode fechar esta janela.');
});

// Endpoint para buscar os contatos da conta
app.get('/google-contacts', async (req, res) => {
    try {
        const oAuth2Client = getOAuth2Client();
        const tokens = JSON.parse(fs.readFileSync('token.json'));
        oAuth2Client.setCredentials(tokens);

        const people = google.people({ version: 'v1', auth: oAuth2Client });
        const result = await people.people.connections.list({
            resourceName: 'people/me',
            pageSize: 1000,
            personFields: 'names,phoneNumbers',
        });

        // Formata para [{ name, phone }]
        const contacts = (result.data.connections || [])
            .filter(p => p.names && p.phoneNumbers)
            .map(p => ({
                name: p.names[0].displayName,
                phone: p.phoneNumbers[0].value,
            }));

        res.json(contacts);
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Você pode adicionar este app.listen() no seu bot principal, ou exportar como router!
app.listen(3002, () => console.log('API Google Contacts rodando na porta 3002'));
