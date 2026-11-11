<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { cancelOrder, getOrder, payOrder } from '@/api/orders'
import type { Order } from '@/types/models'
import {
  Clock,
  Package,
  MapPin,
  CreditCard,
  XCircle,
  CheckCircle,
  ArrowLeft,
  AlertCircle,
  Truck,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()

const order = ref<Order | null>(null)
const loading = ref(true)
const error = ref('')
const busy = ref(false)

const remainingSeconds = ref(120)
let timer: ReturnType<typeof setInterval> | null = null

const labels: Record<string, string> = {
  waiting: 'Aguardando pagamento',
  paid: 'Pago',
  sent: 'Enviado',
  arrived: 'Entregue',
  canceled: 'Cancelado',
}

const formattedTime = computed(() => {
  const minutes = Math.floor(remainingSeconds.value / 60)
  const seconds = remainingSeconds.value % 60

  return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
})

const timerProgress = computed(() => {
  return (remainingSeconds.value / 120) * 100
})

function stopTimer() {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

function startTimer() {
  stopTimer()

  if (!order.value || order.value.status !== 'waiting') return

  remainingSeconds.value = 120

  timer = setInterval(async () => {
    if (remainingSeconds.value > 0) {
      remainingSeconds.value--
    }

    if (remainingSeconds.value <= 0) {
      stopTimer()
      await expireOrder()
    }
  }, 1000)
}

async function expireOrder() {
  if (!order.value || order.value.status !== 'waiting' || busy.value) return

  busy.value = true

  try {
    order.value = await cancelOrder(order.value.id)
    error.value = 'O tempo para pagamento expirou. Seu pedido foi cancelado.'
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }
    error.value =
      err.response?.data?.message ??
      'O tempo de pagamento expirou, mas não foi possível cancelar o pedido.'
  } finally {
    busy.value = false
  }
}

async function reload() {
  order.value = await getOrder(Number(route.params.id))

  if (order.value.status === 'waiting') {
    startTimer()
  } else {
    stopTimer()
  }
}

async function pay() {
  if (!order.value || busy.value) return

  busy.value = true
  error.value = ''
  stopTimer()

  try {
    await payOrder(order.value.id)
    await reload()
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }

    error.value = err.response?.data?.message ?? 'Falha ao pagar pedido.'

    // Retoma a contagem caso o pagamento não tenha sido confirmado.
    if (order.value?.status === 'waiting') {
      startTimer()
    }
  } finally {
    busy.value = false
  }
}

