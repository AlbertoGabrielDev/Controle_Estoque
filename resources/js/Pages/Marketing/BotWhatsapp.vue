<template>
    <div class="flex justify-center p-4 bg-gray-100 min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-screen-xl w-full">

            <!-- Contatos para Envio -->
            <div class="border rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-2">👥 Contatos para Envio</h2>
                <div class="text-sm mb-2 text-gray-600">Lista de Contatos <span class="float-right font-medium">Total:
                        24</span></div>
                <div class="border rounded p-2 mb-4 h-40 overflow-y-auto">
                    <div v-for="(contact, index) in contacts" :key="index"
                        class="flex justify-between items-center border-b py-1">
                        <div>
                            <div class="font-semibold">{{ contact.name }}</div>
                            <div class="text-gray-600 text-sm">{{ contact.phone }}</div>
                        </div>
                        <button @click="removeContact(index)" class="text-red-500">🗑️</button>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Importar Contatos</label>
                    <input type="file" class="border rounded p-1 w-full text-sm" />
                    <button class="bg-green-600 text-white px-4 py-1 mt-2 rounded">Importar</button>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium mb-1">Adicionar Manualmente</label>
                    <div class="flex gap-2">
                        <input v-model="manualName" type="text" placeholder="Nome"
                            class="border rounded p-1 flex-1 text-sm" />
                        <input v-model="manualPhone" type="text" placeholder="Número contato"
                            class="border rounded p-1 flex-1 text-sm" />
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

                <!-- Título "Texto da Mensagem" -->
                <h3 class="text-sm font-medium mb-2">Texto da Mensagem</h3>
                
                <!-- Botões para adicionar conteúdo -->
                <div class="flex gap-1 mb-2">
                    <button class="p-1 border rounded hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    <button class="p-1 border rounded hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <button class="p-1 border rounded hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </button>
                </div>
                
                <!-- Variáveis com título -->
                <div class="mb-2">
                    <div class="text-xs text-gray-500 mb-1">Variáveis:</div>
                    <div class="flex gap-1">
                        <span class="bg-gray-200 px-2 py-1 rounded cursor-pointer hover:bg-gray-300 text-xs"
                            @click="addVariable('{home}')">{home}</span>
                        <span class="bg-gray-200 px-2 py-1 rounded cursor-pointer hover:bg-gray-300 text-xs"
                            @click="addVariable('{telefone}')">{telefone}</span>
                    </div>
                </div>
                
                <textarea v-model="message" class="border rounded p-2 w-full text-sm h-32 mb-4"></textarea>

                <!-- Prévia com layout melhorado -->
                <div class="border rounded p-3 text-sm bg-gray-50">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-medium">Prévia</div>
                        <button class="text-blue-600 text-sm">Atualizar prévia</button>
                    </div>
                    
                    <div class="bg-white p-3 rounded border mb-3">
                        <div v-html="previewMessage"></div>
                    </div>
                    
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gray-300 text-black px-4 py-1.5 rounded text-sm">Salvar como modelo</button>
                        <button class="flex-1 bg-blue-600 text-white px-4 py-1.5 rounded text-sm">Testar mensagem</button>
                    </div>
                </div>
            </div>

            <!-- Configurações de Envio -->
            <div class="border rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-2">⚙️ Configurações de Envio</h2>

                <label class="block text-sm font-medium mb-1">Intervalo entre mensagens</label>
                <select class="border rounded p-1 w-full text-sm mb-1">
                    <option>1 minuto</option>
                </select>
                <div class="text-xs text-gray-500 mb-2">Recomendado: 30s<br />Intervalos muito curtos podem resultar em
                    bloqueio pela Meta</div>

                <label class="block text-sm font-medium mb-1">Horário de envio</label>
                <div class="flex gap-2 mb-2">
                    <select class="border rounded p-1 flex-1 text-sm">
                        <option>Imediatamente</option>
                    </select>
                    <input type="text" placeholder="mm/dd/yyyy, --" class="border rounded p-1 flex-1 text-sm" />
                </div>

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
                    <div>Total de contatos: <span class="float-right">24</span></div>
                    <div>Intervalo: <span class="float-right">1 minuto</span></div>
                    <div>Tempo estimado: <span class="float-right">24 minutos</span></div>
                </div>

                <button class="bg-green-600 text-white w-full py-2 rounded mt-2">▶️ Iniciar Campanha</button>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            contacts: [
                { name: 'João Silva', phone: '+55 11 98765-4321' },
                { name: 'Maria Oliveira', phone: '+55 11 91234-5678' },
                { name: 'Carlos Santos', phone: '+55 11 99876-5432' },
            ],
            message: 'Olá {home}!\nTemos uma oferta especial para você! Aproveite 20% de desconto em todos os nossos produtos até o final da semana.',
            manualName: '',
            manualPhone: ''
        }
    },
    computed: {
        previewMessage() {
            return this.message
                .replace(/{home}/g, this.contacts[0].name)
                .replace(/{telefone}/g, this.contacts[0].phone)
                .replace(/\n/g, '<br />')
        }
    },
    methods: {
        removeContact(index) {
            this.contacts.splice(index, 1)
        },
        addVariable(variable) {
            this.message += variable
            this.$nextTick(() => {
                this.$refs.messageTextarea.focus()
            })
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