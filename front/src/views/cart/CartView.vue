<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useCartStore } from '@/stores/cart'
const cart = useCartStore()
const error = ref('')
async function change(id: number, quantity: number) {
  error.value = ''
  try {
    if (quantity < 1) await cart.removeItem(id)
    else await cart.updateQuantity(id, quantity)
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }
    error.value = err.response?.data?.message ?? 'Não foi possível atualizar o carrinho.'
  }
}
async function remove(id: number) {
  await change(id, 0)
}
onMounted(async () => {
  try {
    await cart.fetchCart()
  } catch {
    error.value = 'Não foi possível carregar o carrinho.'
  }
})
</script>
<template>
  <section>
    <h1>Meu carrinho</h1>
    <p v-if="cart.loading">Carregando...</p>
    <p v-if="error" role="alert">{{ error }}</p>
    <p v-if="!cart.loading && !cart.items.length">
      Seu carrinho está vazio. <RouterLink to="/">Ver produtos</RouterLink>.
    </p>
    <ul>
      <li v-for="item in cart.items" :key="item.id">
        <h2>{{ item.name }}</h2>
        <p>Preço unitário: R$ {{ Number(item.price).toFixed(2) }}</p>
        <p>Subtotal: R$ {{ Number(item.subtotal).toFixed(2) }}</p>
        <p v-if="!item.available">Produto indisponível ou sem estoque.</p>
        <label :for="`qty-${item.id}`">Quantidade</label>
        <input
          :id="`qty-${item.id}`"
          type="number"
          min="0"
          :value="item.quantity"
          :disabled="!item.available"
          @change="change(item.id, Number(($event.target as HTMLInputElement).value))"
        /><button type="button" @click="remove(item.id)">Remover</button>
      </li>
    </ul>
    <p v-if="cart.hasUnavailableItems">
      Há produtos indisponíveis no carrinho. Remova-os para continuar.
    </p>
    <p>
      <strong>Total: R$ {{ Number(cart.total).toFixed(2) }}</strong>
    </p>
    <RouterLink v-if="cart.items.length && !cart.hasUnavailableItems" to="/checkout">Continuar para entrega</RouterLink>
  </section>
</template>
