// whatsapp-bot/labels.js
module.exports = function setupLabelRoutes(app, clientGlobal, log = console.log) {

    const dbg = (type, msg, meta) => {
        if (String(process.env.DEBUG_CONTACTS || '').toLowerCase() === '1') {
            log(type, msg, meta || {});
        }
    };
    const info = (msg, meta) => dbg('info', msg, meta);
    const warn = (msg, meta) => dbg('warn', msg, meta);
    const error = (msg, meta) => dbg('error', msg, meta);

    const hasFn = (obj, name) => obj && typeof obj[name] === 'function';
    const onlyDigits = (p) => String(p || '').replace(/\D/g, '');

    function parseJid(anyId) {
        try {
            let s = '';
            if (!anyId) return null;
            if (typeof anyId === 'string') s = anyId;
            else if (typeof anyId === 'object') {
                if (anyId._serialized) s = anyId._serialized;
                else if (anyId.user && anyId.server) s = `${anyId.user}@${anyId.server}`;
                else if (anyId.id) s = String(anyId.id);
            }
            const m = s.match(/^(.+?)@([a-z0-9.\-]+)$/i);
            if (!m) return null;
            const user = onlyDigits(m[1]);
            const server = m[2];
            return user && server ? { user, server, jid: `${user}@${server}` } : null;
        } catch {
            return null;
        }
    }

    const phonesToJids = (arr) =>
        (arr || []).map(onlyDigits).filter(Boolean).map((p) => `${p}@c.us`);

    async function firstAvailable(client, tries) {
        for (const [name, call] of tries) {
            if (!hasFn(client, name)) continue;
            try {
                const r = await call();
                if (r !== undefined && r !== null) return r;
            } catch (e) {
                warn(`${name} falhou`, { err: String(e) });
            }
        }
        return null;
    }

    const listChatsSmart = (client) =>
        firstAvailable(client, [
            ['listChats', () => client.listChats()],
            ['getAllChats', () => client.getAllChats()],
        ]) || [];

    const getMessagesSmart = (client, jid) =>
        firstAvailable(client, [
            ['loadAndGetAllMessagesInChat', () => client.loadAndGetAllMessagesInChat(jid, true, false)],
            ['getAllMessagesInChat', () => client.getAllMessagesInChat(jid, true, false)],
            ['getMessages', () => client.getMessages(jid, { count: 50, direction: 'backward' })],
        ]) || [];

    app.get('/verdurao/bot/whatsapp/diag/ping', (_req, res) => {
        info('diag/ping');
        res.json({ ok: true, ts: new Date().toISOString() });
    });

    app.get('/verdurao/bot/whatsapp/diag/summary', async (_req, res) => {
        info('diag/summary');
        try {
            if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

            const supported = {};
            ['listChats', 'getAllChats', 'getAllContacts', 'loadAndGetAllMessagesInChat', 'getAllMessagesInChat', 'getMessages',
                'getAllLabels', 'addNewLabel', 'addOrRemoveLabels', 'deleteLabel'
            ].forEach((k) => (supported[k] = hasFn(clientGlobal, k)));

            const [chats, saved] = await Promise.all([
                listChatsSmart(clientGlobal),
                hasFn(clientGlobal, 'getAllContacts') ? clientGlobal.getAllContacts() : [],
            ]);

            res.json({
                connected: true,
                supported,
                counts: { chats: Array.isArray(chats) ? chats.length : 0, saved: Array.isArray(saved) ? saved.length : 0 },
            });
        } catch (e) {
            error('diag/summary error', { err: String(e) });
            res.status(500).json({ error: e?.message || String(e) });
        }
    });

    app.get('/verdurao/bot/whatsapp/labels', async (_req, res) => {
        info('labels/list');
        try {
            if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
            if (!hasFn(clientGlobal, 'getAllLabels')) return res.json([]);
            const labels = await clientGlobal.getAllLabels();
            res.json(Array.isArray(labels) ? labels : []);
        } catch (e) {
            error('labels/list error', { err: String(e) });
            res.status(500).json({ error: e?.message || String(e) });
        }
    });

    app.post('/verdurao/bot/whatsapp/labels', async (req, res) => {
        info('labels/create');
        try {
            if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
            const { name, labelColor } = req.body || {};
            if (!name) return res.status(400).json({ error: 'name é obrigatório' });
            if (!hasFn(clientGlobal, 'addNewLabel')) return res.status(501).json({ error: 'addNewLabel não suportado' });
            const result = await clientGlobal.addNewLabel(name, labelColor ? { labelColor } : undefined);
            res.json({ ok: true, result });
        } catch (e) {
            error('labels/create error', { err: String(e) });
            res.status(500).json({ error: e?.message || String(e) });
        }
    });

    app.post('/verdurao/bot/whatsapp/labels/assign', async (req, res) => {
        info('labels/assign');
        try {
            if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
            if (!hasFn(clientGlobal, 'addOrRemoveLabels')) return res.status(501).json({ error: 'addOrRemoveLabels não suportado' });

            const { labelId, type, phones } = req.body || {};
            if (!labelId || !['add', 'remove'].includes(type)) {
                return res.status(400).json({ error: 'labelId e type(add|remove) são obrigatórios' });
            }
            const chatIds = phonesToJids(phones);
            if (!chatIds.length) return res.status(400).json({ error: 'phones vazio' });

            const ops = [{ labelId: String(labelId), type }];
            const result = await clientGlobal.addOrRemoveLabels(chatIds, ops);
            res.json({ ok: true, result });
        } catch (e) {
            error('labels/assign error', { err: String(e) });
            res.status(500).json({ error: e?.message || String(e) });
        }
    });

    app.delete('/verdurao/bot/whatsapp/labels/:id', async (req, res) => {
        info('labels/delete', { id: req.params.id });
        try {
            if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });
            const id = String(req.params.id || '');
            if (!id) return res.status(400).json({ error: 'id obrigatório' });

            if (hasFn(clientGlobal, 'deleteLabel')) {
                const r = await clientGlobal.deleteLabel(id);
                return res.json({ ok: true, result: r });
            }
            return res.status(501).json({ error: 'deleteLabel não suportado na sua versão' });
        } catch (e) {
            error('labels/delete error', { err: String(e) });
            res.status(500).json({ error: e?.message || String(e) });
        }
    });

    app.get('/verdurao/bot/whatsapp/contacts-with-dialog', async (_req, res) => {
        info('contacts-with-dialog');
        try {
            if (!clientGlobal) return res.status(500).json({ error: 'Cliente não conectado' });

            const outMap = new Map(); // phone -> { name, phone, saved, jid }

            // 1) Contatos salvos (agenda)
            if (hasFn(clientGlobal, 'getAllContacts')) {
                const saved = await clientGlobal.getAllContacts();
                if (Array.isArray(saved)) {
                    for (const c of saved) {
                        const parsed = parseJid(c?.id || c?._id);
                        if (!parsed || parsed.server !== 'c.us') continue;
                        const phone = parsed.user;
                        const name = c?.formattedName || c?.name || c?.pushname || phone;
                        outMap.set(phone, { name, phone, saved: true, jid: parsed.jid });
                    }
                }
            }

            const chats = await listChatsSmart(clientGlobal);
            if (Array.isArray(chats)) {
                for (const ch of chats) {
                    const parsed = parseJid(ch?.id);
                    if (!parsed || parsed.server !== 'c.us') continue;
                    const phone = parsed.user;
                    if (!outMap.has(phone)) {
                        const name = ch?.formattedTitle || ch?.name || ch?.contact?.name || ch?.contact?.pushname || phone;
                        outMap.set(phone, { name, phone, saved: false, jid: parsed.jid });
                    }
                }
            }

            if (outMap.size === 0 && hasFn(clientGlobal, 'getAllContacts')) {
                const saved = await clientGlobal.getAllContacts();
                if (Array.isArray(saved)) {
                    for (const c of saved) {
                        const parsed = parseJid(c?.id || c?._id);
                        if (!parsed || parsed.server !== 'c.us') continue;
                        const msgs = await getMessagesSmart(clientGlobal, parsed.jid);
                        if (Array.isArray(msgs) && msgs.length) {
                            const phone = parsed.user;
                            const name = c?.formattedName || c?.name || c?.pushname || phone;
                            outMap.set(phone, { name, phone, saved: true, jid: parsed.jid });
                        }
                    }
                }
            }
            const out = Array.from(outMap.values()).sort((a, b) => a.name.localeCompare(b.name, 'pt-BR'));
            res.json(out);
        } catch (e) {
            error('contacts-with-dialog error', { err: String(e) });
            res.status(500).json({ error: e?.message || String(e) });
        }
    });
};
