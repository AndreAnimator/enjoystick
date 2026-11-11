<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { cancelOrder, getOrder, payOrder } from '@/api/orders'
import type { Order } from '@/types/models'
import {
  Package,
  CalendarDays,
  MapPin,
  CreditCard,
  Truck,
  CheckCircle,
  AlertCircle,
  ArrowLeft,
  ShoppingBag,
  XCircle,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()

const order = ref<Order | null>(null)
const loading = ref(true)
const error = ref('')
const busy = ref(false)

const labels: Record<string, string> = {
  waiting: 'Aguardando pagamento',
  paid: 'Pago',
  sent: 'Enviado',
  arrived: 'Entregue',
  canceled: 'Cancelado',
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
  return new Date(value).toLocaleString('pt-BR')
}

async function reload() {
  order.value = await getOrder(Number(route.params.id))
}

async function pay() {
  if (!order.value) return

  busy.value = true
  error.value = ''

  try {
    await payOrder(order.value.id)
    await reload()
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }
    error.value = err.response?.data?.message ?? 'Falha ao pagar pedido.'
  } finally {
    busy.value = false
  }
}

async function cancel() {
  if (!order.value || !window.confirm('Deseja cancelar este pedido?')) return

  busy.value = true
  error.value = ''

  try {
    order.value = await cancelOrder(order.value.id)
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }
    error.value = err.response?.data?.message ?? 'Falha ao cancelar pedido.'
  } finally {
    busy.value = false
  }
}

