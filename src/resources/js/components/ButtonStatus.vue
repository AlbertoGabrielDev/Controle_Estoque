<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
const props = defineProps({
  modelId: { type: [String, Number], required: true },
  status: { type: [Boolean, Number], required: true },
  modelName: { type: String, required: true },
  canToggle: { type: Boolean, default: true },
  toggleRouteName: { type: String, default: '' }
})

const emit = defineEmits(['toggled']) // emite newStatus

const processing = ref(false)
const localStatus = ref(!!Number(props.status))

const csrf = computed(() => {
  const el = document.querySelector('meta[name="csrf-token"]')
  return el ? el.getAttribute('content') : ''
})

const url = computed(() => {
  if (props.toggleRouteName) {
    return route(props.toggleRouteName, { modelName: props.modelName, id: props.modelId })
  }
  return route(`${props.modelName}.status`, { modelName: props.modelName, id: props.modelId })
})

function cls(active) {
  return [
    'toggle-status',
    'flex items-center px-3 py-1 rounded-full text-sm transition-colors gap-1',
    active ? 'bg-green-400 hover:bg-green-400' : 'bg-red-400 hover:bg-red-400'
  ].join(' ')
}

async function toggle() {
  if (processing.value || !props.canToggle) return
  processing.value = true
  try {
    const res = await router.post(
      url.value,                         // mesma URL gerada pelo route()
      {},                                // payload vazio
      { preserveScroll: true, preserveState: true }
    )
    // depois atualize o estado local:
    localStatus.value = !localStatus.value
    emit('toggled', localStatus.value)
    const data = await res.json()
    if (data.status === 200) {
      localStatus.value = !!data.new_status
      emit('toggled', localStatus.value)
      showToast(localStatus.value ? 'Status ativado com sucesso!' : 'Status desativado com sucesso!', localStatus.value ? 'success' : 'error')
    } else {
      showToast(data.message || 'Erro ao atualizar status.', 'error')
    }
  } catch (e) {
    console.error(e)
    showToast('Erro ao atualizar status.', 'error')
  } finally {
    processing.value = false
  }
}

function showToast(message, type = 'success') {
  const id = 'toast-container'
  let container = document.getElementById(id)
  if (!container) {
    container = document.createElement('div')
    container.id = id
    container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2'
    document.body.appendChild(container)
  }
  const toast = document.createElement('div')
  toast.className = `toast flex items-center w-full max-w-xs p-4 rounded-lg shadow-sm text-sm gap-3
    ${type === 'success' ? 'bg-green-400 text-white' : 'bg-red-400 text-white'}`
  toast.innerHTML = `
    <span class="flex-1">${message}</span>
    <button type="button" class="ml-2 text-white/80 hover:text-white" onclick="this.closest('.toast')?.remove()">✕</button>
  `
  container.appendChild(toast)
  toast.style.opacity = '0'
  requestAnimationFrame(() => {
    toast.style.transition = 'opacity .3s'
    toast.style.opacity = '1'
  })
  setTimeout(() => {
    toast.style.transition = 'opacity .5s'
    toast.style.opacity = '0'
    setTimeout(() => toast.remove(), 500)
  }, 3000)
}
</script>

<template>
  <template v-if="canToggle">
    <button :class="cls(localStatus)" :title="localStatus ? 'Desativar' : 'Ativar'" :disabled="processing"
      @click="toggle">
      <i class="fa-solid fa-power-off text-xs text-gray-600"></i>
    </button>
  </template>
</template>
