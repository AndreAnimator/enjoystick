<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { listAllOrders, updateOrderStatus } from '@/api/orders'
import type { Order, OrderStatus } from '@/types/models'
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
async function load() {
  loading.value = true
  try {
    orders.value = await listAllOrders(status.value || undefined)
  } catch {
    error.value = 'Não foi possível carregar os pedidos.'
  } finally {
    loading.value = false
  }
}
async function change(order: Order, value: OrderStatus) {
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
  <section>
    <h2>Pedidos</h2>
    <label for="status-filter">Filtrar por status</label>
    <select id="status-filter" v-model="status" @change="load">
      <option value="">Todos</option>
      <option v-for="(label, value) in labels" :key="value" :value="value">{{ label }}</option>
    </select>
    <p v-if="loading">Carregando...</p>
    <p v-if="error" role="alert">{{ error }}</p>
    <table>
      <thead>
        <tr>
          <th>Pedido</th>
          <th>Cliente</th>
          <th>Total</th>
          <th>Status</th>
          <th>Alterar status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td>#{{ order.id }}</td>
          <td>{{ order.shipping_recipient_name }}</td>
          <td>R$ {{ Number(order.total).toFixed(2) }}</td>
          <td>{{ labels[order.status] }}</td>
          <td>
            <select
              :value="order.status"
              @change="change(order, ($event.target as HTMLSelectElement).value as OrderStatus)"
            >
              <option value="waiting" :disabled="order.status !== 'waiting'">
                Aguardando pagamento
              </option>
              <option value="paid" :disabled="order.status !== 'waiting'">Pago</option>
              <option value="sent" :disabled="order.status !== 'paid'">Enviado</option>
              <option value="arrived" :disabled="order.status !== 'sent'">Entregue</option>
              <option value="canceled" :disabled="order.status !== 'waiting'">Cancelado</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="!loading && !orders.length">Nenhum pedido encontrado.</p>
  </section>
</template>
