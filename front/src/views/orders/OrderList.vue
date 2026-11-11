<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { listOrders } from '@/api/orders'
import type { Order } from '@/types/models'
import { Package, CalendarDays, ChevronRight, ShoppingBag, AlertCircle } from 'lucide-vue-next'

const orders = ref<Order[]>([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    orders.value = await listOrders()
  } catch {
    error.value = 'Não foi possível carregar seus pedidos.'
  } finally {
    loading.value = false
  }
})

function statusLabel(status: string) {
  return (
    {
      waiting: 'Aguardando pagamento',
      paid: 'Pago',
      sent: 'Enviado',
      arrived: 'Entregue',
      canceled: 'Cancelado',
    } as Record<string, string>
  )[status] ?? status
}

function statusClass(status: string) {
  return (
    {
      waiting: 'bg-yellow-100 text-yellow-700',
      paid: 'bg-blue-100 text-blue-700',
      sent: 'bg-purple-100 text-purple-700',
      arrived: 'bg-green-100 text-green-700',
      canceled: 'bg-red-100 text-red-700',
    } as Record<string, string>
  )[status] ?? 'bg-gray-100 text-gray-700'
}

function formatPrice(value: string | number) {
  return Number(value).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
}

function formatDate(value: string) {
  return new Date(value).toLocaleDateString('pt-BR')
}
</script>

<template>
  <main class="min-h-screen bg-[#D9D9D9] px-4 py-8 font-HelveticaRounded sm:px-6">
    <div class="mx-auto max-w-5xl">
      <header class="mb-8">
        <div class="mb-2 flex items-center gap-3">
          <div class="rounded-xl bg-[#C9007A] p-3 text-white">
            <Package :size="26" />
          </div>
          <div>
            <h1 class="text-2xl font-bold text-[#C9007A] sm:text-3xl">
              Meus pedidos
            </h1>
            <p class="text-sm text-gray-600">
              Acompanhe suas compras e consulte seus pedidos.
            </p>
          </div>
        </div>
      </header>

      <div
        v-if="loading"
        class="rounded-2xl bg-white p-10 text-center shadow-sm"
      >
        <div class="mx-auto mb-3 h-9 w-9 animate-spin rounded-full border-4 border-[#f1abd5] border-t-[#C9007A]"></div>
        <p class="font-semibold text-gray-600">Carregando seus pedidos...</p>
      </div>

      <div
        v-else-if="error"
        role="alert"
        class="flex items-center gap-3 rounded-2xl border border-red-200 bg-white p-5 text-red-600 shadow-sm"
      >
        <AlertCircle :size="22" />
        <p>{{ error }}</p>
      </div>

      <div
        v-else-if="!orders.length"
        class="rounded-2xl bg-white px-6 py-14 text-center shadow-sm"
      >
        <ShoppingBag :size="54" class="mx-auto mb-4 text-[#f1abd5]" />
        <h2 class="mb-2 text-xl font-bold text-gray-800">
          Você ainda não fez pedidos
        </h2>
        <p class="mb-6 text-gray-500">
          Que tal encontrar algo especial para você?
        </p>
        <RouterLink
          to="/"
          class="inline-flex items-center justify-center rounded-xl bg-[#C9007A] px-6 py-3 font-bold text-white transition hover:bg-[#A80065]"
        >
          Explorar produtos
        </RouterLink>
      </div>

      <div v-else class="space-y-4">
        <div class="mb-5 flex items-center justify-between">
          <p class="font-semibold text-gray-700">
            {{ orders.length }} {{ orders.length === 1 ? 'pedido encontrado' : 'pedidos encontrados' }}
          </p>
        </div>

        <article
          v-for="order in orders"
          :key="order.id"
          class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm transition hover:shadow-md sm:p-6"
        >
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
              <div class="rounded-xl bg-[#fce4f1] p-3 text-[#C9007A]">
                <Package :size="25" />
              </div>

              <div>
                <h2 class="text-lg font-bold text-gray-800">
                  Pedido #{{ order.id }}
                </h2>

                <p class="mt-1 flex items-center gap-2 text-sm text-gray-500">
                  <CalendarDays :size="15" />
                  {{ formatDate(order.created_at) }}
                </p>
              </div>
            </div>

            <span
              class="w-fit rounded-full px-4 py-2 text-xs font-bold"
              :class="statusClass(order.status)"
            >
              {{ statusLabel(order.status) }}
            </span>
          </div>

          <div class="my-5 border-t border-gray-100"></div>

          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-500">Valor total</p>
              <p class="text-xl font-bold text-[#C9007A]">
                {{ formatPrice(order.total) }}
              </p>
            </div>

            <RouterLink
              :to="{ name: 'order-detail', params: { id: order.id } }"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-5 py-3 font-bold text-white transition hover:bg-[#A80065]"
            >
              Ver detalhes
              <ChevronRight :size="18" />
            </RouterLink>
          </div>
        </article>
      </div>
    </div>
  </main>
</template>