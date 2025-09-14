<template>
  <div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Contatos (WhatsApp)</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Coluna 1-2: lista de contatos -->
      <div class="lg:col-span-2 bg-white rounded-lg shadow p-4">
        <div class="flex items-center mb-3">
          <input v-model="q" type="text" placeholder="Buscar por nome/telefone"
            class="w-full border rounded px-3 py-2" />
          <button class="ml-3 px-3 py-2 border rounded" @click="clearSelection">Limpar seleção</button>
        </div>

        <div class="max-h-[60vh] overflow-y-auto divide-y">
          <label v-for="c in filtered" :key="c.phone" class="flex items-center gap-3 py-2">
            <input type="checkbox" v-model="selectedPhones" :value="c.phone" />
            <div>
              <div class="font-medium">{{ c.name }}</div>
              <div class="text-sm text-gray-500">{{ c.phone }}</div>
            </div>
          </label>
          <div v-if="!filtered.length" class="text-gray-500 py-8 text-center">Nenhum contato</div>
        </div>
      </div>

      <!-- Coluna 3: etiquetas -->
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="font-semibold mb-3">Etiquetas</h2>

        <div class="flex gap-2 mb-3">
          <input v-model="newLabel.name" type="text" placeholder="Nome da etiqueta"
            class="flex-1 border rounded px-3 py-2" />
          <input v-model="newLabel.labelColor" type="text" placeholder="#HEX" class="w-28 border rounded px-3 py-2" />
          <button class="px-3 py-2 bg-green-600 text-white rounded" :disabled="busy" @click="createLabel">Criar</button>
        </div>

        <div class="space-y-2">
          <div v-for="l in labelsLocal" :key="l.id" class="flex items-center justify-between border rounded px-3 py-2">
            <div class="flex items-center gap-2">
              <span class="inline-block w-3 h-3 rounded"
                :style="{ background: l.hexColor || l.color || '#ccc' }"></span>
              <span>{{ l.name }}</span>
            </div>
            <div class="flex items-center gap-2">
              <button class="px-2 py-1 text-xs border rounded" :disabled="!selectedPhones.length || busy"
                @click="applyLabel(l.id, 'add')">
                Aplicar ({{ selectedPhones.length }})
              </button>
              <button class="px-2 py-1 text-xs border rounded" :disabled="!selectedPhones.length || busy"
                @click="applyLabel(l.id, 'remove')">
                Remover ({{ selectedPhones.length }})
              </button>
              <button class="px-2 py-1 text-xs border rounded" @click="viewLabel(l.id)">
                Ver contatos
              </button>
              <button class="px-2 py-1 text-xs border rounded text-red-600" :disabled="busy" @click="deleteLabel(l.id)">
                Excluir
              </button>
            </div>
          </div>
          <div v-if="!labelsLocal.length" class="text-gray-500">Nenhuma etiqueta criada.</div>
        </div>

        <div v-if="flash?.message" class="mt-4 text-sm" :class="flash.ok ? 'text-green-700' : 'text-red-700'">
          {{ flash.message }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  contacts: { type: Array, default: () => [] },
  labels: { type: Array, default: () => [] }
})

const page = usePage()
const flash = computed(() => page.props.flash || null)

const q = ref('')
const selectedPhones = ref([])

const labelsLocal = ref([...(props.labels || [])])
const contactsLocal = ref([...(props.contacts || [])])
const selectedLabelId = ref(null)
const labelContacts = ref([])
const filtered = computed(() => {
  const term = q.value.trim().toLowerCase()
  if (!term) return contactsLocal.value
  return contactsLocal.value.filter(c =>
    (c.name || '').toLowerCase().includes(term) ||
    (c.phone || '').includes(term)
  )
})

function clearSelection() {
  selectedPhones.value = []
}

const busy = ref(false)
const newLabel = ref({ name: '', labelColor: '' })

async function createLabel() {
  if (!newLabel.value.name) return
  try {
    busy.value = true
    await axios.post(route('whatsapp.labels.store'), {
      name: newLabel.value.name,
      labelColor: newLabel.value.labelColor || null
    })
    // Recarrega página para refletir labels (ou poderia pedir ao backend apenas listar)
    router.reload({ only: ['labels'] })
    newLabel.value = { name: '', labelColor: '' }
  } finally {
    busy.value = false
  }
}

async function viewLabel(labelId) {
  selectedLabelId.value = labelId
  const { data } = await axios.get(route('whatsapp.labels.members', labelId))
  labelContacts.value = Array.isArray(data) ? data : []
  // Se quiser mostrar os contatos dessa etiqueta na lista principal:
  contactsLocal.value = labelContacts.value
}

async function applyLabel(labelId, type) {
  if (!selectedPhones.value.length) return
  try {
    busy.value = true
    await axios.post(route('whatsapp.labels.assign'), {
      labelId, type, phones: selectedPhones.value
    })
  } finally {
    busy.value = false
  }
}

async function deleteLabel(id) {
  try {
    busy.value = true
    await axios.delete(route('whatsapp.labels.destroy', id))
    router.reload({ only: ['labels'] })
  } finally {
    busy.value = false
  }
}
</script>

<style scoped>
/* básico */
</style>
