<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { listProducts, listCategories, type ProductQuery } from '@/api/products'
import type { Category, Product } from '@/types/models'

const route = useRoute()
const router = useRouter()

const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const loading = ref<boolean>(false)
const currentPage = ref<number>(1)
const lastPage = ref<number>(1)

const filters = ref<{
  search: string
  category_id: number | null
  sort: ProductQuery['sort'] | null
}>({
  search: (route.query.search as string) ?? '',
  category_id: route.query.category_id ? Number(route.query.category_id) : null,
  sort: (route.query.sort as ProductQuery['sort']) ?? null,
})

async function loadProducts(): Promise<void> {
  loading.value = true
  try {
    const query: ProductQuery = {
      search: filters.value.search || undefined,
      category_id: filters.value.category_id ?? undefined,
      sort: filters.value.sort ?? undefined,
      page: currentPage.value,
    }
    const res = await listProducts(query)
    products.value = res.data
    currentPage.value = res.current_page
    lastPage.value = res.last_page
  } finally {
    loading.value = false
  }
}

function applyFilters(): void {
  currentPage.value = 1
  router.replace({ query: { ...filters.value } })
  loadProducts()
}

function goToPage(page: number): void {
  currentPage.value = page
  loadProducts()
}

onMounted(async () => {
  categories.value = await listCategories()
  await loadProducts()
})

watch(() => route.query, applyFilters)
</script>

<template>
  <section>
    <h1>Catálogo</h1>

    <form @submit.prevent="applyFilters">
      <input v-model="filters.search" type="text" placeholder="Buscar" />

      <select v-model="filters.category_id">
        <option :value="null">Todas as categorias</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>

      <select v-model="filters.sort">
        <option :value="null">Padrão</option>
        <option value="price_asc">Preço crescente</option>
        <option value="price_desc">Preço decrescente</option>
        <option value="recent">Mais recentes</option>
      </select>

      <button type="submit">Filtrar</button>
    </form>

    <p v-if="loading">Carregando...</p>
    <p v-else-if="products.length === 0">Nenhum produto encontrado.</p>

    <ul v-else>
      <li v-for="p in products" :key="p.id">
        <RouterLink :to="{ name: 'product', params: { id: p.id } }">
          {{ p.name }} — R$ {{ p.price }}
        </RouterLink>
        <span v-if="p.stock === 0"> (Indisponível)</span>
      </li>
    </ul>

    <nav>
      <button :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">Anterior</button>
      <span>Página {{ currentPage }} de {{ lastPage }}</span>
      <button :disabled="currentPage >= lastPage" @click="goToPage(currentPage + 1)">Próxima</button>
    </nav>
  </section>
</template>