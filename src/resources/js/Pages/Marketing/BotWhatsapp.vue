<template>
    <Sidebar :activeTab="activeTab" @setActiveTab="setActiveTab">
        <div class="flex justify-center p-4 bg-gray-100 min-h-screen">
            <div class="bg-white rounded-lg shadow-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-screen-xl w-full">

                <!-- Contatos para Envio -->
                <!-- Contatos para Envio -->
                <div class="border rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">👥 Contatos para Envio</h2>
                    <div class="text-sm mb-2 text-gray-600">
                        Lista de Contatos
                        <span class="float-right font-medium">
                            Selecionados: {{ selectedContacts.size }} / {{ contacts.length }}
                        </span>
                    </div>

                    <!-- Campo de busca -->
                    <input v-model="searchQuery" type="text" placeholder="🔍 Buscar por nome ou número"
                        class="border p-1 rounded mb-2 w-full text-sm" />

                    <!-- Carregando contatos -->
                    <div v-if="loadingContacts" class="text-center text-blue-600 py-2">
                        Carregando contatos...
                    </div>

                    <!-- Lista de contatos -->
                    <div v-else class="border rounded p-2 mb-4 h-40 overflow-y-auto">
                        <div v-for="(contact, index) in filteredContacts" :key="index"
                            class="flex justify-between items-center border-b py-1">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" :value="contact" :checked="selectedContacts.has(contact)"
                                    @change="toggleSelection(contact)" />
                                <div>
                                    <div class="font-semibold">{{ contact.name }}</div>
                                    <div class="text-gray-600 text-sm">{{ contact.phone }}</div>
                                </div>
                            </div>
                            <button @click="removeContact(index)" class="text-red-500">🗑️</button>
                        </div>
                        <div v-if="!filteredContacts.length" class="text-gray-500 text-center py-3">
                            Nenhum contato encontrado.
                        </div>
                    </div>

                    <!-- Adicionar manualmente -->
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

                    <!-- Conectar WhatsApp -->
                    <button class="bg-green-600 text-white px-4 py-2 rounded mt-4" @click="showQrModal = true">
                        Conectar WhatsApp
                    </button>

                    <!-- Modal do QRCode -->
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
                    <h2 class="text-lg font-semibold mb-2">💬 Mensagem</h2>

                    <label class="block text-sm font-medium mb-1">Modelo de Mensagem</label>
                    <select class="border rounded p-1 mb-4 w-full text-sm">
                        <option>Selecionar modelo salvo</option>
                    </select>

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
                            <button class="text-blue-600 text-sm">Atualizar prévia</button>
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

                <!-- Configurações de Envio -->
                <div class="border rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">⚙️ Configurações de Envio</h2>

                    <label class="block text-sm font-medium mb-1">Intervalo entre mensagens (em segundos)</label>
                    <input v-model.number="intervalSeconds" type="number" min="1"
                        class="border rounded p-1 w-full text-sm mb-2" placeholder="Ex: 60" />
                    <div class="text-xs text-gray-500 mb-2">
                        Recomendado: mínimo 30s para evitar bloqueios.
                    </div>

                    <label class="block text-sm font-medium mb-1">Agendar horário de envio (opcional)</label>
                    <input v-model="scheduledTime" type="datetime-local"
                        class="border rounded p-1 w-full text-sm mb-2" />

                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" class="accent-gray-700" />
                        <label class="text-sm">Intervalo aleatório</label>
                    </div>

                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" checked class="accent-green-600" />
                        <label class="text-sm">Simular digitação</label>
                    </div>

                    <div class="text-sm font-medium mb-1">Resumo</div>
                    <div class="border rounded p-2 text-sm mb-2">
                        <div class="text-yellow-600 font-semibold mb-1">Aguardando envio</div>
                        <div>Total de contatos: <span class="float-right">{{ selectedContacts.size }}</span></div>
                        <div>Intervalo: <span class="float-right">{{ intervalSeconds }} segundos</span></div>
                        <div>Tempo estimado: <span class="float-right">{{ estimatedTime }} minutos</span></div>
                        <div v-if="scheduledTime">Agendado para: <span class="float-right">{{ formatScheduledTime
                                }}</span></div>
                    </div>

                    <button class="bg-green-600 text-white w-full py-2 rounded mt-2" @click="sendMassMessage">
                        ▶️ Iniciar Campanha
                    </button>
                </div>
            </div>
        </div>
    </Sidebar>