onMounted(async () => {
  try {
    await reload()
  } catch {
    error.value = 'Pedido não encontrado ou sem permissão para visualizá-lo.'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <main class="min-h-screen bg-[#D9D9D9] px-4 py-8 font-HelveticaRounded sm:px-6">
    <div class="mx-auto max-w-5xl">
      <RouterLink
        to="/meus-pedidos"
        class="mb-6 inline-flex items-center gap-2 font-semibold text-[#C9007A] transition hover:text-[#A80065]"
      >
        <ArrowLeft :size="19" />
        Voltar aos pedidos
      </RouterLink>

      <header class="mb-8 flex items-center gap-3">
        <div class="rounded-xl bg-[#C9007A] p-3 text-white">
          <ShoppingBag :size="26" />
        </div>
        <div>
          <h1 class="text-2xl font-bold text-[#C9007A] sm:text-3xl">
            Detalhes do pedido
          </h1>
          <p class="text-sm text-gray-600">
            Consulte os itens, valores e informações de entrega.
          </p>
        </div>
      </header>

      <div
        v-if="loading"
        class="rounded-2xl bg-white p-10 text-center shadow-sm"
      >
        <div class="mx-auto mb-3 h-9 w-9 animate-spin rounded-full border-4 border-[#f1abd5] border-t-[#C9007A]"></div>
        <p class="font-semibold text-gray-600">Carregando pedido...</p>
      </div>

      <div
        v-else-if="error && !order"
        role="alert"
        class="flex items-center gap-3 rounded-2xl bg-white p-6 text-red-600 shadow-sm"
      >
        <AlertCircle :size="22" />
        {{ error }}
      </div>

      <template v-if="order">
        <div
          v-if="error"
          role="alert"
          class="mb-5 flex items-center gap-3 rounded-xl border border-red-200 bg-white p-4 text-sm text-red-600"
        >
          <AlertCircle :size="20" />
          {{ error }}
        </div>

        <section class="mb-6 rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-6">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p class="mb-1 text-sm text-gray-500">Número do pedido</p>
              <h2 class="text-2xl font-bold text-gray-800">
                #{{ order.id }}
              </h2>
              <p class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                <CalendarDays :size="16" />
                {{ formatDate(order.created_at) }}
              </p>
            </div>

            <span
              class="w-fit rounded-full px-4 py-2 text-sm font-bold"
              :class="statusClass(order.status)"
            >
              {{ labels[order.status] ?? order.status }}
            </span>
          </div>

          <div v-if="order.status === 'waiting'" class="mt-6 flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row">
            <button
              type="button"
              :disabled="busy"
              @click="pay"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-6 py-3 font-bold text-white transition hover:bg-[#A80065] disabled:cursor-not-allowed disabled:opacity-50"
            >
              <CreditCard :size="19" />
              {{ busy ? 'Processando...' : 'Pagar pedido (simulado)' }}
            </button>

            <button
              type="button"
              :disabled="busy"
              @click="cancel"
              class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-300 px-6 py-3 font-bold text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
            >
              <XCircle :size="19" />
              Cancelar pedido
            </button>
          </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div class="space-y-6 lg:col-span-2">
            <section class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-6">
              <div class="mb-5 flex items-center gap-3">
                <div class="rounded-lg bg-[#fce4f1] p-2 text-[#C9007A]">
                  <Package :size="22" />
                </div>
                <h3 class="text-lg font-bold text-gray-800">
                  Itens do pedido
                </h3>
              </div>

              <div
                v-for="(item, index) in order.items"
                :key="`${item.product_id}-${index}`"
                class="flex flex-col gap-2 border-b border-gray-100 py-4 last:border-0 sm:flex-row sm:items-center sm:justify-between"
              >
                <div>
                  <p class="font-bold text-gray-800">
                    {{ item.product_name }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    {{ item.quantity }} unidade(s) × {{ formatPrice(item.unit_price) }}
                  </p>
                </div>

                <p class="font-bold text-[#C9007A]">
                  {{ formatPrice(Number(item.unit_price) * item.quantity) }}
                </p>
              </div>

              <div class="mt-4 space-y-3 border-t border-gray-100 pt-5">
                <div class="flex justify-between text-sm text-gray-600">
                  <span>Subtotal</span>
                  <span>{{ formatPrice(order.subtotal) }}</span>
                </div>

                <div class="flex justify-between text-sm text-gray-600">
                  <span>Frete</span>
                  <span>{{ formatPrice(order.shipping_cost) }}</span>
                </div>

                <div class="flex justify-between border-t border-gray-100 pt-4 text-lg font-bold">
                  <span class="text-gray-800">Total</span>
                  <span class="text-[#C9007A]">
                    {{ formatPrice(order.total) }}
                  </span>
                </div>
              </div>
            </section>
          </div>

          <div class="space-y-6">
            <section class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-6">
              <div class="mb-5 flex items-center gap-3">
                <div class="rounded-lg bg-[#fce4f1] p-2 text-[#C9007A]">
                  <Truck :size="22" />
                </div>
                <h3 class="text-lg font-bold text-gray-800">
                  Entrega
                </h3>
              </div>

              <div class="space-y-3 text-sm text-gray-600">
                <p class="font-bold text-gray-800">
                  {{ order.shipping_recipient_name }}
                </p>

                <div class="flex items-start gap-2">
                  <MapPin :size="17" class="mt-0.5 shrink-0 text-[#C9007A]" />
                  <address class="not-italic leading-relaxed">
                    {{ order.shipping_street }}, {{ order.shipping_number }}
                    <template v-if="order.shipping_complement">
                      <br />{{ order.shipping_complement }}
                    </template>
                    <br />{{ order.shipping_neighborhood }}
                    <br />{{ order.shipping_city }} - {{ order.shipping_state }}
                    <br />{{ order.shipping_postal_code }}
                    <br />{{ order.shipping_country }}
                  </address>
                </div>
              </div>
            </section>

            <section
              v-if="order.status === 'arrived'"
              class="rounded-2xl border border-green-200 bg-green-50 p-5"
            >
              <div class="flex items-center gap-3 text-green-700">
                <CheckCircle :size="24" />
                <div>
                  <h3 class="font-bold">Pedido entregue!</h3>
                  <p class="text-sm">Sua compra foi concluída.</p>
                </div>
              </div>
            </section>

            <section
              v-else-if="order.status === 'canceled'"
              class="rounded-2xl border border-red-200 bg-red-50 p-5"
            >
              <div class="flex items-center gap-3 text-red-700">
                <XCircle :size="24" />
                <div>
                  <h3 class="font-bold">Pedido cancelado</h3>
                  <p class="text-sm">Este pedido não está mais ativo.</p>
                </div>
              </div>
            </section>
          </div>
        </div>
      </template>
    </div>
  </main>
</template>