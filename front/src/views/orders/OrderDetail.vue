<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { cancelOrder, getOrder, payOrder } from '@/api/orders'
import type { Order } from '@/types/models'
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
  <section>
    <h1>Detalhes do pedido</h1>
    <p v-if="loading">Carregando...</p>
    <p v-if="error" role="alert">{{ error }}</p>
    <article v-if="order">
      <h2>Pedido #{{ order.id }}</h2>
      <p>Status: {{ labels[order.status] }}</p>
      <p>Data: {{ new Date(order.created_at).toLocaleString('pt-BR') }}</p>
      <h3>Itens</h3>
      <ul>
        <li v-for="(item, index) in order.items" :key="`${item.product_id}-${index}`">
          {{ item.product_name }} — {{ item.quantity }} × R$
          {{ Number(item.unit_price).toFixed(2) }}
        </li>
      </ul>
      <p>Subtotal: R$ {{ Number(order.subtotal).toFixed(2) }}</p>
      <p>Frete: R$ {{ Number(order.shipping_cost).toFixed(2) }}</p>
      <p>
        <strong>Total: R$ {{ Number(order.total).toFixed(2) }}</strong>
      </p>
      <h3>Entrega</h3>
      <address>
        {{ order.shipping_recipient_name }}<br />{{ order.shipping_street }},
        {{ order.shipping_number }}<br />{{ order.shipping_complement }}<br />{{
          order.shipping_neighborhood
        }}<br />{{ order.shipping_city }} - {{ order.shipping_state }}
        {{ order.shipping_postal_code }}<br />{{ order.shipping_country }}
      </address>
      <p v-if="order.status === 'waiting'">
        <button type="button" :disabled="busy" @click="pay">Pagar pedido (simulado)</button>
        <button type="button" :disabled="busy" @click="cancel">Cancelar pedido</button>
      </p>
    </article>
    <p><RouterLink to="/meus-pedidos">Voltar aos pedidos</RouterLink></p>
  </section>
</template>
