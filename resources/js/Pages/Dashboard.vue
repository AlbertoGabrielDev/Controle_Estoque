<template>
  <Sidebar :activeTab="activeTab" @setActiveTab="setActiveTab">

      <!-- Main Content -->
 

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto bg-gray-100 p-6">
          <!-- Demo Notice -->
          <div class="demo-notice p-4 mb-6 rounded-lg">
            <div class="flex items-start">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 mr-2 mt-0.5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <p class="font-medium text-yellow-800">Esta é uma demonstração</p>
                <p class="text-sm text-yellow-700">Esta interface é um protótipo funcional. Em um ambiente real, seria
                  necessário configurar a API do WhatsApp Business e autenticação adequada.</p>
              </div>
            </div>
          </div>

          <!-- Dashboard Tab -->
          <div v-if="activeTab === 'dashboard'" id="content-dashboard" class="tab-content active">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
              <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-gray-700">Mensagens Enviadas</h3>
                  <span class="text-green-500 bg-green-100 rounded-full px-3 py-1 text-sm">+12% ↑</span>
                </div>
                <p class="text-3xl font-bold text-gray-800">1,248</p>
                <p class="text-sm text-gray-500 mt-2">Últimos 30 dias</p>
              </div>
              <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-gray-700">Taxa de Resposta</h3>
                  <span class="text-green-500 bg-green-100 rounded-full px-3 py-1 text-sm">+5% ↑</span>
                </div>
                <p class="text-3xl font-bold text-gray-800">68%</p>
                <p class="text-sm text-gray-500 mt-2">Últimos 30 dias</p>
              </div>
              <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-gray-700">Contatos Ativos</h3>
                  <span class="text-yellow-500 bg-yellow-100 rounded-full px-3 py-1 text-sm">+2% ↑</span>
                </div>
                <p class="text-3xl font-bold text-gray-800">523</p>
                <p class="text-sm text-gray-500 mt-2">Últimos 30 dias</p>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Atividade Recente</h3>
                <div class="space-y-4">
                  <div class="flex items-start">
                    <div class="bg-blue-100 rounded-full p-2 mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-800">Campanha "Promoção de Verão" enviada</p>
                      <p class="text-sm text-gray-500">Enviado para 215 contatos • 85% taxa de entrega</p>
                      <p class="text-xs text-gray-400 mt-1">Hoje, 10:45</p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="bg-green-100 rounded-full p-2 mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-800">Novo bot de resposta automática criado</p>
                      <p class="text-sm text-gray-500">Bot "Atendimento ao Cliente" • 3 fluxos configurados</p>
                      <p class="text-xs text-gray-400 mt-1">Ontem, 15:30</p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="bg-purple-100 rounded-full p-2 mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-800">Extração de contatos concluída</p>
                      <p class="text-sm text-gray-500">78 novos contatos extraídos • Região: Centro</p>
                      <p class="text-xs text-gray-400 mt-1">23/11/2023, 09:15</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Conversas Recentes</h3>
                <div class="space-y-3">
                  <div class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer contact-card">
                    <img
                      src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wMSAwIDAgMS0zLjAxLTMuMDFWNWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                      class="h-10 w-10 rounded-full bg-blue-100 p-2" alt="Contact">
                    <div class="ml-3 flex-1">
                      <div class="flex justify-between items-center">
                        <p class="font-medium text-gray-800">Maria Silva</p>
                        <p class="text-xs text-gray-500">10:23</p>
                      </div>
                      <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500 break-words">Olá, gostaria de mais informações sobre...</p>
                        <span
                          class="bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">2</span>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer contact-card">
                    <img
                      src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA5djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wMSAwIDAgMS0zLjAxLTMuMDFWOWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                      class="h-10 w-10 rounded-full bg-green-100 p-2" alt="Contact">
                    <div class="ml-3 flex-1">
                      <div class="flex justify-between items-center">
                        <p class="font-medium text-gray-800">João Pereira</p>
                        <p class="text-xs text-gray-500">09:45</p>
                      </div>
                      <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500 break-words">Obrigado pelo atendimento! Vou...</p>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer contact-card">
                    <img
                      src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wMSAwIDAgMS0zLjAxLTMuMDFWNWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                      class="h-10 w-10 rounded-full bg-yellow-100 p-2" alt="Contact">
                    <div class="ml-3 flex-1">
                      <div class="flex justify-between items-center">
                        <p class="font-medium text-gray-800">Mercado Central</p>
                        <p class="text-xs text-gray-500">Ontem</p>
                      </div>
                      <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500 break-words">Prezado cliente, informamos que seu...</p>
                        <span
                          class="bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">1</span>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer contact-card">
                    <img
                      src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wMSAwIDAgMS0zLjAxLTMuMDFWNWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                      class="h-10 w-10 rounded-full bg-purple-100 p-2" alt="Contact">
                    <div class="ml-3 flex-1">
                      <div class="flex justify-between items-center">
                        <p class="font-medium text-gray-800">Ana Ferreira</p>
                        <p class="text-xs text-gray-500">22/11</p>
                      </div>
                      <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500 break-words">Você: Enviei o orçamento por email...</p>
                      </div>
                    </div>
                  </div>
                </div>
                <button
                  class="mt-4 w-full py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">Ver
                  todas as conversas</button>
              </div>
            </div>
          </div>

          <!-- Bulk Message Tab -->
          <div v-if="activeTab === 'bulk'" id="content-bulk" class="tab-content">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Envio de Mensagens em Massa</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="mb-4">
                    <label for="message-text" class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                    <textarea id="message-text" rows="5"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Digite sua mensagem aqui...">Olá, temos uma promoção especial para você! 🎉 Aproveite 20% de desconto em todos os nossos produtos até o final da semana. Responda esta mensagem para mais informações.</textarea>
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adicionar</label>
                    <div class="flex space-x-2">
                      <button
                        class="flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Imagem
                      </button>
                      <button
                        class="flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        Arquivo
                      </button>
                      <button
                        class="flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Emoji
                      </button>
                    </div>
                  </div>
                  <div class="mb-4">
                    <label for="interval" class="block text-sm font-medium text-gray-700 mb-2">Intervalo entre
                      mensagens</label>
                    <div class="flex items-center space-x-4">
                      <select id="interval" v-model="intervalOption"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="10">10 segundos</option>
                        <option value="30" selected>30 segundos</option>
                        <option value="60">1 minuto</option>
                        <option value="300">5 minutos</option>
                        <option value="custom">Personalizado</option>
                      </select>
                      <div v-if="intervalOption === 'custom'" class="flex items-center">
                        <input type="number" min="1"
                          class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                          value="30">
                        <span class="ml-2 text-gray-700">segundos</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Destinatários</label>
                    <div class="flex mb-2">
                      <button @click="bulkSubTab = 'contacts'"
                        :class="{ 'border-b-2 border-green-500 text-green-600 font-medium': bulkSubTab === 'contacts', 'text-gray-500 hover:text-gray-700': bulkSubTab !== 'contacts' }"
                        class="flex-1 py-2 text-center">Lista de Contatos</button>
                      <button @click="bulkSubTab = 'import'"
                        :class="{ 'border-b-2 border-green-500 text-green-600 font-medium': bulkSubTab === 'import', 'text-gray-500 hover:text-gray-700': bulkSubTab !== 'import' }"
                        class="flex-1 py-2 text-center">Importar Contatos</button>
                    </div>
                    <div v-if="bulkSubTab === 'contacts'">
                      <div class="border border-gray-300 rounded-md h-64 overflow-y-auto p-2">
                        <div class="flex items-center mb-2">
                          <input type="checkbox" id="select-all" class="mr-2">
                          <label for="select-all" class="text-sm font-medium text-gray-700">Selecionar todos</label>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                          <div class="flex items-center py-1">
                            <input type="checkbox" checked class="mr-2">
                            <span class="text-sm text-gray-800">Maria Silva</span>
                            <span class="ml-auto text-xs text-gray-500">+55 11 98765-4321</span>
                          </div>
                          <div class="flex items-center py-1">
                            <input type="checkbox" checked class="mr-2">
                            <span class="text-sm text-gray-800">João Pereira</span>
                            <span class="ml-auto text-xs text-gray-500">+55 11 91234-5678</span>
                          </div>
                          <div class="flex items-center py-1">
                            <input type="checkbox" checked class="mr-2">
                            <span class="text-sm text-gray-800">Ana Ferreira</span>
                            <span class="ml-auto text-xs text-gray-500">+55 21 99876-5432</span>
                          </div>
                          <div class="flex items-center py-1">
                            <input type="checkbox" class="mr-2">
                            <span class="text-sm text-gray-800">Carlos Santos</span>
                            <span class="ml-auto text-xs text-gray-500">+55 21 98765-1234</span>
                          </div>
                          <div class="flex items-center py-1">
                            <input type="checkbox" class="mr-2">
                            <span class="text-sm text-gray-800">Mercado Central</span>
                            <span class="ml-auto text-xs text-gray-500">+55 11 3456-7890</span>
                          </div>
                          <div class="flex items-center py-1">
                            <input type="checkbox" checked class="mr-2">
                            <span class="text-sm text-gray-800">Farmácia Popular</span>
                            <span class="ml-auto text-xs text-gray-500">+55 11 2345-6789</span>
                          </div>
                          <div class="flex items-center py-1">
                            <input type="checkbox" checked class="mr-2">
                            <span class="text-sm text-gray-800">Restaurante Sabor</span>
                            <span class="ml-auto text-xs text-gray-500">+55 11 3456-7891</span>
                          </div>
                        </div>
                      </div>
                      <p class="text-sm text-gray-600 mt-2">5 contatos selecionados</p>
                    </div>
                    <div v-if="bulkSubTab === 'import'">
                      <div
                        class="border border-gray-300 border-dashed rounded-md h-64 flex items-center justify-center">
                        <div class="text-center p-6">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-3"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                          </svg>
                          <p class="text-gray-700 mb-2">Arraste um arquivo CSV ou Excel</p>
                          <p class="text-gray-500 text-sm mb-3">ou</p>
                          <button
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">Selecionar
                            arquivo</button>
                        </div>
                      </div>
                      <p class="text-sm text-gray-600 mt-2">Formatos suportados: CSV, XLS, XLSX</p>
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Agendamento (opcional)</label>
                    <div class="flex space-x-4">
                      <div class="flex-1">
                        <input type="date"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                      </div>
                      <div class="flex-1">
                        <input type="time"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex justify-end mt-6 space-x-3">
                <button
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">Salvar
                  como rascunho</button>
                <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">Enviar
                  mensagens</button>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
              <h3 class="text-lg font-semibold text-gray-700 mb-4">Campanhas Recentes</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Destinatários</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Entregues
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Promoção de Verão</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">24/11/2023</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">215</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">183 (85%)</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Concluído</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-blue-600 hover:text-blue-800 mr-3">Ver relatório</button>
                        <button class="text-gray-600 hover:text-gray-800">Duplicar</button>
                      </td>
                    </tr>
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Lançamento Produto</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">20/11/2023</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">150</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">142 (95%)</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Concluído</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-blue-600 hover:text-blue-800 mr-3">Ver relatório</button>
                        <button class="text-gray-600 hover:text-gray-800">Duplicar</button>
                      </td>
                    </tr>
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Pesquisa de Satisfação</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">15/11/2023</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">300</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">275 (92%)</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Concluído</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-blue-600 hover:text-blue-800 mr-3">Ver relatório</button>
                        <button class="text-gray-600 hover:text-gray-800">Duplicar</button>
                      </td>
                    </tr>
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Black Friday</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">25/11/2023</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">500</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">0 (0%)</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Agendado</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-blue-600 hover:text-blue-800 mr-3">Editar</button>
                        <button class="text-red-600 hover:text-red-800">Cancelar</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Auto Response Tab -->
          <div v-if="activeTab === 'auto'" id="content-auto" class="tab-content">
            <div v-if="!showBotEditor" class="bg-white rounded-lg shadow-md p-6 mb-6">
              <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Respostas Automáticas</h2>
                <button @click="showBotEditor = true"
                  class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Novo Bot
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                  <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center">
                      <div class="bg-blue-100 p-2 rounded-full mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                          viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                      </div>
                      <h3 class="font-semibold text-gray-800">Atendimento ao Cliente</h3>
                    </div>
                    <div class="flex items-center">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Ativo
                      </span>
                    </div>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Responde perguntas frequentes sobre produtos, horários de
                    funcionamento e políticas de devolução.</p>
                  <div class="flex justify-between text-sm text-gray-500">
                    <span>3 fluxos configurados</span>
                    <span>Última edição: 22/11/2023</span>
                  </div>
                  <div class="mt-4 flex justify-end space-x-2">
                    <button
                      class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">Editar</button>
                    <button
                      class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">Ver
                      detalhes</button>
                  </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                  <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center">
                      <div class="bg-purple-100 p-2 rounded-full mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                          viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                      </div>
                      <h3 class="font-semibold text-gray-800">Vendas Automáticas</h3>
                    </div>
                    <div class="flex items-center">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Ativo
                      </span>
                    </div>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Apresenta produtos, responde dúvidas sobre preços e direciona
                    para
                    finalização de compra.</p>
                  <div class="flex justify-between text-sm text-gray-500">
                    <span>5 fluxos configurados</span>
                    <span>Última edição: 20/11/2023</span>
                  </div>
                  <div class="mt-4 flex justify-end space-x-2">
                    <button
                      class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">Editar</button>
                    <button
                      class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">Ver
                      detalhes</button>
                  </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                  <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center">
                      <div class="bg-yellow-100 p-2 rounded-full mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none"
                          viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </div>
                      <h3 class="font-semibold text-gray-800">Confirmação de Agendamentos</h3>
                    </div>
                    <div class="flex items-center">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Inativo
                      </span>
                    </div>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Confirma agendamentos, envia lembretes e permite reagendamentos
                    automáticos.</p>
                  <div class="flex justify-between text-sm text-gray-500">
                    <span>2 fluxos configurados</span>
                    <span>Última edição: 15/11/2023</span>
                  </div>
                  <div class="mt-4 flex justify-end space-x-2">
                    <button
                      class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">Editar</button>
                    <button
                      class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">Ver
                      detalhes</button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="showBotEditor" id="bot-editor" class="bg-white rounded-lg shadow-md p-6">
              <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Novo Bot de Resposta Automática</h2>
                <button @click="showBotEditor = false" class="text-gray-500 hover:text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                  <div class="mb-4">
                    <label for="bot-name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Bot</label>
                    <input type="text" id="bot-name"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Ex: Atendimento ao Cliente">
                  </div>
                  <div class="mb-4">
                    <label for="bot-description" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                    <textarea id="bot-description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Descreva a função deste bot..."></textarea>
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center">
                      <label class="inline-flex items-center mr-4">
                        <input type="radio" name="bot-status" value="active" class="form-radio text-green-600" checked>
                        <span class="ml-2">Ativo</span>
                      </label>
                      <label class="inline-flex items-center">
                        <input type="radio" name="bot-status" value="inactive" class="form-radio text-gray-600">
                        <span class="ml-2">Inativo</span>
                      </label>
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fluxos</label>
                    <div class="space-y-2">
                      <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                        <span class="text-sm font-medium">Boas-vindas</span>
                        <button class="text-blue-600 hover:text-blue-800 text-sm">Editar</button>
                      </div>
                      <button class="flex items-center text-green-600 hover:text-green-800 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Adicionar novo fluxo
                      </button>
                    </div>
                  </div>
                </div>
                <div class="md:col-span-2">
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Configuração do Fluxo:
                      Boas-vindas</label>
                    <div class="border border-gray-300 rounded-md p-4">
                      <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem de Boas-vindas</label>
                        <textarea rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Olá! Bem-vindo ao nosso atendimento automático...">Olá! 👋 Bem-vindo ao nosso atendimento automático. Como posso ajudar você hoje?

