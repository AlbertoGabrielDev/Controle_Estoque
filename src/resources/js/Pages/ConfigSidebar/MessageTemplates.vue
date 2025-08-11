<template>
  <Sidebar>
    <div class="flex-1 bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto mt-8">
      <h2 class="text-2xl font-bold mb-6 text-gray-800">Modelos de Mensagem</h2>
      <form @submit.prevent="submit" class="flex flex-col md:flex-row md:items-end gap-4 mb-6">
        <div class="flex-1 flex flex-col gap-2">
          <label class="text-sm text-gray-700 font-medium">Nome do modelo</label>
          <input v-model="form.name" class="border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none transition" placeholder="Nome do modelo" />
        </div>
        <div class="flex-1 flex flex-col gap-2">
          <label class="text-sm text-gray-700 font-medium">Corpo da mensagem</label>
          <textarea v-model="form.body" rows="2" class="border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none transition resize-none" placeholder="Corpo da mensagem"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded px-6 py-2 font-semibold transition">Salvar</button>
      </form>
      <hr class="my-6" />
      <div v-if="templates.length === 0" class="text-gray-500 text-center py-8">Nenhum modelo cadastrado ainda.</div>
      <ul class="space-y-4">
        <li v-for="tpl in templates" :key="tpl.id" class="border border-gray-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between bg-gray-50 hover:shadow transition">
          <div>
            <strong class="block text-lg text-gray-800 mb-1">{{ tpl.name }}</strong>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ tpl.body }}</p>
          </div>
          <div class="mt-3 md:mt-0 flex gap-2">
            <button @click="edit(tpl)" class="text-blue-600 hover:underline text-xs font-medium">Editar</button>
            <button @click="del(tpl.id)" class="text-red-600 hover:underline text-xs font-medium">Excluir</button>
          </div>
        </li>
      </ul>
    </div>
  </Sidebar>
</template>

<script setup>
import { reactive } from 'vue'
import Sidebar from '../../Layouts/Sidebar.vue'

const props = defineProps({
  templates: Array
})

const form = reactive({
  id: null,
  name: '',
  body: ''
})

function submit() {
  if (form.id) {
    window.axios.put(`/verdurao/configuracoes/modelos-mensagem/${form.id}`, form)
      .then(() => window.location.reload())
  } else {
    window.axios.post('/verdurao/configuracoes/modelos-mensagem', form) 
      .then(() => window.location.reload())
  }
  form.id = null
  form.name = ''
  form.body = ''
}

function edit(tpl) {
  form.id = tpl.id
  form.name = tpl.name
  form.body = tpl.body
}

function del(id) {
  if (confirm('Tem certeza?')) {
    window.axios.delete(`/verdurao/configuracoes/modelos-mensagem/${id}`)
      .then(() => window.location.reload())
  }
}
</script>

<style scoped>
body {
  background: #f5f8fa;
}
</style>