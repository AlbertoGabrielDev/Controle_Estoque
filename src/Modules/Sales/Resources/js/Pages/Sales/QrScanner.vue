<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['decoded'])

const videoRef = ref(null)
const running = ref(false)
const detectorSupported = ref(false)

let detector = null
let stream = null
let intervalId = null
let reading = false

onMounted(() => {
  detectorSupported.value = typeof window !== 'undefined' && 'BarcodeDetector' in window
})

onBeforeUnmount(async () => {
  await stop()
})

async function start() {
  if (props.disabled || running.value) {
    return
  }

  if (!navigator?.mediaDevices?.getUserMedia) {
    notify(t('Camera not available in this browser.'), 'warning')
    return
  }

  try {
    stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: { ideal: 'environment' } },
      audio: false,
    })

    videoRef.value.srcObject = stream
    await videoRef.value.play()

    running.value = true

    if (!detectorSupported.value) {
      notify(t('Automatic reading not supported. Use manual code.'), 'warning')
      return
    }

    try {
      detector = new window.BarcodeDetector({ formats: ['qr_code'] })
    } catch (_) {
      detector = new window.BarcodeDetector()
    }

    intervalId = window.setInterval(scanFrame, 350)
  } catch (_) {
    notify(t('Could not start camera.'), 'error')
    await stop()
  }
}

async function stop() {
  if (intervalId) {
    clearInterval(intervalId)
    intervalId = null
  }

  if (stream) {
    stream.getTracks().forEach((track) => track.stop())
    stream = null
  }

  if (videoRef.value) {
    videoRef.value.srcObject = null
  }

  detector = null
  running.value = false
  reading = false
}

async function scanFrame() {
  if (!detector || !running.value || reading) {
    return
  }

  if (!videoRef.value || videoRef.value.readyState < 2) {
    return
  }

  reading = true
  try {
    const barcodes = await detector.detect(videoRef.value)
    if (!barcodes?.length) {
      return
    }

    const code = String(barcodes[0]?.rawValue ?? '').trim()
    if (!code) {
      return
    }

    emit('decoded', code)
    notify(t('QR code read successfully.'), 'success')
    await stop()
  } catch (_) {
    // Ignora falhas pontuais de frame.
  } finally {
    reading = false
  }
}

function notify(message, type = 'success') {
  if (typeof window !== 'undefined' && typeof window.showToast === 'function') {
    window.showToast(message, type)
  }
}
</script>

<template>
  <section class="mt-6">
    <div class="flex flex-wrap items-center gap-2">
      <button
        type="button"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg disabled:opacity-60"
        :disabled="disabled || running"
        @click="start"
      >
        {{ $t('Start Camera') }}
      </button>

      <button
        v-if="running"
        type="button"
        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg"
        @click="stop"
      >
        {{ $t('Stop Camera') }}
      </button>

      <span class="text-sm text-gray-500">
        {{ $t("Point the camera at the product's QR code.") }}
      </span>
    </div>

    <div v-if="running" class="mt-4 w-full max-w-md rounded-lg border bg-black/90 overflow-hidden">
      <video ref="videoRef" class="w-full h-72 object-cover" playsinline muted />
    </div>
  </section>
</template>
