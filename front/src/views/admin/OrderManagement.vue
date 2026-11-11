<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { listAllOrders, updateOrderStatus } from '@/api/orders'
import type { Order, OrderStatus } from '@/types/models'
import { ShoppingBag, AlertCircle, CalendarDays, RefreshCw } from 'lucide-vue-next'

const orders = ref<Order[]>([])
const status = ref<OrderStatus | ''>('')
const error = ref('')
const loading = ref(false)

const labels: Record<OrderStatus, string> = {
  waiting: 'Aguardando pagamento',
  paid: 'Pago',
  sent: 'Enviado',
  arrived: 'Entregue',
  canceled: 'Cancelado',
}

function statusClass(value: OrderStatus) {
  return {
    waiting: 'bg-yellow-100 text-yellow-700',
    paid: 'bg-blue-100 text-blue-700',
    sent: 'bg-purple-100 text-purple-700',
    arrived: 'bg-green-100 text-green-700',
    canceled: 'bg-red-100 text-red-700',
  }[value]
}

function formatPrice(value: string | number) {
  return Number(value).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
}

function canChangeTo(current: OrderStatus, next: OrderStatus) {
  const transitions: Record<OrderStatus, OrderStatus[]> = {
    waiting: ['paid', 'canceled'],
    paid: ['sent'],
    sent: ['arrived'],
    arrived: [],
    canceled: [],
  }

  return current === next || transitions[current].includes(next)
}

async function load() {
  loading.value = true
  error.value = ''

  try {
    orders.value = await listAllOrders(status.value || undefined)
  } catch {
    error.value = 'Não foi possível carregar os pedidos.'
  } finally {
    loading.value = false
  }
}

async function change(order: Order, value: OrderStatus) {
  if (!canChangeTo(order.status, value) || order.status === value) return

  error.value = ''

  try {
    await updateOrderStatus(order.id, value)
    await load()
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }
    error.value = err.response?.data?.message ?? 'Transição de status não permitida.'
  }
}

onMounted(load)
</script>

<template>
  <section class="font-HelveticaRounded">
    <header class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-3">
        <div class="rounded-xl bg-[#fce4f1] p-3 text-[#C9007A]">
          <ShoppingBag :size="25" />
        </div>
        <div>
          <h2 class="text-2xl font-bold text-[#C9007A]">Pedidos</h2>
          <p class="text-sm text-gray-500">Acompanhe e atualize os pedidos dos clientes.</p>
        </div>
      </div>

      <button
        type="button"
        @click="load"
        :disabled="loading"
        class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#C9007A] px-4 py-2.5 font-bold text-[#C9007A] transition hover:bg-[#fce4f1] disabled:opacity-50"
      >
        <RefreshCw :size="17" :class="{ 'animate-spin': loading }" />
        Atualizar
      </button>
    </header>

    <div class="mb-6 rounded-2xl border border-pink-100 bg-[#fff9fc] p-5">
      <label for="status-filter" class="mb-2 block text-sm font-bold text-gray-700">
        Filtrar pedidos por status
      </label>

      <select
        id="status-filter"
        v-model="status"
        @change="load"
        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-700 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100 sm:max-w-sm"
      >
        <option value="">Todos os pedidos</option>
        <option v-for="(label, value) in labels" :key="value" :value="value">
          {{ label }}
        </option>
      </select>
    </div>

    <div
      v-if="error"
      role="alert"
      class="mb-5 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
    >
      <AlertCircle :size="19" />
      {{ error }}
    </div>

    <div v-if="loading" class="rounded-xl bg-gray-50 p-10 text-center text-gray-500">
      <div class="mx-auto mb-3 h-8 w-8 animate-spin rounded-full border-4 border-[#f1abd5] border-t-[#C9007A]"></div>
      Carregando pedidos...
    </div>

    <div v-else-if="orders.length" class="overflow-x-auto rounded-xl border border-gray-100">
      <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="bg-[#fce4f1] text-[#A80065]">
          <tr>
            <th class="px-5 py-4 font-bold">Pedido</th>
            <th class="px-5 py-4 font-bold">Cliente</th>
            <th class="px-5 py-4 font-bold">Total</th>
            <th class="px-5 py-4 font-bold">Status</th>
            <th class="px-5 py-4 font-bold">Alterar status</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 bg-white">
          <tr
            v-for="order in orders"
            :key="order.id"
            class="transition hover:bg-[#fff9fc]"
          >
            <td class="px-5 py-4 font-bold text-gray-800">
              #{{ order.id }}
            </td>

            <td class="px-5 py-4 text-gray-700">
              {{ order.shipping_recipient_name }}
            </td>

            <td class="px-5 py-4 font-bold text-[#C9007A]">
              {{ formatPrice(order.total) }}
            </td>

            <td class="px-5 py-4">
              <span
                class="whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-bold"
                :class="statusClass(order.status)"
              >
                {{ labels[order.status] }}
              </span>
            </td>

            <td class="px-5 py-4">
              <select
                :value="order.status"
                @change="change(order, ($event.target as HTMLSelectElement).value as OrderStatus)"
                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-700 outline-none focus:border-[#C9007A]"
              >
                <option
                  v-for="(label, value) in labels"
                  :key="value"
                  :value="value"
                  :disabled="!canChangeTo(order.status, value)"
                >
                  {{ label }}
                </option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <p
      v-else
      class="rounded-xl bg-gray-50 p-10 text-center text-gray-500"
    >
      Nenhum pedido encontrado.
    </p>
  </section>
</template>