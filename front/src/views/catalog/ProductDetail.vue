<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { getProduct } from '@/api/products'
import { useCartStore } from '@/stores/cart'
import type { Product } from '@/types/models'
import {
  ShoppingCart,
  Package,
  ArrowLeft,
  Tag,
  Boxes,
  CheckCircle2,
  AlertCircle,
} from 'lucide-vue-next'

const route = useRoute()
const cart = useCartStore()

const product = ref<Product | null>(null)
const loading = ref(true)
const quantity = ref(1)
const message = ref('')
const error = ref('')
const adding = ref(false)

const available = computed(() => {
  return !!product.value &&
    Boolean(product.value.active) &&
    product.value.stock > 0
})

const formattedPrice = computed(() => {
  if (!product.value) return ''

  return Number(product.value.price).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
})

function formatPrice(price: number) {
  return price.toLocaleString('pt-BR', {
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

async function add() {
  if (!product.value || adding.value) return

  message.value = ''
  error.value = ''

  if (!available.value) {
    error.value = 'Este produto não está disponível.'
    return
  }

  if (
    quantity.value < 1 ||
    quantity.value > product.value.stock
  ) {
    error.value = 'Quantidade inválida ou superior ao estoque disponível.'
    return
  }

  adding.value = true

  try {
    await cart.addItem(product.value.id, quantity.value)
    message.value = 'Produto adicionado ao carrinho.'
  } catch (e: unknown) {
    const err = e as {
      response?: {
        data?: {
          message?: string
        }
      }
    }

    error.value =
      err.response?.data?.message ??
      'Não foi possível adicionar o produto.'
  } finally {
    adding.value = false
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
  <main class="min-h-screen w-full bg-[#D9D9D9] font-HelveticaRounded">

    <!-- Cabeçalho da página -->
    <div class="flex w-full items-center bg-[#C9007A] px-5 py-3">
      <RouterLink
        to="/"
        class="flex items-center gap-2 text-lg font-bold text-white transition-colors hover:text-pink-200"
      >
        <ArrowLeft class="h-5 w-5" />
        Voltar ao catálogo
      </RouterLink>
    </div>

    <!-- Carregamento -->
    <section
      v-if="loading"
      class="flex min-h-96 items-center justify-center"
    >
      <p class="text-xl font-bold text-[#C9007A]">
        Carregando produto...
      </p>
    </section>

    <!-- Erro -->
    <section
      v-else-if="error && !product"
      class="mx-auto flex min-h-96 max-w-3xl flex-col items-center justify-center gap-4 px-4"
    >
      <AlertCircle class="h-16 w-16 text-[#C9007A]" />

      <p class="text-center text-xl font-bold text-[#C9007A]">
        {{ error }}
      </p>

      <RouterLink
        to="/"
        class="rounded-lg bg-[#C9007A] px-6 py-3 font-bold text-white hover:bg-[#A80065]"
      >
        Voltar ao catálogo
      </RouterLink>
    </section>

    <!-- Produto -->
    <article
      v-else-if="product"
      class="mx-auto grid w-full max-w-[1500px] grid-cols-1 gap-5 p-4 lg:grid-cols-[minmax(280px,0.95fr)_minmax(300px,1.35fr)_minmax(250px,0.65fr)] lg:gap-6 lg:p-6"
    >

      <!-- Imagem do produto -->
      <section class="flex min-w-0 flex-col gap-3">

        <div
          class="flex aspect-square w-full items-center justify-center overflow-hidden rounded-2xl border-[3px] border-[#C9007A] bg-white p-5 shadow-md"
        >
          <img
            v-if="product.image"
            :src="productImage(product.image)"
            :alt="product.name"
            class="h-full w-full object-contain"
          />

          <div
            v-else
            class="flex flex-col items-center gap-3 text-[#C9007A]"
          >
            <Package class="h-28 w-28" />

            <span class="font-bold">
              Imagem indisponível
            </span>
          </div>
        </div>

        <p class="text-center text-sm font-semibold text-[#C9007A]">
          {{ product.name }}
        </p>

      </section>

      <!-- Informações do produto -->
      <section class="flex min-w-0 flex-col gap-4">

        <!-- Nome e categoria -->
        <div class="border-b-2 border-[#C9007A]/30 pb-4">

          <h1 class="text-3xl font-bold text-[#C9007A]">
            {{ product.name }}
          </h1>

          <p
            v-if="product.category"
            class="mt-3 flex items-center gap-2 text-base font-semibold text-[#A80065]"
          >
            <Tag class="h-5 w-5" />
            {{ product.category.name }}
          </p>

          <div class="mt-4 flex flex-wrap items-center gap-3">

            <span
              v-if="available"
              class="flex items-center gap-1 rounded-md bg-green-100 px-3 py-1 text-sm font-bold text-green-700"
            >
              <CheckCircle2 class="h-4 w-4" />
              Disponível
            </span>

            <span
              v-else
              class="flex items-center gap-1 rounded-md bg-red-100 px-3 py-1 text-sm font-bold text-red-700"
            >
              <AlertCircle class="h-4 w-4" />
              Indisponível
            </span>

          </div>
        </div>

        <!-- Preço -->
        <div class="border-b-2 border-[#C9007A]/30 pb-4">

          <p class="text-sm font-semibold text-gray-600">
            Preço
          </p>

          <p class="mt-1 text-4xl font-bold text-[#C9007A]">
            {{ formattedPrice }}
          </p>

        </div>

        <!-- Descrição -->
        <div class="flex flex-col gap-3">

          <h2 class="text-xl font-bold text-[#C9007A]">
            Descrição do produto
          </h2>

          <div
            class="min-h-32 whitespace-pre-line break-words rounded-xl border-2 border-[#C9007A]/40 bg-white p-4 text-base leading-relaxed text-gray-800"
          >
            {{ product.description || 'Nenhuma descrição disponível para este produto.' }}
          </div>

        </div>

        <!-- Informações adicionais -->
        <div
          class="flex flex-col gap-3 rounded-xl border-2 border-[#C9007A]/40 bg-white p-4"
        >

          <h2 class="text-lg font-bold text-[#C9007A]">
            Informações do produto
          </h2>

          <div class="flex items-center justify-between gap-3">
            <span class="flex items-center gap-2 text-gray-600">
              <Boxes class="h-5 w-5 text-[#C9007A]" />
              Estoque
            </span>

            <span class="font-bold text-[#C9007A]">
              {{ product.stock }} unidades
            </span>
          </div>

          <div
            v-if="product.category"
            class="flex items-center justify-between gap-3"
          >
            <span class="flex items-center gap-2 text-gray-600">
              <Tag class="h-5 w-5 text-[#C9007A]" />
              Categoria
            </span>

            <span class="font-bold text-[#C9007A]">
              {{ product.category.name }}
            </span>
          </div>

        </div>

      </section>

      <!-- Painel de compra -->
      <aside
        class="flex h-fit min-w-0 flex-col gap-4 rounded-xl border-2 border-[#C9007A] bg-white p-5 shadow-md"
      >

        <p class="text-sm font-semibold text-gray-600">
          Valor do produto
        </p>

        <p class="text-3xl font-bold text-[#C9007A]">
          {{ formattedPrice }}
        </p>

        <div class="border-t border-gray-200 pt-3">

          <p
            v-if="available"
            class="flex items-center gap-2 font-bold text-green-700"
          >
            <CheckCircle2 class="h-5 w-5" />
            Em estoque
          </p>

          <p
            v-else
            class="flex items-center gap-2 font-bold text-red-600"
          >
            <AlertCircle class="h-5 w-5" />
            Produto indisponível
          </p>

          <p
            v-if="available"
            class="mt-1 text-sm text-gray-500"
          >
            {{ product.stock }} unidades disponíveis
          </p>

        </div>

        <!-- Quantidade -->
        <div class="flex flex-col gap-2">

          <label
            for="quantity"
            class="font-bold text-[#C9007A]"
          >
            Quantidade
          </label>

          <input
            id="quantity"
            v-model.number="quantity"
            type="number"
            min="1"
            :max="product.stock"
            :disabled="!available || adding"
            required
            class="h-11 w-full rounded-lg border-2 border-[#C9007A] bg-white px-3 text-base text-gray-800 outline-none focus:ring-2 focus:ring-[#C9007A]/30 disabled:bg-gray-100"
          />

        </div>

        <!-- Subtotal -->
        <div
          class="flex items-center justify-between border-t border-gray-200 pt-3"
        >
          <span class="font-semibold text-gray-600">
            Subtotal
          </span>

          <span class="text-lg font-bold text-[#C9007A]">
            {{ formatPrice(Number(product.price) * (quantity || 0)) }}
          </span>
        </div>

        <!-- Adicionar ao carrinho -->
        <button
          type="button"
          @click="add"
          :disabled="!available || adding || quantity < 1 || quantity > product.stock"
          class="flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-[#C9007A] px-4 py-3 font-bold text-white transition-colors hover:bg-[#A80065] disabled:cursor-not-allowed disabled:bg-gray-400"
        >
          <ShoppingCart class="h-5 w-5" />

          {{ adding ? 'Adicionando...' : 'Adicionar ao carrinho' }}
        </button>

        <p class="text-center text-xs text-gray-500">
          Produto vendido pela Enjoystick.
        </p>

        <!-- Feedback -->
        <div
          v-if="message"
          role="status"
          class="rounded-lg border border-green-500 bg-green-50 p-3 text-sm font-semibold text-green-700"
        >
          {{ message }}
        </div>

        <div
          v-if="error"
          role="alert"
          class="rounded-lg border border-red-500 bg-red-50 p-3 text-sm font-semibold text-red-600"
        >
          {{ error }}
        </div>

      </aside>

    </article>

  </main>
</template>