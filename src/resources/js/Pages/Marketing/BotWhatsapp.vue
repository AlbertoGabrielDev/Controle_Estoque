<template>
        <div class="flex justify-center p-4 bg-gray-100 min-h-screen">
            <div class="bg-white rounded-lg shadow-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-screen-xl w-full">
                <div class="border rounded-lg p-4">
                    <div class="p-4">
                        <!-- Botões para mudar a fonte -->
                        <div class="flex gap-2 mb-4">
                            <button @click="changeSource('whatsapp')"
                                :class="sourceType === 'whatsapp' ? 'bg-blue-500 text-white px-3 py-1 rounded' : 'px-3 py-1 border rounded'">
                                Contatos do WhatsApp
                            </button>
                            <button @click="changeSource('google')"
                                :class="sourceType === 'google' ? 'bg-blue-500 text-white px-3 py-1 rounded' : 'px-3 py-1 border rounded'">
                                Contatos do Google
                            </button>
                        </div>

                        <!-- Aqui o resto da sua tela de contatos -->
                    </div>
                    <h2 class="text-lg font-semibold mb-2">👥 Contatos para Envio</h2>
                    <div class="text-sm mb-2 text-gray-600">
                        Lista de Contatos
                        <span class="float-right font-medium">
                            Selecionados: {{ selectedContacts.size }} / {{ contacts.length }}
                        </span>
                    </div>
                    <input v-model="searchQuery" type="text" placeholder="🔍 Buscar por nome ou número"
                        class="border p-1 rounded mb-2 w-full text-sm" />
                    <div v-if="loadingContacts" class="text-center text-blue-600 py-2">
                        Carregando contatos...
                    </div>

                    <div v-else class="border rounded p-2 mb-4 h-40 overflow-y-auto">
                        <div v-for="(contact, index) in filteredContacts" :key="index"
                            class="flex justify-between items-center border-b py-1">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" :value="contact" :checked="selectedContacts.has(contact)"
                                    @change="toggleSelection(contact)" />
                                <div>
                                    <div class="font-semibold">
                                        {{ contact.name }}
                                        <span v-if="!contact.saved"
                                            class="ml-2 text-[10px] px-1 py-0.5 rounded bg-red-100 text-red-700">Não
                                            salvo</span>
                                    </div>
                                    <div class="text-gray-600 text-sm">{{ contact.phone }}</div>
                                </div>
                            </div>
                            <button @click="removeContact(index)" class="text-red-500">🗑️</button>
                        </div>
                        <div v-if="!filteredContacts.length" class="text-gray-500 text-center py-3">
                            Nenhum contato encontrado.
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Adicionar Manualmente</label>
                        <div class="flex gap-2">
                            <input v-model="manualName" type="text" placeholder="Nome"
                                class="border rounded p-1 flex-1 text-sm" />
                            <input v-model="manualPhone" type="text" placeholder="Número contato"
                                class="border rounded p-1 flex-1 text-sm" />
                        </div>
                        <button @click="addManualContact" class="bg-blue-600 text-white px-3 rounded mt-2">
                            Adicionar
                        </button>
                    </div>

                    <button class="bg-green-600 text-white px-4 py-2 rounded mt-4" @click="showQrModal = true">
                        Conectar WhatsApp
                    </button>

                    <!-- Modal QR -->
                    <div v-if="showQrModal"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white p-6 rounded-lg flex flex-col items-center relative">
                            <button class="absolute top-2 right-2 text-gray-600" @click="showQrModal = false">X</button>
                            <h2 class="text-xl font-semibold mb-3">Escaneie o QRCode</h2>
                            <div v-if="isConnected" class="text-green-600 font-semibold">Conectado!</div>
                            <div v-else-if="qrcode">
                                <img :src="qrcode" class="w-72 h-72 mb-3" />
                                <div class="text-gray-600 text-sm">Aponte a câmera do WhatsApp para o QRCode acima</div>
                            </div>
                            <div v-else>
                                <span class="text-gray-500">Aguardando QRCode...</span>
                            </div>
                            <button @click="getQrCode" class="mt-4 px-3 py-1 rounded bg-blue-500 text-white">
                                Atualizar QRCode
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mensagem -->
                <div class="border rounded-lg p-4">
                    <label class="block text-sm font-medium mb-1">Modelo de Mensagem</label>
                    <div class="relative group">
                        <select v-model="selectedTemplateId" @change="applyTemplate"
                            class="border rounded px-3 py-2 w-full text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition outline-none bg-white shadow-sm hover:border-blue-400 cursor-pointer">
                            <option value="" disabled>Selecionar modelo salvo</option>
                            <option v-for="tpl in templates" :key="tpl.id" :value="tpl.id"
                                @mouseenter="showPreview(tpl)" @mouseleave="hidePreview">
                                {{ tpl.name }}
                            </option>
                        </select>
                        <transition name="fade">
                            <div v-if="previewTemplate"
                                class="absolute left-0 mt-1 z-10 bg-white border border-gray-300 rounded shadow-lg p-3 text-xs w-72"
                                style="pointer-events: none;">
                                <div class="font-semibold mb-1">{{ previewTemplate.name }}</div>
                                <div class="text-gray-700 whitespace-pre-line">{{ previewTemplate.body }}</div>
                            </div>
                        </transition>
                    </div>

                    <h3 class="text-sm font-medium mb-2">Texto da Mensagem</h3>

                    <div class="flex gap-1 mb-2">
                        <button class="p-1 border rounded hover:bg-gray-100">🙂</button>
                        <button class="p-1 border rounded hover:bg-gray-100">📎</button>
                        <button class="p-1 border rounded hover:bg-gray-100">📄</button>
                    </div>

                    <div class="mb-2">
                        <div class="text-xs text-gray-500 mb-1">Variáveis:</div>
                        <div class="flex gap-1">
                            <span class="bg-gray-200 px-2 py-1 rounded cursor-pointer hover:bg-gray-300 text-xs"
                                @click="addVariable('{nome}')">{nome}</span>
                            <span class="bg-gray-200 px-2 py-1 rounded cursor-pointer hover:bg-gray-300 text-xs"
                                @click="addVariable('{telefone}')">{telefone}</span>
                        </div>
                    </div>

                    <textarea v-model="message" class="border rounded p-2 w-full text-sm h-32 mb-4"
                        ref="messageTextarea"></textarea>

                    <div class="border rounded p-3 text-sm bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <div class="font-medium">Prévia</div>
                            <button class="text-blue-600 text-sm" @click="noop">Atualizar prévia</button>
                        </div>
                        <div class="bg-white p-3 rounded border mb-3">
                            <div v-html="previewMessage"></div>
                        </div>
                        <div class="flex gap-2">
                            <button class="flex-1 bg-blue-600 text-white px-4 py-1.5 rounded text-sm"
                                :disabled="!manualName || !manualPhone" @click="sendTestMessage">
                                Testar mensagem
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Configurações -->
                <div class="border rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">⚙️ Configurações de Envio</h2>

                    <label class="block text-sm font-medium mb-1">Intervalo entre mensagens (em segundos)</label>
                    <input v-model.number="intervalSeconds" type="number" min="1"
                        class="border rounded p-1 w-full text-sm mb-2" placeholder="Ex: 60" />
                    <div class="text-xs text-gray-500 mb-2">Recomendado: mínimo 30s para evitar bloqueios.</div>

                    <label class="block text-sm font-medium mb-1">Agendar horário de envio (opcional)</label>
                    <input v-model="scheduledTime" type="datetime-local"
                        class="border rounded p-1 w-full text-sm mb-2" />

                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" v-model="randomInterval" class="accent-gray-700" />
                        <label class="text-sm">Intervalo aleatório</label>
                    </div>

                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" v-model="simulateTyping" class="accent-green-600" />
                        <label class="text-sm">Simular digitação</label>
                    </div>

                    <div class="text-sm font-medium mb-1">Resumo</div>
                    <div class="border rounded p-2 text-sm mb-2">
                        <div class="text-yellow-600 font-semibold mb-1">Aguardando envio</div>
                        <div>Total de contatos: <span class="float-right">{{ selectedContacts.size }}</span></div>
                        <div>Intervalo: <span class="float-right">{{ intervalSeconds }} segundos</span></div>
                        <div>Tempo estimado: <span class="float-right">{{ estimatedTime }} minutos</span></div>
                        <div v-if="scheduledTime">Agendado para:
                            <span class="float-right">{{ formatScheduledTime }}</span>
                        </div>
                    </div>
                    <button class="bg-green-600 text-white w-full py-2 rounded mt-2" @click="sendMassMessage">
                        ▶️ Iniciar Campanha
                    </button>
                    <div class="mt-4">
                        <div v-if="feedback" :class="feedbackType === 'error' ? 'text-red-600' : 'text-green-700'">{{
                            feedback }}</div>
                    </div>
                    <div class="mt-4">
                        <button class="text-sm underline" @click="loadLogs">Ver últimos logs</button>
                        <div class="mt-2 h-40 overflow-y-auto border rounded p-2 text-xs bg-gray-50">
                            <div v-for="(l, i) in logs" :key="i">
                                <strong>[{{ l.type }}]</strong> {{ l.ts }} — {{ l.message }}
                                <span v-if="l.meta && Object.keys(l.meta).length"> • {{ JSON.stringify(l.meta) }}</span>
                            </div>
                            <div v-if="!logs.length" class="text-gray-500">Sem logs ainda.</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

