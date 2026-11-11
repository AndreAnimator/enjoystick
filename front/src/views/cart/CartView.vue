<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useCartStore } from '@/stores/cart'
import {
  ShoppingCart,
  Trash2,
  Package,
  ArrowLeft,
  ArrowRight,
  AlertTriangle,
  ShoppingBag,
  Minus,
  Plus,
} from 'lucide-vue-next'

const cart = useCartStore()
const error = ref('')

async function change(id: number, quantity: number) {
  error.value = ''

  try {
    if (quantity < 1) {
      await cart.removeItem(id)
    } else {
      await cart.updateQuantity(id, quantity)
    }
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
      'Não foi possível atualizar o carrinho.'
  }
}

async function remove(id: number) {
  await change(id, 0)
}

function formatPrice(price: string | number) {
  return Number(price).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
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
  <main class="min-h-screen w-full bg-[#D9D9D9] font-HelveticaRounded">

    <!-- Cabeçalho -->
    <section
      class="flex min-h-16 w-full items-center gap-3 bg-[#C9007A] px-5 py-3 text-white"
    >
      <ShoppingCart class="h-7 w-7" />

      <h1 class="text-2xl font-bold">
        Meu carrinho
      </h1>

      <span class="ml-auto text-lg font-semibold">
        {{ cart.items.length }}
        {{ cart.items.length === 1 ? 'produto' : 'produtos' }}
      </span>
    </section>

    <div class="mx-auto w-full max-w-7xl px-4 py-6">

      <!-- Carregamento -->
      <div
        v-if="cart.loading"
        class="flex min-h-64 items-center justify-center"
      >
        <p class="text-xl font-bold text-[#C9007A]">
          Carregando carrinho...
        </p>
      </div>

      <template v-else>

        <!-- Erro -->
        <div
          v-if="error"
          role="alert"
          class="mb-5 flex items-center gap-3 rounded-lg border-2 border-red-500 bg-red-50 p-4 text-red-700"
        >
          <AlertTriangle class="h-6 w-6 shrink-0" />
          <p class="font-semibold">{{ error }}</p>
        </div>

        <!-- Carrinho vazio -->
        <section
          v-if="!cart.items.length"
          class="flex min-h-[400px] flex-col items-center justify-center gap-5 rounded-2xl border-2 border-[#C9007A] bg-white p-8 text-center shadow-md"
        >
          <ShoppingBag class="h-24 w-24 text-[#C9007A]" />

          <h2 class="text-3xl font-bold text-[#C9007A]">
            Seu carrinho está vazio!
          </h2>

          <p class="max-w-md text-gray-600">
            Parece que você ainda não escolheu seus produtos.
            Que tal dar uma olhada no nosso catálogo?
          </p>

          <RouterLink
            to="/"
            class="flex items-center gap-2 rounded-full bg-[#C9007A] px-7 py-3 font-bold text-white transition-colors hover:bg-[#A80065]"
          >
            <ArrowLeft class="h-5 w-5" />
            Ver produtos
          </RouterLink>
        </section>

        <!-- Carrinho com produtos -->
        <div
          v-else
          class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]"
        >

          <!-- Lista de produtos -->
          <section class="flex min-w-0 flex-col gap-4">

            <div
              class="rounded-xl border-2 border-[#C9007A] bg-white p-4 shadow-sm"
            >
              <h2 class="text-xl font-bold text-[#C9007A]">
                Produtos selecionados
              </h2>

              <p class="mt-1 text-sm text-gray-500">
                Confira os produtos e suas respectivas quantidades.
              </p>
            </div>

            <article
              v-for="item in cart.items"
              :key="item.id"
              class="grid grid-cols-1 gap-4 rounded-xl border-2 border-[#C9007A] bg-white p-4 shadow-sm sm:grid-cols-[100px_minmax(0,1fr)]"
            >

              <!-- Imagem / placeholder -->
              <div
                class="flex aspect-square items-center justify-center rounded-lg border border-[#C9007A]/30 bg-[#D9D9D9]"
              >
                <Package class="h-14 w-14 text-[#C9007A]" />
              </div>

              <!-- Informações -->
              <div class="flex min-w-0 flex-col gap-3">

                <div
                  class="flex flex-wrap items-start justify-between gap-2"
                >
                  <h3 class="min-w-0 flex-1 text-lg font-bold text-[#C9007A]">
                    {{ item.name }}
                  </h3>

                  <button
                    type="button"
                    @click="remove(item.id)"
                    aria-label="Remover produto"
                    class="flex items-center gap-1 rounded-lg px-2 py-1 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50"
                  >
                    <Trash2 class="h-4 w-4" />
                    Remover
                  </button>
                </div>

                <p class="text-sm text-gray-600">
                  Preço unitário:
                  <strong class="text-[#C9007A]">
                    {{ formatPrice(item.price) }}
                  </strong>
                </p>

                <!-- Indisponibilidade -->
                <div
                  v-if="!item.available"
                  class="flex items-center gap-2 rounded-lg border border-red-300 bg-red-50 p-3 text-sm font-semibold text-red-600"
                >
                  <AlertTriangle class="h-5 w-5 shrink-0" />

                  Produto indisponível ou sem estoque.
                  Remova-o para continuar.
                </div>

                <!-- Quantidade e subtotal -->
                <div
                  class="mt-auto flex flex-wrap items-end justify-between gap-4"
                >

                  <div class="flex flex-col gap-2">
                    <label
                      :for="`qty-${item.id}`"
                      class="text-sm font-semibold text-gray-600"
                    >
                      Quantidade
                    </label>

                    <div class="flex items-center gap-2">

                      <button
                        type="button"
                        :disabled="!item.available || item.quantity <= 1"
                        @click="change(item.id, item.quantity - 1)"
                        aria-label="Diminuir quantidade"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-[#C9007A] text-[#C9007A] hover:bg-pink-50 disabled:opacity-40"
                      >
                        <Minus class="h-4 w-4" />
                      </button>

                      <input
                        :id="`qty-${item.id}`"
                        type="number"
                        min="1"
                        :value="item.quantity"
                        :disabled="!item.available"
                        @change="change(item.id, Number(($event.target as HTMLInputElement).value))"
                        class="h-9 w-16 rounded-lg border-2 border-[#C9007A] text-center font-bold text-[#C9007A] outline-none focus:ring-2 focus:ring-[#C9007A]/30 disabled:bg-gray-100"
                      />

                      <button
                        type="button"
                        :disabled="!item.available"
                        @click="change(item.id, item.quantity + 1)"
                        aria-label="Aumentar quantidade"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-[#C9007A] text-[#C9007A] hover:bg-pink-50 disabled:opacity-40"
                      >
                        <Plus class="h-4 w-4" />
                      </button>

                    </div>
                  </div>

                  <div class="text-right">
                    <p class="text-sm text-gray-500">
                      Subtotal
                    </p>

                    <p class="text-xl font-bold text-[#C9007A]">
                      {{ formatPrice(item.subtotal) }}
                    </p>
                  </div>

                </div>
              </div>
            </article>

            <!-- Continuar comprando -->
            <RouterLink
              to="/"
              class="flex w-fit items-center gap-2 font-bold text-[#C9007A] transition-colors hover:text-[#A80065]"
            >
              <ArrowLeft class="h-5 w-5" />
              Continuar comprando
            </RouterLink>

          </section>

          <!-- Resumo da compra -->
          <aside
            class="flex h-fit flex-col gap-4 rounded-xl border-2 border-[#C9007A] bg-white p-5 shadow-md lg:sticky lg:top-5"
          >

            <h2 class="border-b-2 border-[#C9007A]/30 pb-3 text-xl font-bold text-[#C9007A]">
              Resumo do pedido
            </h2>

            <div class="flex items-center justify-between gap-3">
              <span class="text-gray-600">
                Quantidade de produtos
              </span>

              <strong class="text-[#C9007A]">
                {{ cart.items.length }}
              </strong>
            </div>

            <div class="flex items-center justify-between gap-3">
              <span class="text-gray-600">
                Total de itens
              </span>

              <strong class="text-[#C9007A]">
                {{ cart.items.reduce((total, item) => total + item.quantity, 0) }}
              </strong>
            </div>

            <div class="border-t border-gray-200 pt-4">

              <p class="text-sm font-semibold text-gray-500">
                Valor total
              </p>

              <p class="mt-1 text-3xl font-bold text-[#C9007A]">
                {{ formatPrice(cart.total) }}
              </p>

            </div>

            <!-- Aviso de indisponibilidade -->
            <div
              v-if="cart.hasUnavailableItems"
              class="flex gap-2 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-600"
            >
              <AlertTriangle class="h-5 w-5 shrink-0" />

              <p>
                Há produtos indisponíveis no carrinho.
                Remova-os para continuar com a compra.
              </p>
            </div>

            <!-- Finalizar -->
            <RouterLink
              v-else
              to="/checkout"
              class="flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-[#C9007A] px-4 py-3 text-center font-bold text-white transition-colors hover:bg-[#A80065]"
            >
              Continuar para entrega
              <ArrowRight class="h-5 w-5" />
            </RouterLink>

            <p class="text-center text-xs text-gray-500">
              O valor final poderá ser atualizado na etapa de entrega.
            </p>

          </aside>

        </div>
      </template>
    </div>
  </main>
</template>