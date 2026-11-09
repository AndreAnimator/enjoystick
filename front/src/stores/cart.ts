import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as cartApi from '@/api/cart'
import type { Cart, CartLine } from '@/types/models'
export const useCartStore = defineStore('cart', () => {
  const items = ref<CartLine[]>([])
  const total = ref('0.00')
  const token = ref<string | null>(localStorage.getItem('enjoystick_cart_token'))
  const hasUnavailableItems = ref(false)
  const loading = ref(false)
  const itemCount = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0))
  function apply(cart: Cart): void {
    items.value = cart.items
    total.value = cart.total
    hasUnavailableItems.value = Boolean(cart.hasUnavailableItems)
    if (cart.token) {
      token.value = cart.token
      localStorage.setItem('enjoystick_cart_token', cart.token)
    }
  }
  async function fetchCart(): Promise<void> {
    loading.value = true
    try {
      apply(await cartApi.getCart())
    } finally {
      loading.value = false
    }
  }
  async function addItem(productId: number, quantity = 1): Promise<void> {
    await cartApi.addCartItem(productId, quantity)
    await fetchCart()
  }
  async function updateQuantity(itemId: number, quantity: number): Promise<void> {
    await cartApi.updateCartItem(itemId, quantity)
    await fetchCart()
  }
  async function removeItem(itemId: number): Promise<void> {
    await cartApi.removeCartItem(itemId)
    await fetchCart()
  }
  function clear(): void {
    items.value = []
    total.value = '0.00'
    hasUnavailableItems.value = false
  }
  return {
    items,
    total,
    token,
    loading,
    hasUnavailableItems,
    itemCount,
    fetchCart,
    addItem,
    updateQuantity,
    removeItem,
    clear,
  }
})
