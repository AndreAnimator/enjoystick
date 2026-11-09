<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { createOrder } from '@/api/orders'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import type { Address } from '@/types/models'
import { listAddresses } from '@/api/addresses'
const router = useRouter()
const cart = useCartStore()
const auth = useAuthStore()
const addresses = ref<Address[]>([])
const selected = ref<number | ''>('')
const loading = ref(false)
const error = ref('')
const form = ref({
  shipping_recipient_name: auth.user?.name ?? '',
  shipping_postal_code: '',
  shipping_street: '',
  shipping_number: '',
  shipping_complement: '',
  shipping_neighborhood: '',
  shipping_city: '',
  shipping_state: '',
  shipping_country: 'Brasil',
})
const canSubmit = computed(() => cart.items.length > 0 && !cart.hasUnavailableItems)
function useAddress() {
  const a = addresses.value.find((item) => item.id === selected.value)
  if (a)
    Object.assign(form.value, {
      shipping_recipient_name: a.recipient_name,
      shipping_postal_code: a.postal_code,
      shipping_street: a.street,
      shipping_number: a.number,
      shipping_complement: a.complement ?? '',
      shipping_neighborhood: a.neighborhood,
      shipping_city: a.city,
      shipping_state: a.state,
      shipping_country: a.country,
    })
}
async function submit() {
  loading.value = true
  error.value = ''
  try {
    const order = await createOrder({
      ...form.value,
      shipping_complement: form.value.shipping_complement || null,
      user_id: auth.user!.id,
    })
    cart.clear()
    await router.push({ name: 'order-detail', params: { id: order.id } })
  } catch (e: unknown) {
    const err = e as {
      response?: { data?: { message?: string; errors?: Record<string, string[]> } }
    }
    error.value = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : (err.response?.data?.message ?? 'Não foi possível finalizar o pedido.')
  } finally {
    loading.value = false
  }
}
onMounted(async () => {
  await cart.fetchCart()
  try {
    addresses.value = await listAddresses()
  } catch {
    /* usuário pode informar um endereço novo */
  }
})
</script>
<template>
  <section>
    <h1>Finalizar compra</h1>
    <p v-if="!cart.items.length">
      Seu carrinho está vazio. <RouterLink to="/carrinho">Voltar ao carrinho</RouterLink>.
    </p>
    <template v-else>
      <p>
        Itens: {{ cart.itemCount }} — Produtos: R$ {{ Number(cart.total).toFixed(2) }} — Frete: R$
        10,00
      </p>
      <p>Total previsto: R$ {{ (Number(cart.total) + 10).toFixed(2) }}</p>
      <form @submit.prevent="submit">
        <fieldset v-if="addresses.length">
          <legend>Usar endereço cadastrado</legend>
          <select v-model.number="selected" @change="useAddress">
            <option value="">Novo endereço</option>
            <option v-for="a in addresses" :key="a.id" :value="a.id">
              {{ a.street }}, {{ a.number }} — {{ a.city }}
            </option>
          </select>
        </fieldset>
        <h2>Endereço de entrega</h2>
        <p>
          <label for="recipient">Destinatário</label><br /><input
            id="recipient"
            v-model="form.shipping_recipient_name"
            required
          />
        </p>
        <p>
          <label for="postal">CEP</label><br /><input
            id="postal"
            v-model="form.shipping_postal_code"
            required
          />
        </p>
        <p>
          <label for="street">Rua</label><br /><input
            id="street"
            v-model="form.shipping_street"
            required
          />
        </p>
        <p>
          <label for="number">Número</label><br /><input
            id="number"
            v-model="form.shipping_number"
            required
          />
        </p>
        <p>
          <label for="complement">Complemento</label><br /><input
            id="complement"
            v-model="form.shipping_complement"
          />
        </p>
        <p>
          <label for="neighborhood">Bairro</label><br /><input
            id="neighborhood"
            v-model="form.shipping_neighborhood"
            required
          />
        </p>
        <p>
          <label for="city">Cidade</label><br /><input
            id="city"
            v-model="form.shipping_city"
            required
          />
        </p>
        <p>
          <label for="state">Estado</label><br /><input
            id="state"
            v-model="form.shipping_state"
            required
          />
        </p>
        <p>
          <label for="country">País</label><br /><input
            id="country"
            v-model="form.shipping_country"
            required
          />
        </p>
        <p v-if="error" role="alert">{{ error }}</p>
        <button type="submit" :disabled="loading || !canSubmit">
          {{ loading ? 'Processando...' : 'Confirmar pedido' }}
        </button>
      </form></template
    >
  </section>
</template>
