<template>
  <Sidebar :activeTab="activeTab" @setActiveTab="setActiveTab">
    <div class="flex-1 overflow-y-auto bg-gray-100 p-6">
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
          <div v-for="stat in dashboardStats" :key="stat.title" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-700">{{ stat.title }}</h3>
              <span :class="stat.badgeClass">{{ stat.badgeText }}</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ stat.value }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ stat.subtitle }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Atividade Recente</h3>
            <div class="space-y-4">
              <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start">
                <div :class="activity.iconBgClass + ' rounded-full p-2 mr-4'">
                  <!-- Substitua por ícones reais, se quiser -->
                  <span v-html="activity.icon"></span>
                </div>
                <div>
                  <p class="font-medium text-gray-800">{{ activity.title }}</p>
                  <p class="text-sm text-gray-500">{{ activity.details }}</p>
                  <p class="text-xs text-gray-400 mt-1">{{ activity.time }}</p>
                </div>
              </div>
              <div v-if="recentActivities.length === 0" class="text-gray-500">Nenhuma atividade recente.</div>
            </div>
          </div>
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Conversas Recentes</h3>
            <div class="space-y-3">
              <div v-for="(conv, idx) in recentConversations" :key="idx"
                class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer contact-card">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                  <span class="font-bold text-lg text-blue-500">{{ conv.name[0] }}</span>
                </div>
                <div class="ml-3 flex-1">
                  <div class="flex justify-between items-center">
                    <p class="font-medium text-gray-800">{{ conv.name }}</p>
                    <p class="text-xs text-gray-500">{{ conv.time }}</p>
                  </div>
                  <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-500 break-words">{{ conv.lastMessage }}</p>
                    <span v-if="conv.unreadCount"
                      class="bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ conv.unreadCount }}</span>
                  </div>
                </div>
              </div>
              <div v-if="recentConversations.length === 0" class="text-gray-500">Nenhuma conversa recente.</div>
            </div>
            <button
              class="mt-4 w-full py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">Ver
              todas as conversas</button>
          </div>
        </div>
      </div>
    </div>
  </Sidebar>
</template>

<script>
import Sidebar from './Sidebar.vue'

export default {
  name: 'Dashboard',
  components: { Sidebar },
  data() {
    return {
      activeTab: 'dashboard',
      dashboardStats: [],
      recentActivities: [],
      recentConversations: [],
    }
  },
  methods: {
    async fetchDashboardStats() {
      // Endpoint Node: /dashboard/stats/sent-messages e /dashboard/stats/response-rate
      try {
        const [sentRes, respRes] = await Promise.all([
          fetch('http://localhost:3001/dashboard/stats/sent-messages').then(r => r.json()),
          fetch('http://localhost:3001/dashboard/stats/response-rate').then(r => r.json())
        ]);

        this.dashboardStats = [
          {
            title: 'Mensagens Enviadas',
            value: sentRes.sent || 0,
            subtitle: 'Últimos 30 dias',
            badgeText: '+0%',
            badgeClass: 'text-green-500 bg-green-100 rounded-full px-3 py-1 text-sm',
          },
          {
            title: 'Taxa de Resposta',
            value: (respRes.rate || 0) + '%',
            subtitle: 'Últimos 30 dias',
            badgeText: '+0%',
            badgeClass: 'text-green-500 bg-green-100 rounded-full px-3 py-1 text-sm',
          },
          {
            title: 'Contatos Ativos',
            value: sentRes.contactsActive || 0,
            subtitle: 'Últimos 30 dias',
            badgeText: '+0%',
            badgeClass: 'text-yellow-500 bg-yellow-100 rounded-full px-3 py-1 text-sm',
          }
        ]
      } catch (err) {
        this.dashboardStats = []
      }
    },
    async fetchRecentActivities() {
      try {
        const activities = await fetch('http://localhost:3001/dashboard/activity/recent').then(r => r.json())
        // Ajuste conforme o retorno real do seu backend!
        this.recentActivities = (activities || []).map((item, idx) => ({
          ...item,
          id: idx,
          icon: '<svg class="w-5 h-5"><circle cx="12" cy="12" r="10" fill="#ddd"/></svg>',
          iconBgClass: 'bg-blue-100'
        }))
      } catch (err) {
        this.recentActivities = []
      }
    },
    async fetchRecentConversations() {
      try {
        const convs = await fetch('http://localhost:3001/dashboard/chats/recent').then(r => r.json())
        this.recentConversations = (convs || []).map(c => ({
          name: c.name,
          time: c.timestamp ? new Date(c.timestamp).toLocaleString() : '',
          lastMessage: c.lastMessage || '',
          unreadCount: c.unreadCount || 0
        }))
      } catch (err) {
        this.recentConversations = []
      }
    },
    setActiveTab(tab) {
      this.activeTab = tab
    }
  },
  mounted() {
    this.fetchDashboardStats()
    this.fetchRecentActivities()
    this.fetchRecentConversations()
  }
}
</script>
