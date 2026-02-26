<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  links: { type: Array, default: () => [] },
})
</script>

<template>
  <div v-if="props.links.length > 1" class="flex flex-wrap gap-2">
    <template v-for="(link, index) in props.links" :key="index">
      <Link
        v-if="link.url"
        :href="link.url"
        class="px-3 py-1 border rounded text-sm"
        :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'text-slate-700 hover:bg-slate-50'"
        v-html="link.label"
      />
      <span v-else class="px-3 py-1 border rounded text-sm text-slate-400" v-html="link.label"></span>
    </template>
  </div>
</template>
