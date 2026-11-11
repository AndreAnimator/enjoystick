import api from './client'
import type { Cart, CartLine } from '@/types/models'

interface CartSummaryRaw { items: Array<[number, string, string | number, number, string | number, boolean, number]>; total: string | number; token: string; hasUnavailableItems: boolean }
export async function getCart(): Promise<Cart> {
  const { data } = await api.get<CartSummaryRaw>('/api/cart')
  const cart: Cart = {
    items: data.items.map((line) => ({ id: line[6], product_id: line[0], name: line[1], price: String(line[2]), quantity: line[3], subtotal: String(line[4]), available: line[5] })),
    total: String(data.total), token: data.token, hasUnavailableItems: data.hasUnavailableItems,
  }
  return cart
}
export async function addCartItem(productId: number, quantity: number): Promise<void> {
  await api.post('/api/cart/items', { product_id: productId, quantity })
}
export async function updateCartItem(id: number, quantity: number): Promise<void> {
  await api.patch(`/api/cart/items/${id}`, { quantity })
}
export async function removeCartItem(id: number): Promise<void> { await api.delete(`/api/cart/items/${id}`) }
