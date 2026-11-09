<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { getProduct } from '@/api/products'
import { useCartStore } from '@/stores/cart'
import type { Product } from '@/types/models'
const route = useRoute()
const cart = useCartStore()
const product = ref<Product | null>(null)
const loading = ref(true)
const quantity = ref(1)
const message = ref('')
const error = ref('')
async function add() {
  if (!product.value) return
  message.value = ''
  error.value = ''
  try {
    await cart.addItem(product.value.id, quantity.value)
    message.value = 'Produto adicionado ao carrinho.'
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }
    error.value = err.response?.data?.message ?? 'Não foi possível adicionar o produto.'
  }
}
onMounted(async () => {
  try {
    product.value = await getProduct(String(route.params.id))
  } catch {
    error.value = 'Produto não encontrado.'
  } finally {
    loading.value = false
  }
})
</script>
<template>
  <section>
    <p v-if="loading">Carregando...</p>
    <p v-if="error" role="alert">{{ error }}</p>
    <article v-if="product">
      <h1>{{ product.name }}</h1>
      <img v-if="product.image" :src="product.image" :alt="product.name" />
      <p>{{ product.description }}</p>
      <p>Preço: R$ {{ Number(product.price).toFixed(2) }}</p>
      <p>Estoque: {{ product.stock }}</p>
      <p v-if="product.category">Categoria: {{ product.category.name }}</p>
      <form @submit.prevent="add">
        <label for="quantity">Quantidade</label>
        <input
          id="quantity"
          v-model.number="quantity"
          type="number"
          min="1"
          :max="product.stock"
          required
        /><button type="submit" :disabled="!product.active || product.stock < 1">
          Adicionar ao carrinho
        </button>
      </form>
      <p v-if="message" role="status">{{ message }}</p>
    </article>
    <p><RouterLink to="/">Voltar ao catálogo</RouterLink></p>
  </section>
</template>