1️⃣ Informações sobre produtos
2️⃣ Horários de funcionamento
3️⃣ Política de devolução
4️⃣ Falar com um atendente</textarea>
                      </div>
                      <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Opções de Resposta</label>
                        <div class="space-y-3">
                          <div class="flex items-center space-x-2">
                            <input type="text"
                              class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              value="1" readonly>
                            <span class="text-gray-500">→</span>
                            <input type="text"
                              class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Resposta para opção 1" value="Informações sobre produtos">
                            <button class="text-gray-500 hover:text-gray-700">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                              </svg>
                            </button>
                          </div>
                          <div class="flex items-center space-x-2">
                            <input type="text"
                              class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              value="2" readonly>
                            <span class="text-gray-500">→</span>
                            <input type="text"
                              class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Resposta para opção 2" value="Horários de funcionamento">
                            <button class="text-gray-500 hover:text-gray-700">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                              </svg>
                            </button>
                          </div>
                          <div class="flex items-center space-x-2">
                            <input type="text"
                              class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              value="3" readonly>
                            <span class="text-gray-500">→</span>
                            <input type="text"
                              class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Resposta para opção 3" value="Política de devolução">
                            <button class="text-gray-500 hover:text-gray-700">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                              </svg>
                            </button>
                          </div>
                          <div class="flex items-center space-x-2">
                            <input type="text"
                              class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              value="4" readonly>
                            <span class="text-gray-500">→</span>
                            <input type="text"
                              class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Resposta para opção 4" value="Falar com um atendente">
                            <button class="text-gray-500 hover:text-gray-700">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                              </svg>
                            </button>
                          </div>
                          <button class="flex items-center text-green-600 hover:text-green-800 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                              stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Adicionar opção
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="flex justify-between mt-6">
                    <button @click="showBotEditor = false"
                      class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">Cancelar</button>
                    <div>
                      <button
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors mr-2">Testar
                        Bot</button>
                      <button
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">Salvar
                        Bot</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="!showBotEditor" class="bg-white rounded-lg shadow-md p-6">
              <h3 class="text-lg font-semibold text-gray-700 mb-4">Simulador de Chat</h3>
              <div class="flex h-96">
                <div class="w-1/3 border-r border-gray-200 pr-4">
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Selecione um bot para testar</label>
                    <select
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                      <option>Atendimento ao Cliente</option>
                      <option>Vendas Automáticas</option>
                      <option>Confirmação de Agendamentos</option>
                    </select>
                  </div>
                  <button
                    class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors mb-4">
                    Iniciar Simulação
                  </button>
                  <div class="text-sm text-gray-600">
                    <p class="font-medium mb-2">Dicas para teste:</p>
                    <ul class="list-disc pl-5 space-y-1">
                      <li>Digite "1" para informações de produtos</li>
                      <li>Digite "2" para horários de funcionamento</li>
                      <li>Digite "3" para política de devolução</li>
                      <li>Digite "4" para falar com atendente</li>
                    </ul>
                  </div>
                </div>
                <div class="w-2/3 pl-4 flex flex-col">
                  <div class="flex-1 bg-gray-50 rounded-lg p-4 overflow-y-auto mb-4">
                    <div class="message-bubble received">
                      Olá! 👋 Bem-vindo ao nosso atendimento automático. Como posso ajudar você hoje?
                    </div>
                  </div>
                  <div class="flex">
                    <input type="text"
                      class="flex-1 chat-input px-4 py-2 border border-gray-300 rounded-l-full focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Digite sua mensagem...">
                    <button class="btn-primary px-4 py-2 text-white rounded-r-full">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Extract Tab -->
          <div v-if="activeTab === 'extract'" id="content-extract" class="tab-content">
            <div class="bg-white rounded-lg shadow-md p-6">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Extração de Contatos do Google Maps</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="mb-4">
                    <label for="search-query" class="block text-sm font-medium text-gray-700 mb-2">Termo de
                      Busca</label>
                    <input type="text" id="search-query"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Ex: Restaurantes em São Paulo">
                  </div>
                  <div class="mb-4">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Localização</label>
                    <input type="text" id="location"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Ex: São Paulo, SP">
                  </div>
                  <div class="mb-4">
                    <label for="max-results" class="block text-sm font-medium text-gray-700 mb-2">Número de
                      Resultados</label>
                    <select id="max-results"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                      <option>10</option>
                      <option selected>50</option>
                      <option>100</option>
                      <option>200</option>
                    </select>
                  </div>
                </div>
                <div>
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opções Avançadas</label>
                    <div class="space-y-2">
                      <div class="flex items-center">
                        <input type="checkbox" id="include-reviews" class="mr-2">
                        <label for="include-reviews" class="text-sm text-gray-700">Incluir avaliações</label>
                      </div>
                      <div class="flex items-center">
                        <input type="checkbox" id="include-hours" class="mr-2">
                        <label for="include-hours" class="text-sm text-gray-700">Incluir horários de
                          funcionamento</label>
                      </div>
                      <div class="flex items-center">
                        <input type="checkbox" id="include-website" class="mr-2" checked>
                        <label for="include-website" class="text-sm text-gray-700">Incluir websites</label>
                      </div>
                    </div>
                  </div>
                  <div class="border border-gray-300 rounded-md p-4 mt-6">
                    <p class="text-sm text-gray-600 mb-2">A extração pode levar de 5 a 15 minutos dependendo do número
                      de
                      resultados.</p>
                    <button
                      class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                      Iniciar Extração
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Contacts Tab -->
          <div v-if="activeTab === 'contacts'" id="content-contacts" class="tab-content">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
              <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Gerenciamento de Contatos</h2>
                <button
                  class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Adicionar Contato
                </button>
              </div>

              <div class="flex items-center justify-between mb-4">
                <div class="flex-1 max-w-md">
                  <div class="relative">
                    <input type="text"
                      class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Buscar contatos...">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                      fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                </div>
                <div>
                  <button
                    class="flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtros
                  </button>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Telefone
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cidade
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <img
                              src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wMSAwIDAgMS0zLjAxLTMuMDFWNWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                              class="h-10 w-10 rounded-full bg-blue-100 p-2" alt="Contact">
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">Maria Silva</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">+55 11 98765-4321</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Restaurante Sabor</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">São Paulo, SP</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Cliente</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                        <button class="text-red-600 hover:text-red-900">Excluir</button>
                      </td>
                    </tr>
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <img
                              src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wMSAwIDAgMS0zLjAxLTMuMDFWNWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                              class="h-10 w-10 rounded-full bg-green-100 p-2" alt="Contact">
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">João Pereira</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">+55 21 99876-5432</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Mercado Central</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">Rio de Janeiro, RJ</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Fornecedor</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                        <button class="text-red-600 hover:text-red-900">Excluir</button>
                      </td>
                    </tr>
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <img
                              src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wISAwIDAgMS0zLjAxLTMuMDFWNWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                              class="h-10 w-10 rounded-full bg-purple-100 p-2" alt="Contact">
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">Carlos Santos</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">+55 31 98765-1234</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Auto Peças Ltda</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">Belo Horizonte, MG</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Lead</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                        <button class="text-red-600 hover:text-red-900">Excluir</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Settings Tab -->
          <div v-if="activeTab === 'settings'" id="content-settings" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                  <h2 class="text-xl font-bold text-gray-800 mb-4">Configurações da Conta</h2>

                  <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto de Perfil</label>
                    <div class="flex items-center">
                      <img
                        src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wMSAwIDAgMS0zLjAxLTMuMDFWNWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                        class="h-16 w-16 rounded-full bg-gray-200 p-2" alt="User">
                      <div class="ml-4">
                        <button
                          class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm mr-2">
                          Alterar
                        </button>
                        <button
                          class="px-4 py-2 bg-gray-100 text-red-600 rounded-md hover:bg-gray-200 transition-colors text-sm">
                          Remover
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                    <input type="text" id="name"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      value="Administrador">
                  </div>

                  <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      value="admin@empresa.com">
                  </div>

                  <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                    <input type="tel" id="phone"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      value="+55 11 91234-5678">
                  </div>

                  <div class="mb-4">
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Fuso Horário</label>
                    <select id="timezone"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                      <option>(UTC-03:00) Brasília</option>
                      <option>(UTC-04:00) Manaus</option>
                      <option>(UTC-05:00) Rio Branco</option>
                    </select>
                  </div>

                  <div class="mt-6">
                    <button
                      class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                      Salvar Alterações
                    </button>
                  </div>
                </div>
              </div>

              <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                  <h2 class="text-xl font-bold text-gray-800 mb-4">Integrações</h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                      <div class="flex items-start mb-3">
                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                          </svg>
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-800">API do WhatsApp Business</h3>
                          <p class="text-sm text-gray-600">Conecte-se à API oficial do WhatsApp Business</p>
                        </div>
                      </div>
                      <div class="flex items-center text-sm">
                        <span class="text-green-600 font-medium mr-2">Conectado</span>
                        <span class="text-gray-500">Última conexão: Hoje, 08:45</span>
                      </div>
                      <button
                        class="mt-3 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                        Gerenciar Conexão
                      </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                      <div class="flex items-start mb-3">
                        <div class="bg-green-100 p-2 rounded-full mr-3">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 14v6m-3-3h6M6 10h2a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 10h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-6a2 2 0 00-2-2z" />
                          </svg>
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-800">Google Sheets</h3>
                          <p class="text-sm text-gray-600">Sincronize contatos com planilhas do Google</p>
                        </div>
                      </div>
                      <div class="flex items-center text-sm">
                        <span class="text-gray-600 font-medium mr-2">Não conectado</span>
                      </div>
                      <button
                        class="mt-3 w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        Conectar
                      </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                      <div class="flex items-start mb-3">
                        <div class="bg-yellow-100 p-2 rounded-full mr-3">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                          </svg>
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-800">CRM</h3>
                          <p class="text-sm text-gray-600">Integre com seu sistema CRM</p>
                        </div>
                      </div>
                      <div class="flex items-center text-sm">
                        <span class="text-gray-600 font-medium mr-2">Não conectado</span>
                      </div>
                      <button
                        class="mt-3 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                        Configurar
                      </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                      <div class="flex items-start mb-3">
                        <div class="bg-purple-100 p-2 rounded-full mr-3">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                          </svg>
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-800">API Personalizada</h3>
                          <p class="text-sm text-gray-600">Conecte com outras aplicações via API</p>
                        </div>
                      </div>
                      <div class="flex items-center text-sm">
                        <span class="text-gray-600 font-medium mr-2">Não conectado</span>
                      </div>
                      <button
                        class="mt-3 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                        Configurar
                      </button>
                    </div>
                  </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                  <h2 class="text-xl font-bold text-gray-800 mb-4">Preferências de Notificação</h2>

                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="font-medium text-gray-800">Notificações por Email</p>
                        <p class="text-sm text-gray-600">Receba alertas importantes por email</p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked>
                        <div
                          class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                        </div>
                      </label>
                    </div>

                    <div class="flex items-center justify-between">
                      <div>
                        <p class="font-medium text-gray-800">Notificações por WhatsApp</p>
                        <p class="text-sm text-gray-600">Alertas importantes no seu WhatsApp</p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer">
                        <div
                          class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                        </div>
                      </label>
                    </div>

                    <div class="flex items-center justify-between">
                      <div>
                        <p class="font-medium text-gray-800">Alertas de Campanha</p>
                        <p class="text-sm text-gray-600">Notificações quando campanhas terminarem</p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked>
                        <div
                          class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                        </div>
                      </label>
                    </div>

                    <div class="flex items-center justify-between">
                      <div>
                        <p class="font-medium text-gray-800">Alertas de Respostas</p>
                        <p class="text-sm text-gray-600">Notificações quando receber novas respostas</p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked>
                        <div
                          class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                        </div>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    
  </Sidebar>
</template>

<script>
import { ref } from 'vue';
import Sidebar from './Sidebar.vue';


export default {
  name: 'WhatsAppDashboard',
  components: { Sidebar },
  setup() {
    const activeTab = ref('dashboard');
    const showBotEditor = ref(false);
    const bulkSubTab = ref('contacts');
    const intervalOption = ref('30');

    const setActiveTab = (tab) => {
      activeTab.value = tab;
      showBotEditor.value = false;
    };

    return {
      activeTab,
      showBotEditor,
      bulkSubTab,
      intervalOption,
      setActiveTab
    };
  }
};
</script>

<style></style>