import axios from 'axios'
import { computed, ref } from 'vue'

export function useCart(options = {}) {
  const requireClient = options.requireClient !== false
  const userId = options.userId ?? null
  const client = ref('')
  const serverCart = ref(null)
  const stagedItems = ref([])
  const busy = ref(false)
  const finalizing = ref(false)

  const hasServerCart = computed(() => !!serverCart.value)

  const cartItems = computed(() => {
    if (serverCart.value?.items?.length) {
      return serverCart.value.items.map((item) => ({
        kind: 'server',
        rowKey: `server-${item.id}`,
        id: Number(item.id),
        id_produto: Number(item.id_produto ?? 0),
        id_estoque: item.id_estoque_fk ? Number(item.id_estoque_fk) : null,
        name: item.nome_produto,
        code: item.cod_produto,
        unitPrice: normalizeMoney(item.preco_unit),
        quantity: Number(item.quantidade || 0),
        subtotal: normalizeMoney(item.subtotal_valor),
      }))
    }

      return stagedItems.value.map((item) => ({
        kind: 'local',
        rowKey: `local-${item.localId}`,
        id: item.localId,
        id_produto: Number(item.id_produto),
        id_estoque: item.id_estoque ?? null,
        name: item.nome_produto,
        code: item.cod_produto,
        unitPrice: normalizeMoney(item.preco_venda),
        quantity: Number(item.quantidade || 0),
        subtotal: normalizeMoney(item.preco_venda) * Number(item.quantidade || 0),
    }))
  })

  const cartTotal = computed(() => {
    if (serverCart.value) {
      return normalizeMoney(serverCart.value.total_valor)
    }

    return stagedItems.value.reduce((total, item) => {
      return total + normalizeMoney(item.preco_venda) * Number(item.quantidade || 0)
    }, 0)
  })

  function setClient(value) {
    const nextClient = String(value ?? '').trim()
    if (nextClient !== client.value) {
      client.value = nextClient
      serverCart.value = null
    }
  }

  function resolveClientKey() {
    const trimmed = String(client.value ?? '').trim()
    if (trimmed) {
      return trimmed
    }

    if (!requireClient && userId) {
      const key = `ANON-${userId}`
      return key.length > 20 ? key.slice(0, 20) : key
    }

    return ''
  }

  async function fetchProduct(payload) {
    const { data } = await axios.post(route('buscar.produto'), payload)
    if (!data?.success) {
      throw new Error(data?.message || 'Produto nao encontrado.')
    }

    const opcoes = Array.isArray(data?.opcoes) ? data.opcoes : []
    const produto = data?.produto ?? null

    if (!produto && opcoes.length === 0) {
      throw new Error(data?.message || 'Produto nao encontrado.')
    }

    return { produto, opcoes }
  }

  function addProductToStaged(product, quantity = 1) {
    const estoqueId = product.id_estoque ? Number(product.id_estoque) : null
    const existing = stagedItems.value.find((item) => {
      if (estoqueId) {
        return Number(item.id_estoque) === estoqueId
      }
      return Number(item.id_produto) === Number(product.id_produto) && !item.id_estoque
    })
    if (existing) {
      existing.quantidade += quantity
      return
    }

    stagedItems.value.push({
      localId: Date.now() + Math.round(Math.random() * 1000),
      id_produto: Number(product.id_produto),
      id_estoque: estoqueId,
      cod_produto: product.cod_produto,
      nome_produto: product.nome_produto,
      preco_venda: normalizeMoney(product.preco_venda),
      quantidade: quantity,
    })
  }

  async function addProduct(product, quantity = 1) {
    const clientKey = resolveClientKey()
    if (!clientKey) {
      addProductToStaged(product, quantity)
      const message = requireClient
        ? 'Produto adicionado no carrinho local. Defina o cliente para sincronizar.'
        : 'Produto adicionado no carrinho local.'
      notify(message, 'info')
      return true
    }

    busy.value = true
    try {
      const { data } = await axios.post(route('adicionar.venda'), {
        client: clientKey,
        id_produto: Number(product.id_produto),
        id_estoque: product.id_estoque ? Number(product.id_estoque) : null,
        quantidade: quantity,
      })

      if (!data?.success) {
        notify(data?.message || 'Falha ao adicionar produto.', 'error')
        return false
      }

      serverCart.value = data.cart ?? null
      notify('Produto adicionado ao carrinho.', 'success')
      return true
    } catch (error) {
      notify(messageFromError(error, 'Falha ao adicionar produto.'), 'error')
      return false
    } finally {
      busy.value = false
    }
  }

  async function addByManualCode(code) {
    const normalizedCode = String(code ?? '').trim()
    if (!normalizedCode) {
      notify('Informe um codigo de produto.', 'warning')
      return { added: false, opcoes: [] }
    }

    try {
      const { produto, opcoes } = await fetchProduct({ codigo_produto: normalizedCode })
      if (opcoes.length > 1) {
        return { added: false, opcoes }
      }

      const selecionado = produto ?? opcoes[0]
      if (!selecionado) {
        notify('Produto nao encontrado.', 'error')
        return { added: false, opcoes: [] }
      }

      const added = await addProduct(selecionado, 1)
      return { added, opcoes: [] }
    } catch (error) {
      notify(messageFromError(error, 'Produto nao encontrado.'), 'error')
      return { added: false, opcoes: [] }
    }
  }

  async function addByQrCode(qrCode) {
    const normalizedCode = String(qrCode ?? '').trim()
    if (!normalizedCode) {
      notify('Leitura de QR code invalida.', 'warning')
      return false
    }

    try {
      const { produto } = await fetchProduct({ codigo_qr: normalizedCode })
      if (!produto) {
        notify('Produto nao encontrado pelo QR code.', 'error')
        return false
      }

      return await addProduct(produto, 1)
    } catch (error) {
      notify(messageFromError(error, 'Produto nao encontrado pelo QR code.'), 'error')
      return false
    }
  }

  async function addSelectedProduct(product, quantity = 1) {
    if (!product) {
      return false
    }

    return await addProduct(product, quantity)
  }

  async function loadCart() {
    const clientKey = resolveClientKey()
    if (!clientKey) {
      if (requireClient) {
        notify('Informe o cliente para carregar o carrinho.', 'warning')
      }
      return null
    }

    busy.value = true
    try {
      const { data } = await axios.post(route('carrinho.venda'), { client: clientKey })
      if (!data?.success) {
        notify(data?.message || 'Falha ao carregar carrinho.', 'error')
        return null
      }

      serverCart.value = data.cart ?? null
      return serverCart.value
    } catch (error) {
      notify(messageFromError(error, 'Falha ao carregar carrinho.'), 'error')
      return null
    } finally {
      busy.value = false
    }
  }

  async function loadCartMerge() {
    const clientKey = resolveClientKey()
    if (!clientKey) {
      if (requireClient) {
        notify('Informe o cliente para sincronizar o carrinho.', 'warning')
      }
      return null
    }

    const currentCart = await loadCart()
    if (!currentCart) {
      return null
    }

    if (stagedItems.value.length === 0) {
      notify('Carrinho carregado com sucesso.', 'success')
      return currentCart
    }

    busy.value = true
    try {
      for (const item of stagedItems.value) {
        await axios.post(route('adicionar.venda'), {
          client: clientKey,
          id_produto: Number(item.id_produto),
          id_estoque: item.id_estoque ? Number(item.id_estoque) : null,
          quantidade: Number(item.quantidade || 1),
        })
      }

      stagedItems.value = []
      notify('Itens locais sincronizados no carrinho.', 'success')
    } catch (error) {
      notify(messageFromError(error, 'Falha ao sincronizar itens locais.'), 'error')
    } finally {
      busy.value = false
    }

    return await loadCart()
  }

  async function changeQuantity(item, delta) {
    if (!item) {
      return
    }

    if (item.kind === 'local') {
      const localItem = stagedItems.value.find((entry) => entry.localId === item.id)
      if (!localItem) {
        return
      }

      const nextQuantity = Number(localItem.quantidade || 0) + Number(delta || 0)
      if (nextQuantity <= 0) {
        stagedItems.value = stagedItems.value.filter((entry) => entry.localId !== item.id)
      } else {
        localItem.quantidade = nextQuantity
      }

      return
    }

    const clientKey = resolveClientKey()
    if (!clientKey) {
      if (requireClient) {
        notify('Cliente nao informado.', 'warning')
      }
      return
    }

    const nextQuantity = Math.max(0, Number(item.quantity || 0) + Number(delta || 0))

    busy.value = true
    try {
      const { data } = await axios.post(route('atualizar_quantidade.venda'), {
        client: clientKey,
        cart_item_id: Number(item.id),
        quantidade: nextQuantity,
      })

      if (!data?.success) {
        notify(data?.message || 'Falha ao atualizar quantidade.', 'error')
        return
      }

      serverCart.value = data.cart ?? null
    } catch (error) {
      notify(messageFromError(error, 'Falha ao atualizar quantidade.'), 'error')
    } finally {
      busy.value = false
    }
  }

  async function removeItem(item) {
    if (!item) {
      return
    }

    if (item.kind === 'local') {
      stagedItems.value = stagedItems.value.filter((entry) => entry.localId !== item.id)
      return
    }

    const clientKey = resolveClientKey()
    if (!clientKey) {
      if (requireClient) {
        notify('Cliente nao informado.', 'warning')
      }
      return
    }

    busy.value = true
    try {
      const { data } = await axios.post(route('remover.venda'), {
        client: clientKey,
        cart_item_id: Number(item.id),
      })

      if (!data?.success) {
        notify(data?.message || 'Falha ao remover item.', 'error')
        return
      }

      serverCart.value = data.cart ?? null
      notify('Item removido do carrinho.', 'info')
    } catch (error) {
      notify(messageFromError(error, 'Falha ao remover item.'), 'error')
    } finally {
      busy.value = false
    }
  }

  async function finalizeSale() {
    const clientKey = resolveClientKey()
    if (!clientKey) {
      if (requireClient) {
        notify('Informe o cliente antes de finalizar a venda.', 'warning')
      }
      return false
    }

    finalizing.value = true
    try {
      if (stagedItems.value.length > 0) {
        await loadCartMerge()
      }

      if (!serverCart.value?.items?.length) {
        await loadCart()
      }

      if (!serverCart.value?.items?.length) {
        notify('Carrinho vazio. Adicione itens antes de finalizar.', 'warning')
        return false
      }

      const { data } = await axios.post(route('registrar.venda'), { client: clientKey })
      if (!data?.success) {
        const detailText = stockDetails(data?.detalhes ?? [])
        notify(detailText ? `${data?.message || 'Falha ao finalizar venda.'} ${detailText}` : (data?.message || 'Falha ao finalizar venda.'), 'error')
        return false
      }

      serverCart.value = null
      stagedItems.value = []
      notify(data?.message || 'Venda registrada com sucesso.', 'success')
      return true
    } catch (error) {
      const payload = error?.response?.data ?? {}
      const detailText = stockDetails(payload?.detalhes ?? payload?.produtos_sem_estoque ?? [])
      const baseMessage = payload?.message || 'Erro ao processar a venda.'
      notify(detailText ? `${baseMessage} ${detailText}` : baseMessage, 'error')
      return false
    } finally {
      finalizing.value = false
    }
  }

  return {
    client,
    setClient,
    hasServerCart,
    cartItems,
    cartTotal,
    busy,
    finalizing,
    addByManualCode,
    addByQrCode,
    addSelectedProduct,
    loadCartMerge,
    changeQuantity,
    removeItem,
    finalizeSale,
  }
}

function notify(message, type = 'success') {
  if (!message) {
    return
  }

  if (typeof window !== 'undefined' && typeof window.showToast === 'function') {
    window.showToast(message, type)
    return
  }

  const level = type === 'error' ? 'error' : 'log'
  // eslint-disable-next-line no-console
  console[level](message)
}

function messageFromError(error, fallback = 'Erro inesperado.') {
  return error?.response?.data?.message || error?.message || fallback
}

function normalizeMoney(value) {
  return Number(value || 0)
}

function stockDetails(details = []) {
  if (!Array.isArray(details) || details.length === 0) {
    return ''
  }

  return details
    .map((item) => {
      const id = item?.id_produto ?? '-'
      const requested = item?.quantidade_solicitada ?? '-'
      const available = item?.estoque_atual ?? '-'
      return `Produto ${id}: solicitado ${requested}, disponivel ${available}`
    })
    .join(' | ')
}
