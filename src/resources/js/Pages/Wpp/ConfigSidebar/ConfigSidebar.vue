<template>
  <Sidebar :activeTab="activeTab" @setActiveTab="setActiveTab">
    <div class="w-64 bg-white rounded-lg shadow p-4">
      <h3 class="font-semibold mb-4 text-lg">Configurações</h3>
      <ul>
        <li>
          <inertia-link :href="route('configuracoes.modelos-mensagem')" class="block py-2 px-2 hover:bg-gray-100 rounded">
            <i class="fas fa-comment-dots mr-2"></i> Modelos de Mensagem
          </inertia-link>
        </li>
        <!-- Adicione outros menus depois -->
      </ul>
    </div>
  </Sidebar>
</template>

<script setup>
import { Link as InertiaLink } from '@inertiajs/vue3'
import Sidebar from '../../../Layouts/Sidebar.vue'
</script>
