import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/client'
import type { Cart, CartItem } from '@/types/models'

interface CartResponse {
  items: CartItem[]
  total: string
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])
  const total = ref<string>('0.00')
  const loading = ref<boolean>(false)

  const itemCount = computed<number>(() =>
    items.value.reduce((sum, i) => sum + i.quantity, 0)
  )

  function apply(data: CartResponse): void {
    items.value = data.items
    total.value = data.total
  }

  async function fetchCart(): Promise<Cart> {
    loading.value = true
    try {
      const { data } = await api.get<CartResponse>('/api/cart')
      apply(data)
      return data as Cart
    } finally {
      loading.value = false
    }
  }

  async function addItem(productId: number, quantity = 1): Promise<CartResponse> {
    const { data } = await api.post<CartResponse>('/api/cart/items', {
      product_id: productId,
      quantity,
    })
    apply(data)
    return data
  }

  async function updateQuantity(itemId: number, quantity: number): Promise<void> {
    const { data } = await api.patch<CartResponse>(`/api/cart/items/${itemId}`, { quantity })
    apply(data)
  }

  async function removeItem(itemId: number): Promise<void> {
    const { data } = await api.delete<CartResponse>(`/api/cart/items/${itemId}`)
    apply(data)
  }

  async function clear(): Promise<void> {
    items.value = []
    total.value = '0.00'
  }

  return {
    items,
    total,
    loading,
    itemCount,
    fetchCart,
    addItem,
    updateQuantity,
    removeItem,
    clear,
  }
})