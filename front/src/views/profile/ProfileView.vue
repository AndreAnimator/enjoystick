<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import {
  createAddress,
  deleteAddress,
  listAddresses,
  updateAddress,
  type AddressPayload,
} from '@/api/addresses'
import type { Address } from '@/types/models'
const auth = useAuthStore()
const addresses = ref<Address[]>([])
const profile = ref({ name: auth.user?.name ?? '', email: auth.user?.email ?? '' })
const editing = ref<number | null>(null)
const error = ref('')
const message = ref('')
const saving = ref(false)
const emptyAddress = (): AddressPayload => ({
  recipient_name: auth.user?.name ?? '',
  postal_code: '',
  street: '',
  number: '',
  complement: '',
  neighborhood: '',
  city: '',
  state: '',
  country: 'Brasil',
})
const address = ref<AddressPayload>(emptyAddress())
async function load() {
  addresses.value = await listAddresses()
}
async function saveProfile() {
  saving.value = true
  error.value = ''
  message.value = ''
  try {
    const payload: { name?: string; email?: string } = {}
    if (profile.value.name !== auth.user?.name) payload.name = profile.value.name
    if (profile.value.email !== auth.user?.email) payload.email = profile.value.email
    if (Object.keys(payload).length) await auth.update(payload)
    message.value = 'Perfil atualizado.'
  } catch (e: unknown) {
    const err = e as {
      response?: { data?: { message?: string; errors?: Record<string, string[]> } }
    }
    error.value = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : (err.response?.data?.message ?? 'Não foi possível atualizar o perfil.')
  } finally {
    saving.value = false
  }
}
function edit(a: Address) {
  editing.value = a.id
  address.value = {
    recipient_name: a.recipient_name,
    postal_code: a.postal_code,
    street: a.street,
    number: a.number,
    complement: a.complement ?? '',
    neighborhood: a.neighborhood,
    city: a.city,
    state: a.state,
    country: a.country,
  }
}
function resetAddress() {
  editing.value = null
  address.value = emptyAddress()
}
async function saveAddress() {
  error.value = ''
  try {
    if (editing.value) await updateAddress(editing.value, address.value)
    else await createAddress(address.value)
    await load()
    resetAddress()
    message.value = 'Endereço salvo.'
  } catch (e: unknown) {
    const err = e as {
      response?: { data?: { message?: string; errors?: Record<string, string[]> } }
    }
    error.value = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : (err.response?.data?.message ?? 'Não foi possível salvar o endereço.')
  }
}
async function removeAddress(id: number) {
  if (!window.confirm('Remover este endereço?')) return
  try {
    await deleteAddress(id)
    await load()
  } catch {
    error.value = 'Não foi possível remover o endereço.'
  }
}
onMounted(async () => {
  try {
    await load()
  } catch {
    error.value = 'Não foi possível carregar seus endereços.'
  }
})
</script>
<template>
  <section>
    <h1>Meu perfil</h1>
    <form @submit.prevent="saveProfile">
      <h2>Dados da conta</h2>
      <p><label for="name">Nome</label><br /><input id="name" v-model="profile.name" required /></p>
      <p>
        <label for="email">E-mail</label><br /><input
          id="email"
          v-model="profile.email"
          type="email"
          required
        />
      </p>
      <button type="submit" :disabled="saving">Salvar perfil</button>
    </form>
    <h2>Meus endereços</h2>
    <ul>
      <li v-for="a in addresses" :key="a.id">
        {{ a.recipient_name }} — {{ a.street }}, {{ a.number }}, {{ a.city }}/{{ a.state }}
        <button type="button" @click="edit(a)">Editar</button>
        <button type="button" @click="removeAddress(a.id)">Excluir</button>
      </li>
    </ul>
    <form @submit.prevent="saveAddress">
      <h3>{{ editing ? 'Editar endereço' : 'Adicionar endereço' }}</h3>
      <p>
        <label for="recipient-name">Destinatário</label><br /><input
          id="recipient-name"
          v-model="address.recipient_name"
          required
        />
      </p>
      <p>
        <label for="postal-code">CEP</label><br /><input
          id="postal-code"
          v-model="address.postal_code"
          required
        />
      </p>
      <p>
        <label for="address-street">Rua</label><br /><input
          id="address-street"
          v-model="address.street"
          required
        />
      </p>
      <p>
        <label for="address-number">Número</label><br /><input
          id="address-number"
          v-model="address.number"
          required
        />
      </p>
      <p>
        <label for="address-complement">Complemento</label><br /><input
          id="address-complement"
          v-model="address.complement"
        />
      </p>
      <p>
        <label for="address-neighborhood">Bairro</label><br /><input
          id="address-neighborhood"
          v-model="address.neighborhood"
          required
        />
      </p>
      <p>
        <label for="address-city">Cidade</label><br /><input
          id="address-city"
          v-model="address.city"
          required
        />
      </p>
      <p>
        <label for="address-state">Estado</label><br /><input
          id="address-state"
          v-model="address.state"
          required
        />
      </p>
      <p>
        <label for="address-country">País</label><br /><input
          id="address-country"
          v-model="address.country"
          required
        />
      </p>
      <button type="submit">{{ editing ? 'Atualizar endereço' : 'Salvar endereço' }}</button>
      <button v-if="editing" type="button" @click="resetAddress">Cancelar edição</button>
    </form>
    <p v-if="error" role="alert">{{ error }}</p>
    <p v-if="message" role="status">{{ message }}</p>
  </section>
</template>