</template>

<script>


const BASE_URL = import.meta.env.VITE_WHATSAPP_API_URL || 'http://localhost:3001';

export default {
    props: {
        templates: { type: Array, default: () => [] }
    },
  
    data() {
        return {
            activeTab: null,
            contacts: [],
            manualName: '',
            manualPhone: '',
            message: 'Olá {nome}!\nTemos uma oferta especial para você!',
            feedback: '',
            feedbackType: 'success',
            showQrModal: false,
            qrcode: null,
            isConnected: false,
            loadingContacts: false,
            searchQuery: '',
            selectedContacts: new Set(),
            intervalSeconds: 60,
            scheduledTime: '',
            randomInterval: false,
            simulateTyping: false,
            selectedTemplateId: '',
            previewTemplate: null,
            logs: [],
            sourceType: 'whatsapp',
        }
    },
    mounted() {
        this.loadContactsFromWhatsapp();
    },
    watch: {
        showQrModal(val) { if (val) this.getQrCode(); }
    },
    computed: {
        previewMessage() {
            const name = this.manualName || (this.contacts[0] && this.contacts[0].name) || 'Nome';
            const phone = this.manualPhone || (this.contacts[0] && this.contacts[0].phone) || 'Telefone';
            return this.message.replace(/{nome}/g, name).replace(/{telefone}/g, phone).replace(/\n/g, '<br />');
        },
        filteredContacts() {
            const q = this.searchQuery.toLowerCase();
            return this.contacts.filter(c => (c.name || '').toLowerCase().includes(q) || (c.phone || '').toLowerCase().includes(q));
        },
        estimatedTime() {
            const total = this.selectedContacts.size || 0;
            return ((total * this.intervalSeconds) / 60).toFixed(1);
        },
        formatScheduledTime() {
            return this.scheduledTime ? new Date(this.scheduledTime).toLocaleString() : '';
        }
    },
    methods: {
        async loadContactsFromWhatsapp() {
            this.loadingContacts = true;
            try {
                const r = await fetch(`${BASE_URL}/verdurao/bot/whatsapp/all-contacts`);
                const data = await r.json();
                if (Array.isArray(data)) this.contacts = data;
            } catch {
                this.feedback = 'Erro ao carregar contatos do WhatsApp.';
                this.feedbackType = 'error';
            }
            this.loadingContacts = false;
        },
        async loadContactsFromGoogle() {
            this.loadingContacts = true;
            try {
                const r = await fetch(`${BASE_URL}/verdurao/bot/whatsapp/google-contacts`);
                if (r.status === 401) {
                    const j = await r.json();
                    this.feedback = 'Você precisa autorizar o acesso ao Google Contacts.';
                    this.feedbackType = 'error';
                    // Abre a janela de autorização (pop-up)
                    const url = j?.url;
                    if (url) {
                        window.open(url, '_blank', 'width=500,height=700');
                    } else {
                        const r2 = await fetch(`${BASE_URL}/verdurao/bot/whatsapp/google-auth`);
                        const j2 = await r2.json();
                        if (j2?.url) window.open(j2.url, '_blank', 'width=500,height=700');
                    }
                    this.feedback = 'Autorize no Google e depois clique novamente em "Contatos do Google".';
                    this.feedbackType = 'success';
                    this.contacts = [];
                } else {
                    const data = await r.json();
                    if (Array.isArray(data)) {
                        this.contacts = data;
                        this.feedback = '';
                    } else if (data?.error) {
                        this.feedback = `Erro ao carregar contatos do Google: ${data.error.error_description || data.error}`;
                        this.feedbackType = 'error';
                        this.contacts = [];
                    }
                }
            } catch (e) {
                this.feedback = 'Erro ao carregar contatos do Google.';
                this.feedbackType = 'error';
                this.contacts = [];
            }
            this.loadingContacts = false;
        },
        changeSource(type) {
            this.sourceType = type;
            if (type === 'whatsapp') {
                this.loadContactsFromWhatsapp();
            } else {
                this.loadContactsFromGoogle();
            }
        },
        noop() { },
        async loadAllContacts() {
            this.loadingContacts = true;
            try {
                const r = await fetch(`${BASE_URL}/verdurao/bot/whatsapp/all-contacts`);
                const data = await r.json();
                if (Array.isArray(data)) this.contacts = data;
            } catch (e) {
                this.feedback = 'Não foi possível carregar contatos do WhatsApp.';
                this.feedbackType = 'error';
            }
            this.loadingContacts = false;
        },
        async loadLogs() {
            try {
                const r = await fetch(`${BASE_URL}/verdurao/bot/whatsapp/logs?limit=200`);
                this.logs = await r.json();
            } catch (e) {
                this.logs = [];
            }
        },
        async getQrCode() {
            this.qrcode = null; this.isConnected = false;
            try {
                const response = await fetch(`${BASE_URL}/verdurao/bot/whatsapp/qrcode`);
                const data = await response.json();
                if (data.connected) { this.isConnected = true; this.qrcode = null; }
                else { this.qrcode = data.qrcode; this.isConnected = false; }
            } catch (err) { this.qrcode = null; this.isConnected = false; }
        },
        removeContact(index) { this.contacts.splice(index, 1); },
        addManualContact() {
            if (!this.manualName.trim() || !this.manualPhone.trim()) return;
            this.contacts.push({ name: this.manualName, phone: this.manualPhone, saved: false });
            this.manualName = ''; this.manualPhone = '';
        },
        addVariable(variable) {
            this.message += variable;
            this.$nextTick(() => { this.$refs.messageTextarea?.focus(); });
        },
        async sendTestMessage() {
            if (!this.manualName || !this.manualPhone) return;
            this.selectedContacts = new Set([{ name: this.manualName, phone: this.manualPhone }]);
            await this.sendMassMessage();
        },
        async sendMassMessage() {
            const selected = Array.from(this.selectedContacts);
            if (!selected.length) {
                this.feedback = "Selecione ao menos um contato.";
                this.feedbackType = "error";
                return;
            }

            this.feedback = "Enviando ou agendando mensagens...";
            this.feedbackType = "success";

            const url = this.scheduledTime
                ? `${BASE_URL}/verdurao/bot/whatsapp/send-scheduled`
                : `${BASE_URL}/verdurao/bot/whatsapp/send-mass`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contacts: selected,
                        message: this.message,
                        intervalSeconds: this.intervalSeconds,
                        scheduledTime: this.scheduledTime || null,
                        randomInterval: this.randomInterval,
                        simulateTyping: this.simulateTyping,
                    })
                });

                const data = await response.json();

                if (data.status === 'agendado') {
                    this.feedback = `✅ Campanha agendada para ${this.formatScheduledTime}`;
                } else if (Array.isArray(data)) {
                    const ok = data.filter(d => d.status === 'enviado').length;
                    const fail = data.length - ok;
                    this.feedback = `✅ Envio finalizado: ${ok} enviado(s), ${fail} erro(s).`;
                } else {
                    this.feedback = "✅ Solicitação enviada.";
                }

                this.feedbackType = "success";
                this.selectedContacts.clear();
                this.loadLogs();
            } catch (err) {
                this.feedback = "❌ Erro ao enviar ou agendar mensagens.";
                this.feedbackType = "error";
            }
        },
        toggleSelection(contact) {
            if (this.selectedContacts.has(contact)) this.selectedContacts.delete(contact);
            else this.selectedContacts.add(contact);
        },
        applyTemplate() {
            const tpl = this.templates.find(t => t.id == this.selectedTemplateId);
            if (tpl) this.message = tpl.body;
        },
        hidePreview() { this.previewTemplate = null; },
        showPreview(tpl) { this.previewTemplate = tpl; },
    }
}
</script>

<style scoped>
body {
    font-family: sans-serif;
}

.border-rounded {
    border-radius: 0.5rem;
}
</style>
