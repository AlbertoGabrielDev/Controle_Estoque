<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 min-h-screen bg-gradient-to-b from-[#0b7361] to-[#045c4d] text-white">
      <div class="text-2xl font-bold p-4 border-b border-green-800">
        <i class="fab fa-whatsapp mr-2"></i> WhatsApp Marketing Dashboard
      </div>
      <nav class="flex-1">
        <ul>
          <li class="p-4 hover:bg-green-600 flex items-center">
            <i class="fas fa-home mr-2"></i> Dashboard
          </li>
          <li class="p-4 hover:bg-green-600 flex items-center">
            <i class="fas fa-paper-plane mr-2"></i> Envio em Massa
          </li>
          <li class="p-4 hover:bg-green-600 flex items-center">
            <i class="fas fa-bolt mr-2"></i> Respostas Automáticas
          </li>
          <li class="p-4 hover:bg-green-600 flex items-center">
            <i class="fas fa-map-marker-alt mr-2"></i> Extração do Maps
          </li>
          <li class="p-4 hover:bg-green-600 flex items-center">
            <i class="fas fa-address-book mr-2"></i> Contatos
          </li>
          <li class="p-4 hover:bg-green-600 flex items-center">
            <i class="fas fa-cog mr-2"></i> Configurações
          </li>
        </ul>
      </nav>
      <div class="p-4 bg-green-800">
        <div class="text-sm">Plano Atual</div>
        <div class="text-lg font-bold">Premium</div>
        <div class="text-xs">Válido até 15/12/2023</div>
      </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-6 overflow-y-auto">
      <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
        <p class="font-bold">Esta é uma demonstração</p>
        <p>Esta interface é um protótipo funcional. Em um ambiente real, seria necessário configurar a API do WhatsApp Business e autenticação adequada.</p>
      </div>

      <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow p-4 rounded">
          <div class="text-gray-500">Mensagens Enviadas</div>
          <div class="text-3xl font-bold">1,248</div>
          <div class="text-green-500 text-sm mt-1">+12% ↑</div>
        </div>
        <div class="bg-white shadow p-4 rounded">
          <div class="text-gray-500">Taxa de Resposta</div>
          <div class="text-3xl font-bold">68%</div>
          <div class="text-green-500 text-sm mt-1">+5% ↑</div>
        </div>
        <div class="bg-white shadow p-4 rounded">
          <div class="text-gray-500">Contatos Ativos</div>
          <div class="text-3xl font-bold">523</div>
          <div class="text-yellow-500 text-sm mt-1">+2% ↑</div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="bg-white shadow p-4 rounded">
          <div class="font-bold mb-4">Atividade Recente</div>
          <ul class="space-y-3">
            <li class="flex items-start">
              <i class="fas fa-bullhorn text-blue-500 mr-2"></i>
              <div>
                <div>Campanha "Promoção de Verão" enviada</div>
                <div class="text-sm text-gray-500">Hoje, 10:45 • 215 contatos • 85% taxa de entrega</div>
              </div>
            </li>
            <li class="flex items-start">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <div>
                <div>Novo bot de resposta automática criado</div>
                <div class="text-sm text-gray-500">Ontem, 15:30 • 3 fluxos configurados</div>
              </div>
            </li>
            <li class="flex items-start">
              <i class="fas fa-map-marker text-purple-500 mr-2"></i>
              <div>
                <div>Extração de contatos concluída</div>
                <div class="text-sm text-gray-500">23/11/2023, 09:15 • 78 novos contatos • Região: Centro</div>
              </div>
            </li>
          </ul>
        </div>

        <div class="bg-white shadow p-4 rounded">
          <div class="font-bold mb-4">Conversas Recentes</div>
          <ul class="space-y-3">
            <li class="flex justify-between items-center">
              <div>
                <div class="font-bold">Maria Silva</div>
                <div class="text-sm text-gray-500">Olá, gostaria de mais informações sobre...</div>
              </div>
              <div class="text-sm text-gray-500">10:23 <span class="bg-green-500 text-white rounded-full px-2 ml-2">2</span></div>
            </li>
            <li class="flex justify-between items-center">
              <div>
                <div class="font-bold">João Pereira</div>
                <div class="text-sm text-gray-500">Obrigado pelo atendimento! Vou...</div>
              </div>
              <div class="text-sm text-gray-500">09:45 <span class="bg-green-500 text-white rounded-full px-2 ml-2">1</span></div>
            </li>
            <li class="flex justify-between items-center">
              <div>
                <div class="font-bold">Mercado Central</div>
                <div class="text-sm text-gray-500">Prezado cliente, informamos que seu...</div>
              </div>
              <div class="text-sm text-gray-500">Ontem <span class="bg-green-500 text-white rounded-full px-2 ml-2">1</span></div>
            </li>
            <li class="flex justify-between items-center">
              <div>
                <div class="font-bold">Ana Ferreira</div>
                <div class="text-sm text-gray-500">Você: Enviei o orçamento por email...</div>
              </div>
              <div class="text-sm text-gray-500">22/11 <span class="bg-green-500 text-white rounded-full px-2 ml-2">0</span></div>
            </li>
          </ul>
          <div class="text-blue-500 text-sm mt-4 cursor-pointer">Ver todas as conversas</div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'WhatsAppDashboard'
};
</script>

<style scoped>
@import 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
</style>
