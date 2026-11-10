<script setup lang="ts">
import { onMounted, ref } from 'vue'
import bannerImage from '@/assets/Banner.png'
import logoImage from '@/assets/LOGO BANNER.png'
import {
  listCategories,
  listProducts,
  type ProductQuery,
} from '@/api/products'
import type { Category, Product } from '@/types/models'
import {
  Search,
  SlidersHorizontal,
  ChevronDown,
  Package,
} from 'lucide-vue-next'

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

function formatPrice(price: string | number) {
  return Number(price).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
}

function productImage(image: string | null | undefined) {
  if (!image) return ''

  if (image.startsWith('http://') || image.startsWith('https://')) {
    return image
  }

  return image.startsWith('/') ? image : `/storage/${image}`
}

function previousPage() {
  if (page.value > 1 && !loading.value) {
    page.value--
    void load()
  }
}

function nextPage() {
  if (page.value < lastPage.value && !loading.value) {
    page.value++
    void load()
  }
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
  <main class="min-h-screen w-full bg-[#D9D9D9] font-HelveticaRounded">

    <!-- Banner / título -->
    <!-- Banner principal -->
  <section
    class="relative h-56 w-full overflow-hidden border-y-4 border-[#C9007A] sm:h-64 md:h-80"
  >
    <!-- Imagem de fundo -->
    <img
      :src="bannerImage"
      alt=""
      class="absolute inset-0 h-full w-full object-cover object-center"
    />

    <!-- Conteúdo do banner -->
    <div
      class="absolute inset-0 flex flex-col items-center justify-center gap-3 sm:items-end sm:justify-start sm:pr-[12%] sm:pt-5 md:pr-[14%] md:pt-6"
    >
      <!-- Logo -->
      <RouterLink to="/" class="block">
        <img
          :src="logoImage"
          alt="Enjoystick"
          class="w-48 object-contain drop-shadow-md sm:w-56 md:w-64 lg:w-72"
        />
      </RouterLink>

      <!-- Texto promocional -->
      <div
        class="flex items-center gap-3 text-xl font-bold text-white drop-shadow-[2px_2px_2px_#C9007A] sm:text-2xl"
      >
        <span>Controles acessíveis para todo mundo poder jogar!</span>
        <span class="text-3xl">➜</span>
      </div>
    </div>
  </section>
  <!-- Separador do catálogo -->
    <section
      class="flex min-h-12 w-full items-center justify-center bg-[#C9007A] px-4 py-2 text-white"
    >
      <h2 class="text-xl font-bold uppercase">
        Catálogo de produtos
      </h2>
    </section>
    <!-- Área de filtros -->
    <section class="w-full px-4 py-5">

      <!-- Pesquisa -->
      <form
        @submit.prevent="filter"
        class="flex w-full items-stretch overflow-hidden rounded-lg border-2 border-[#C9007A] bg-white focus-within:ring-2 focus-within:ring-[#C9007A]/30"
      >
        <div class="flex flex-1 items-center gap-3 px-4">
          <Search class="h-6 w-6 shrink-0 text-[#C9007A]" />

          <input
            v-model="search"
            type="search"
            placeholder="Buscar produtos..."
            aria-label="Buscar produtos"
            class="h-12 w-full min-w-0 border-0 bg-transparent text-lg text-[#C9007A] outline-none placeholder:text-gray-400 focus:ring-0"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="flex min-w-32 items-center justify-center gap-2 bg-[#C9007A] px-6 text-lg font-bold text-white transition-colors hover:bg-[#A80065] disabled:opacity-60"
        >
          <Search class="h-5 w-5" />
          Buscar
        </button>
      </form>
      
      <!-- Categorias e ordenação -->
      <div
        class="mt-5 flex flex-wrap items-end justify-between gap-4"
      >
        <div class="flex flex-col gap-2">
          <label
            for="category"
            class="text-lg font-bold text-[#C9007A]"
          >
            Categoria
          </label>

          <div class="relative">
            <select
              id="category"
              v-model="category"
              @change="filter"
              class="h-11 min-w-52 appearance-none rounded-lg border-2 border-[#C9007A] bg-white px-4 pr-10 text-base text-[#C9007A] outline-none focus:ring-2 focus:ring-[#C9007A]/30"
            >
              <option value="">Todas as categorias</option>

              <option
                v-for="item in categories"
                :key="item.id"
                :value="item.id"
              >
                {{ item.name }}
              </option>
            </select>

            <ChevronDown
              class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[#C9007A]"
            />
          </div>
        </div>

        <div class="flex flex-col gap-2">
          <label
            for="sort"
            class="flex items-center gap-2 text-lg font-bold text-[#C9007A]"
          >
            <SlidersHorizontal class="h-5 w-5" />
            Ordenar por
          </label>

          <div class="relative">
            <select
              id="sort"
              v-model="sort"
              @change="filter"
              class="h-11 min-w-52 appearance-none rounded-lg border-2 border-[#C9007A] bg-white px-4 pr-10 text-base text-[#C9007A] outline-none focus:ring-2 focus:ring-[#C9007A]/30"
            >
              <option value="recent">Mais recentes</option>
              <option value="price_asc">Menor preço</option>
              <option value="price_desc">Maior preço</option>
            </select>

            <ChevronDown
              class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[#C9007A]"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Produtos -->
    <section class="w-full px-4 py-5">

      <p
        v-if="loading"
        class="py-10 text-center text-xl font-bold text-[#C9007A]"
      >
        Carregando produtos...
      </p>

      <p
        v-if="error"
        role="alert"
        class="my-4 rounded-lg border-2 border-red-500 bg-white p-4 text-center text-red-600"
      >
        {{ error }}
      </p>

      <div
        v-if="!loading && !products.length && !error"
        class="py-16 text-center"
      >
        <Package class="mx-auto mb-3 h-16 w-16 text-[#C9007A]" />

        <p class="text-xl font-bold text-[#C9007A]">
          Nenhum produto encontrado.
        </p>

        <p class="mt-2 text-gray-600">
          Tente modificar sua pesquisa ou categoria.
        </p>
      </div>

      <div
        v-if="products.length"
        class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
      >
        <RouterLink
          v-for="product in products"
          :key="product.id"
          :to="{ name: 'product', params: { id: product.id } }"
          class="group flex min-w-0 flex-col overflow-hidden rounded-xl border-[3px] border-[#C9007A] bg-[#D9D9D9] shadow-md transition-transform hover:-translate-y-1 hover:shadow-lg"
        >
          <!-- Imagem -->
          <div
            class="flex aspect-square w-full items-center justify-center overflow-hidden bg-white p-2"
          >
            <img
              v-if="product.image"
              :src="productImage(product.image)"
              :alt="product.name"
              class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
            />

            <Package
              v-else
              class="h-24 w-24 text-[#C9007A]"
            />
          </div>

          <!-- Informações -->
          <div class="flex flex-1 flex-col gap-2 p-3">

            <div class="flex items-start justify-between gap-2">
              <h3
                class="min-w-0 flex-1 text-base font-bold uppercase text-[#C9007A]"
              >
                {{ product.name }}
              </h3>

              <span
                class="shrink-0 whitespace-nowrap text-base font-bold text-[#C9007A]"
              >
                {{ formatPrice(product.price) }}
              </span>
            </div>

            <p
              v-if="product.category"
              class="text-sm font-semibold text-[#C9007A]"
            >
              {{ product.category.name }}
            </p>

            <p
              v-if="product.description"
              class="line-clamp-2 text-sm text-gray-700"
            >
              {{ product.description }}
            </p>

            <div class="mt-auto flex items-center justify-between gap-2 pt-2">
              <span
                v-if="product.stock > 0"
                class="text-sm font-semibold text-green-700"
              >
                Em estoque
              </span>

              <span
                v-else
                class="text-sm font-bold text-red-600"
              >
                Sem estoque
              </span>

              <span
                class="rounded-md bg-[#C9007A] px-3 py-1 text-sm font-bold text-white transition-colors group-hover:bg-[#A80065]"
              >
                Ver produto
              </span>
            </div>
          </div>
        </RouterLink>
      </div>

      <!-- Paginação -->
      <nav
        v-if="lastPage > 1"
        aria-label="Paginação dos produtos"
        class="mt-8 flex flex-wrap items-center justify-center gap-5"
      >
        <button
          type="button"
          :disabled="page <= 1 || loading"
          @click="previousPage"
          class="rounded-lg border-2 border-[#C9007A] bg-white px-5 py-2 font-bold text-[#C9007A] transition-colors hover:bg-[#f1abd5] disabled:cursor-not-allowed disabled:opacity-40"
        >
          Anterior
        </button>

        <span class="font-bold text-[#C9007A]">
          Página {{ page }} de {{ lastPage }}
        </span>

        <button
          type="button"
          :disabled="page >= lastPage || loading"
          @click="nextPage"
          class="rounded-lg border-2 border-[#C9007A] bg-white px-5 py-2 font-bold text-[#C9007A] transition-colors hover:bg-[#f1abd5] disabled:cursor-not-allowed disabled:opacity-40"
        >
          Próxima
        </button>
      </nav>

    </section>
  </main>
</template>
