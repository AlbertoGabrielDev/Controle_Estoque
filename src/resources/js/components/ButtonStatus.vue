<script setup>
import axios from 'axios'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelId: { type: [String, Number], required: true },
  status: { type: [Boolean, Number], required: true },
  modelName: { type: String, required: true },
  canToggle: { type: Boolean, default: true },
  toggleRouteName: { type: String, default: '' },
})

const emit = defineEmits(['toggled'])

const processing = ref(false)
const localStatus = ref(Number(props.status) === 1 || props.status === true)

watch(
  () => props.status,
  (value) => {
    localStatus.value = Number(value) === 1 || value === true
  }
)

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
    active ? 'bg-green-500 hover:bg-green-600' : 'bg-red-400 hover:bg-red-500',
  ].join(' ')
}

async function toggle() {
  if (processing.value || !props.canToggle) return

  processing.value = true
  try {
    const { data } = await axios.post(
      url.value,
      {},
      {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
          ...(csrf.value ? { 'X-CSRF-TOKEN': csrf.value } : {}),
        },
      }
    )

    if (data?.status === 200) {
      localStatus.value = Number(data.new_status) === 1 || data.new_status === true
      emit('toggled', localStatus.value)
      showToast(
        localStatus.value ? 'Status ativado com sucesso!' : 'Status desativado com sucesso!',
        localStatus.value ? 'success' : 'warning'
      )
      return
    }

    showToast(data?.message || 'Erro ao atualizar status.', 'error')
  } catch (error) {
    console.error('toggle-status failed', error)
    showToast('Erro ao atualizar status.', 'error')
  } finally {
    processing.value = false
  }
}

function showToast(message, type = 'success') {
  if (typeof window.showToast === 'function') {
    window.showToast(message, type)
    return
  }

  const id = 'toast-container'
  let container = document.getElementById(id)
  if (!container) {
    container = document.createElement('div')
    container.id = id
    container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2'
    document.body.appendChild(container)
  }
  const toast = document.createElement('div')
  toast.className = `toast flex items-center w-full max-w-xs p-4 rounded-lg shadow-sm text-sm gap-3 ${
    type === 'success' ? 'bg-green-400 text-white' : 'bg-red-400 text-white'
  }`
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
  <button
    v-if="canToggle"
    :class="cls(localStatus)"
    :title="localStatus ? 'Desativar' : 'Ativar'"
    :disabled="processing"
    @click="toggle"
  >
    <i class="fa-solid fa-power-off text-xs text-white"></i>
  </button>
</template>
