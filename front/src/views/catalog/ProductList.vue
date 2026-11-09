<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { listCategories, listProducts, type ProductQuery } from '@/api/products'
import type { Category, Product } from '@/types/models'
const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const loading = ref(false)
const error = ref('')
const search = ref('')
const category = ref<number | ''>('')
const sort = ref<ProductQuery['sort']>('recent')
const page = ref(1)
const lastPage = ref(1)
async function load() {
  loading.value = true
  error.value = ''
  try {
    const result = await listProducts({
      search: search.value || undefined,
      category_id: category.value || undefined,
      sort: sort.value,
      page: page.value,
    })
    products.value = result.data
    page.value = result.current_page
    lastPage.value = result.last_page
  } catch {
    error.value = 'Não foi possível carregar os produtos.'
  } finally {
    loading.value = false
  }
}
function filter() {
  page.value = 1
  void load()
}
onMounted(async () => {
  try {
    categories.value = await listCategories()
  } catch {
    error.value = 'Não foi possível carregar as categorias.'
  }
  await load()
})
</script>
<template>
  <section>
    <h1>Catálogo de produtos</h1>
    <form @submit.prevent="filter">
      <p>
        <label for="search">Buscar produto</label><br /><input
          id="search"
          v-model="search"
          type="search"
        />
      </p>
      <p>
        <label for="category">Categoria</label><br /><select id="category" v-model="category">
          <option value="">Todas</option>
          <option v-for="item in categories" :key="item.id" :value="item.id">
            {{ item.name }}
          </option>
        </select>
      </p>
      <p>
        <label for="sort">Ordenar</label><br /><select id="sort" v-model="sort">
          <option value="recent">Mais recentes</option>
          <option value="price_asc">Menor preço</option>
          <option value="price_desc">Maior preço</option>
        </select>
      </p>
      <button type="submit">Pesquisar</button>
    </form>
    <p v-if="loading">Carregando produtos...</p>
    <p v-if="error" role="alert">{{ error }}</p>
    <p v-if="!loading && !products.length">Nenhum produto encontrado.</p>
    <ul>
      <li v-for="product in products" :key="product.id">
        <RouterLink :to="{ name: 'product', params: { id: product.id } }">{{
          product.name
        }}</RouterLink>
        — R$ {{ Number(product.price).toFixed(2) }} <span v-if="!product.stock">(Sem estoque)</span>
      </li>
    </ul>
    <nav v-if="lastPage > 1" aria-label="Paginação">
      <button
        :disabled="page <= 1 || loading"
        @click="
          page--,
          load()
        "
      >
        Anterior
      </button>
      Página {{ page }} de {{ lastPage }}
      <button
        :disabled="page >= lastPage || loading"
        @click="
          page++,
          load()
        "
      >
        Próxima
      </button>
    </nav>
  </section>
</template>
