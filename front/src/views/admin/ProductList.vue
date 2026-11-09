<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { listAdminProducts, deleteProduct } from '@/api/products'
import type { Product } from '@/types/models'
const products = ref<Product[]>([])
const loading = ref(false)
const error = ref('')
async function load() {
  loading.value = true
  try {
    products.value = (await listAdminProducts({ sort: 'recent' })).data
  } catch {
    error.value = 'Não foi possível carregar os produtos.'
  } finally {
    loading.value = false
  }
}
async function remove(id: number) {
  if (!window.confirm('Desativar este produto?')) return
  try {
    await deleteProduct(id)
    await load()
  } catch {
    error.value = 'Não foi possível desativar o produto.'
  }
}
onMounted(load)
</script>
<template>
  <section>
    <h2>Produtos</h2>
    <p><RouterLink to="/admin/produtos/novo">Cadastrar produto</RouterLink></p>
    <p v-if="loading">Carregando...</p>
    <p v-if="error" role="alert">{{ error }}</p>
    <table>
      <thead>
        <tr>
          <th>Produto</th>
          <th>Preço</th>
          <th>Estoque</th>
          <th>Ativo</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in products" :key="p.id">
          <td>{{ p.name }}</td>
          <td>R$ {{ Number(p.price).toFixed(2) }}</td>
          <td>{{ p.stock }}</td>
          <td>{{ p.active ? 'Sim' : 'Não' }}</td>
          <td>
            <RouterLink :to="`/admin/produtos/${p.id}/editar`">Editar</RouterLink>
            <button type="button" @click="remove(p.id)" :disabled="!p.active">Desativar</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="!loading && !products.length">Nenhum produto cadastrado.</p>
  </section>
</template>