</template>

<script>
import Sidebar from '../Sidebar.vue';

export default {
    components: { Sidebar },
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
            loadingContacts: false, // mostra carregando contatos
            searchQuery: '',
            selectedContacts: new Set(),
            intervalSeconds: 60,
            scheduledTime: '', // formato datetime-local do input
        }
    },
    mounted() {
        this.loadGoogleContacts();
    },
    watch: {
        showQrModal(val) {
            if (val) {
                this.getQrCode();
            }
        }
    },
    computed: {
        previewMessage() {
            const name = this.manualName || (this.contacts[0] && this.contacts[0].name) || 'Nome';
            const phone = this.manualPhone || (this.contacts[0] && this.contacts[0].phone) || 'Telefone';
            return this.message
                .replace(/{nome}/g, name)
                .replace(/{telefone}/g, phone)
                .replace(/\n/g, '<br />');
        },
        filteredContacts() {
            const q = this.searchQuery.toLowerCase();
            return this.contacts.filter(c =>
                c.name.toLowerCase().includes(q) || c.phone.toLowerCase().includes(q)
            );
        },
        estimatedTime() {
            const total = this.selectedContacts.size || 0;
            return ((total * this.intervalSeconds) / 60).toFixed(1);
        },
        formatScheduledTime() {
            return this.scheduledTime
                ? new Date(this.scheduledTime).toLocaleString()
                : '';
        }
    },
    methods: {
        async loadGoogleContacts() {
            this.loadingContacts = true;
            try {
                const response = await fetch('http://localhost:3002/google-contacts');
                if (!response.ok) throw new Error('Erro ao buscar contatos');
                const contatos = await response.json();
                if (Array.isArray(contatos)) {
                    this.contacts = contatos;
                }
            } catch (err) {
                this.feedback = "Não foi possível carregar contatos do Google.";
                this.feedbackType = "error";
            }
            this.loadingContacts = false;
        },
        async getQrCode() {
            this.qrcode = null;
            this.isConnected = false;
            try {
                const response = await fetch('http://localhost:3001/verdurao/bot/whatsapp/qrcode');
                const data = await response.json();
                if (data.connected) {
                    this.isConnected = true;
                    this.qrcode = null;
                } else {
                    this.qrcode = data.qrcode;
                    this.isConnected = false;
                }
            } catch (err) {
                this.qrcode = null;
                this.isConnected = false;
            }
        },
        removeContact(index) {
            this.contacts.splice(index, 1);
        },
        addManualContact() {
            if (!this.manualName.trim() || !this.manualPhone.trim()) return;
            this.contacts.push({ name: this.manualName, phone: this.manualPhone });
            this.manualName = '';
            this.manualPhone = '';
        },
        addVariable(variable) {
            this.message += variable;
            this.$nextTick(() => {
                this.$refs.messageTextarea.focus();
            });
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

            try {
                const response = await fetch('http://localhost:3001/verdurao/bot/whatsapp/send-scheduled', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contacts: selected,
                        message: this.message,
                        intervalSeconds: this.intervalSeconds,
                        scheduledTime: this.scheduledTime || null,
                    })
                });

                const data = await response.json();
                if (data.status === 'agendado') {
                    this.feedback = `✅ Campanha agendada para ${this.formatScheduledTime}`;
                } else {
                    this.feedback = "✅ Mensagens sendo enviadas agora.";
                }

                this.feedbackType = "success";
                this.selectedContacts.clear();
            } catch (err) {
                this.feedback = "❌ Erro ao enviar ou agendar mensagens.";
                this.feedbackType = "error";
            }
        },
        toggleSelection(contact) {
            if (this.selectedContacts.has(contact)) {
                this.selectedContacts.delete(contact);
            } else {
                this.selectedContacts.add(contact);
            }
        }
    }
}
</script>

<style scoped>
body {
    font-family: sans-serif;
}

/* Melhorias de espaçamento e alinhamento */
.border-rounded {
    border-radius: 0.5rem;
}
</style>