async function cancel() {
  if (
    !order.value ||
    busy.value ||
    !window.confirm('Deseja cancelar este pedido?')
  ) return

  busy.value = true
  error.value = ''
  stopTimer()

  try {
    order.value = await cancelOrder(order.value.id)
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }

    error.value = err.response?.data?.message ?? 'Falha ao cancelar pedido.'

    if (order.value?.status === 'waiting') {
      startTimer()
    }
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

onUnmounted(() => {
  stopTimer()
})
</script>

<template>
  <main class="min-h-screen bg-[#D9D9D9] px-4 py-8 font-HelveticaRounded sm:px-6">
    <div class="mx-auto max-w-5xl">

      <!-- Cabeçalho -->
      <header class="mb-7 flex items-center gap-3">
        <div class="rounded-xl bg-[#C9007A] p-3 text-white">
          <Package :size="26" />
        </div>

        <div>
          <h1 class="text-2xl font-bold text-[#C9007A] sm:text-3xl">
            Detalhes do pedido
          </h1>
          <p class="text-sm text-gray-600">
            Acompanhe as informações e o andamento da sua compra.
          </p>
        </div>
      </header>

      <!-- Carregamento -->
      <div
        v-if="loading"
        class="rounded-2xl bg-white p-10 text-center text-gray-500 shadow-sm"
      >
        Carregando pedido...
      </div>

      <template v-else-if="order">

        <!-- Mensagem de erro -->
        <div
          v-if="error"
          role="alert"
          class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-white p-4 text-sm text-red-700 shadow-sm"
        >
          <AlertCircle :size="20" class="shrink-0" />
          {{ error }}
        </div>

        <!-- Resumo -->
        <section class="mb-6 rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-7">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p class="text-sm font-semibold text-gray-500">
                Número do pedido
              </p>
              <h2 class="text-2xl font-extrabold text-[#C9007A]">
                #{{ order.id }}
              </h2>
              <p class="mt-1 text-sm text-gray-500">
                {{ new Date(order.created_at).toLocaleString('pt-BR') }}
              </p>
            </div>

            <span
              class="w-fit rounded-full px-4 py-2 text-sm font-bold"
              :class="{
                'bg-yellow-100 text-yellow-700': order.status === 'waiting',
                'bg-green-100 text-green-700': order.status === 'paid',
                'bg-blue-100 text-blue-700': order.status === 'sent',
                'bg-emerald-100 text-emerald-700': order.status === 'arrived',
                'bg-red-100 text-red-700': order.status === 'canceled',
              }"
            >
              {{ labels[order.status] ?? order.status }}
            </span>
          </div>

          <!-- Timer -->
          <div
            v-if="order.status === 'waiting'"
            class="mt-7 rounded-xl border border-pink-200 bg-[#fce4f1] p-5"
          >
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
              <div class="flex items-center gap-3">
                <div class="rounded-xl bg-white p-3 text-[#C9007A]">
                  <Clock :size="25" />
                </div>

                <div>
                  <h3 class="font-bold text-[#A80065]">
                    Tempo para pagamento
                  </h3>
                  <p class="text-sm text-gray-600">
                    Seu pedido será cancelado quando o tempo acabar.
                  </p>
                </div>
              </div>

              <p class="text-4xl font-extrabold tabular-nums text-[#C9007A]">
                {{ formattedTime }}
              </p>
            </div>

            <div class="mt-4 h-2 overflow-hidden rounded-full bg-white">
              <div
                class="h-full rounded-full bg-[#C9007A] transition-all duration-1000"
                :style="{ width: `${timerProgress}%` }"
              ></div>
            </div>
          </div>

          <!-- Pedido pago -->
          <div
            v-else-if="order.status === 'paid'"
            class="mt-6 flex items-center gap-3 rounded-xl bg-green-50 p-4 text-green-700"
          >
            <CheckCircle :size="24" />
            <p class="font-bold">
              Pagamento confirmado! Obrigado pela compra.
            </p>
          </div>

          <!-- Pedido cancelado -->
          <div
            v-else-if="order.status === 'canceled'"
            class="mt-6 flex items-center gap-3 rounded-xl bg-red-50 p-4 text-red-700"
          >
            <XCircle :size="24" />
            <p class="font-bold">
              Este pedido foi cancelado.
            </p>
          </div>
        </section>

        <!-- Itens -->
        <section class="mb-6 rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-7">
          <div class="mb-5 flex items-center gap-3">
            <Package :size="22" class="text-[#C9007A]" />
            <h2 class="text-xl font-bold text-gray-800">
              Itens do pedido
            </h2>
          </div>

          <div class="divide-y divide-gray-100">
            <div
              v-for="(item, index) in order.items"
              :key="`${item.product_id}-${index}`"
              class="flex flex-col justify-between gap-2 py-4 sm:flex-row sm:items-center"
            >
              <div>
                <h3 class="font-bold text-gray-800">
                  {{ item.product_name }}
                </h3>
                <p class="text-sm text-gray-500">
                  {{ item.quantity }} × R$
                  {{ Number(item.unit_price).toFixed(2) }}
                </p>
              </div>

              <p class="font-bold text-[#C9007A]">
                R$ {{ (Number(item.unit_price) * item.quantity).toFixed(2) }}
              </p>
            </div>
          </div>
        </section>

        <!-- Entrega e valores -->
        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">

          <section class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="mb-5 flex items-center gap-3">
              <MapPin :size="22" class="text-[#C9007A]" />
              <h2 class="text-xl font-bold text-gray-800">
                Endereço de entrega
              </h2>
            </div>

            <address class="not-italic text-sm leading-7 text-gray-600">
              <strong class="text-gray-800">
                {{ order.shipping_recipient_name }}
              </strong>
              <br />
              {{ order.shipping_street }}, {{ order.shipping_number }}
              <br />
              <template v-if="order.shipping_complement">
                {{ order.shipping_complement }}<br />
              </template>
              {{ order.shipping_neighborhood }}
              <br />
              {{ order.shipping_city }} - {{ order.shipping_state }}
              <br />
              CEP: {{ order.shipping_postal_code }}
              <br />
              {{ order.shipping_country }}
            </address>
          </section>

          <section class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="mb-5 flex items-center gap-3">
              <CreditCard :size="22" class="text-[#C9007A]" />
              <h2 class="text-xl font-bold text-gray-800">
                Resumo financeiro
              </h2>
            </div>

            <div class="space-y-4 text-sm">
              <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span>R$ {{ Number(order.subtotal).toFixed(2) }}</span>
              </div>

              <div class="flex justify-between text-gray-600">
                <span>Frete</span>
                <span>R$ {{ Number(order.shipping_cost).toFixed(2) }}</span>
              </div>

              <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between text-lg font-extrabold text-[#C9007A]">
                  <span>Total</span>
                  <span>R$ {{ Number(order.total).toFixed(2) }}</span>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Ações -->
        <section
          v-if="order.status === 'waiting'"
          class="mb-6 rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-7"
        >
          <h2 class="mb-2 text-lg font-bold text-gray-800">
            Ações do pedido
          </h2>
          <p class="mb-5 text-sm text-gray-500">
            Finalize o pagamento antes que o tempo termine.
          </p>

          <div class="flex flex-col gap-3 sm:flex-row">
            <button
              type="button"
              @click="pay"
              :disabled="busy || remainingSeconds <= 0"
              class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-6 py-3 font-bold text-white transition hover:bg-[#A80065] disabled:cursor-not-allowed disabled:opacity-50"
            >
              <CreditCard :size="19" />
              {{ busy ? 'Processando...' : 'Pagar pedido (simulado)' }}
            </button>

            <button
              type="button"
              @click="cancel"
              :disabled="busy"
              class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 px-6 py-3 font-bold text-red-600 transition hover:bg-red-50 disabled:opacity-50"
            >
              <XCircle :size="19" />
              Cancelar pedido
            </button>
          </div>
        </section>

        <div class="flex justify-start">
          <button
            type="button"
            @click="router.push('/meus-pedidos')"
            class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 font-bold text-[#C9007A] shadow-sm transition hover:bg-[#fce4f1]"
          >
            <ArrowLeft :size="18" />
            Voltar aos pedidos
          </button>
        </div>
      </template>
    </div>
  </main>
</template>