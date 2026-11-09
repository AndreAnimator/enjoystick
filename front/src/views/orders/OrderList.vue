<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { listOrders } from '@/api/orders'
import type { Order } from '@/types/models'
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
    (
      {
        waiting: 'Aguardando pagamento',
        paid: 'Pago',
        sent: 'Enviado',
        arrived: 'Entregue',
        canceled: 'Cancelado',
      } as Record<string, string>
    )[status] ?? status
  )
}
</script>
<template>
  <section>
    <h1>Meus pedidos</h1>
    <p v-if="loading">Carregando...</p>
    <p v-if="error" role="alert">{{ error }}</p>
    <p v-if="!loading && !orders.length">Você ainda não fez pedidos.</p>
    <ul>
      <li v-for="order in orders" :key="order.id">
        <RouterLink :to="{ name: 'order-detail', params: { id: order.id } }"
          >Pedido #{{ order.id }}</RouterLink
        >
        — {{ statusLabel(order.status) }} — R$ {{ Number(order.total).toFixed(2) }} —
        {{ new Date(order.created_at).toLocaleDateString('pt-BR') }}
      </li>
    </ul>
  </section>
</template>
