import { router } from '@inertiajs/vue3'
import { watch } from 'vue'

export function useQueryFilters(form, routeName, options = {}) {
  const {
    debounce = 200,
    preserveState = true,
    replace = true,
    only = [],
  } = options

  let timer = null

  const stop = watch(
    form,
    (value) => {
      clearTimeout(timer)
      timer = setTimeout(() => {
        router.get(route(routeName), value, { preserveState, replace, only })
      }, debounce)
    },
    { deep: true }
  )

  return () => {
    clearTimeout(timer)
    stop()
  }
}
