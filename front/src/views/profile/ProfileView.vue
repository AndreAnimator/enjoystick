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
import {
  UserRound,
  MapPin,
  Pencil,
  Trash2,
  Plus,
  Save,
  X,
  Mail,
  User,
  Home,
  CheckCircle,
  AlertCircle,
  MapPinned,
} from 'lucide-vue-next'

const auth = useAuthStore()
const addresses = ref<Address[]>([])
const profile = ref({
  name: auth.user?.name ?? '',
  email: auth.user?.email ?? '',
})
const editing = ref<number | null>(null)
const error = ref('')
const message = ref('')
const saving = ref(false)
const loadingAddresses = ref(true)

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

    if (profile.value.name !== auth.user?.name) {
      payload.name = profile.value.name
    }

    if (profile.value.email !== auth.user?.email) {
      payload.email = profile.value.email
    }

    if (Object.keys(payload).length) {
      await auth.update(payload)
    }

    message.value = 'Perfil atualizado com sucesso.'
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

  message.value = ''
  error.value = ''
}

function resetAddress() {
  editing.value = null
  address.value = emptyAddress()
}

async function saveAddress() {
  error.value = ''
  message.value = ''

  try {
    if (editing.value) {
      await updateAddress(editing.value, address.value)
    } else {
      await createAddress(address.value)
    }

    await load()
    resetAddress()
    message.value = 'Endereço salvo com sucesso.'
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
  if (!window.confirm('Deseja realmente remover este endereço?')) return

  error.value = ''
  message.value = ''

  try {
    await deleteAddress(id)
    await load()

    if (editing.value === id) {
      resetAddress()
    }

    message.value = 'Endereço removido com sucesso.'
  } catch {
    error.value = 'Não foi possível remover o endereço.'
  }
}

onMounted(async () => {
  try {
    await load()
  } catch {
    error.value = 'Não foi possível carregar seus endereços.'
  } finally {
    loadingAddresses.value = false
  }
})
</script>

<template>
  <main class="min-h-screen bg-[#D9D9D9] px-4 py-8 font-HelveticaRounded sm:px-6">
    <div class="mx-auto max-w-5xl">
      <!-- Cabeçalho -->
      <header class="mb-8 flex items-center gap-3">
        <div class="rounded-xl bg-[#C9007A] p-3 text-white">
          <UserRound :size="27" />
        </div>

        <div>
          <h1 class="text-2xl font-bold text-[#C9007A] sm:text-3xl">
            Meu perfil
          </h1>
          <p class="text-sm text-gray-600">
            Gerencie seus dados pessoais e endereços de entrega.
          </p>
        </div>
      </header>

      <!-- Mensagens -->
      <div
        v-if="error"
        role="alert"
        class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-white p-4 text-sm text-red-700 shadow-sm"
      >
        <AlertCircle :size="20" class="mt-0.5 shrink-0" />
        {{ error }}
      </div>

      <div
        v-if="message"
        role="status"
        class="mb-5 flex items-start gap-3 rounded-xl border border-green-200 bg-white p-4 text-sm text-green-700 shadow-sm"
      >
        <CheckCircle :size="20" class="mt-0.5 shrink-0" />
        {{ message }}
      </div>

      <!-- Dados da conta -->
      <section class="mb-8 rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-6 flex items-center gap-3">
          <div class="rounded-lg bg-[#fce4f1] p-2.5 text-[#C9007A]">
            <User :size="22" />
          </div>

          <div>
            <h2 class="text-xl font-bold text-gray-800">
              Dados da conta
            </h2>
            <p class="text-sm text-gray-500">
              Atualize suas informações pessoais.
            </p>
          </div>
        </div>

        <form @submit.prevent="saveProfile" class="space-y-5">
          <div>
            <label for="name" class="mb-2 block text-sm font-bold text-gray-700">
              Nome completo
            </label>

            <div class="relative">
              <User
                :size="19"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-[#C9007A]"
              />

              <input
                id="name"
                v-model.trim="profile.name"
                required
                maxlength="255"
                autocomplete="name"
                placeholder="Seu nome completo"
                class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-12 pr-4 text-gray-800 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
              />
            </div>
          </div>

          <div>
            <label for="email" class="mb-2 block text-sm font-bold text-gray-700">
              E-mail
            </label>

            <div class="relative">
              <Mail
                :size="19"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-[#C9007A]"
              />

              <input
                id="email"
                v-model.trim="profile.email"
                type="email"
                required
                autocomplete="email"
                placeholder="seuemail@exemplo.com"
                class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-12 pr-4 text-gray-800 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
              />
            </div>
          </div>

          <div class="flex justify-end border-t border-gray-100 pt-5">
            <button
              type="submit"
              :disabled="saving"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-6 py-3 font-bold text-white transition hover:bg-[#A80065] disabled:cursor-not-allowed disabled:opacity-50"
            >
              <Save :size="18" />
              {{ saving ? 'Salvando...' : 'Salvar perfil' }}
            </button>
          </div>
        </form>
      </section>

      <!-- Endereços cadastrados -->
      <section class="mb-8 rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-3">
            <div class="rounded-lg bg-[#fce4f1] p-2.5 text-[#C9007A]">
              <MapPin :size="22" />
            </div>

            <div>
              <h2 class="text-xl font-bold text-gray-800">
                Meus endereços
              </h2>
              <p class="text-sm text-gray-500">
                Endereços cadastrados para suas entregas.
              </p>
            </div>
          </div>

          <span class="w-fit rounded-full bg-[#fce4f1] px-4 py-2 text-sm font-bold text-[#C9007A]">
            {{ addresses.length }}
            {{ addresses.length === 1 ? 'endereço' : 'endereços' }}
          </span>
        </div>

        <div
          v-if="loadingAddresses"
          class="rounded-xl bg-gray-50 p-8 text-center text-gray-500"
        >
          <div class="mx-auto mb-3 h-8 w-8 animate-spin rounded-full border-4 border-[#f1abd5] border-t-[#C9007A]"></div>
          Carregando endereços...
        </div>

        <div v-else-if="addresses.length" class="space-y-4">
          <article
            v-for="a in addresses"
            :key="a.id"
            class="rounded-xl border border-gray-200 p-4 transition hover:border-pink-200 sm:p-5"
          >
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
              <div class="flex items-start gap-3">
                <div class="rounded-lg bg-[#fce4f1] p-2 text-[#C9007A]">
                  <Home :size="20" />
                </div>

                <div>
                  <h3 class="font-bold text-gray-800">
                    {{ a.recipient_name }}
                  </h3>

                  <p class="mt-1 text-sm leading-relaxed text-gray-600">
                    {{ a.street }}, {{ a.number }}
                    <template v-if="a.complement">
                      — {{ a.complement }}
                    </template>
                    <br />
                    {{ a.neighborhood }} — {{ a.city }}/{{ a.state }}
                    <br />
                    CEP: {{ a.postal_code }}
                    <br />
                    {{ a.country }}
                  </p>
                </div>
              </div>

              <div class="flex gap-2 sm:shrink-0">
                <button
                  type="button"
                  @click="edit(a)"
                  class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-[#C9007A] px-4 py-2 text-sm font-bold text-[#C9007A] transition hover:bg-[#fce4f1] sm:flex-none"
                >
                  <Pencil :size="16" />
                  Editar
                </button>

                <button
                  type="button"
                  @click="removeAddress(a.id)"
                  class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50 sm:flex-none"
                >
                  <Trash2 :size="16" />
                  Excluir
                </button>
              </div>
            </div>
          </article>
        </div>

        <div
          v-else
          class="rounded-xl bg-gray-50 px-5 py-10 text-center"
        >
          <MapPinned :size="42" class="mx-auto mb-3 text-[#f1abd5]" />
          <h3 class="mb-1 font-bold text-gray-700">
            Nenhum endereço cadastrado
          </h3>
          <p class="text-sm text-gray-500">
            Adicione um endereço para facilitar suas próximas compras.
          </p>
        </div>
      </section>

      <!-- Formulário de endereço -->
      <section class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-6 flex items-center gap-3">
          <div class="rounded-lg bg-[#fce4f1] p-2.5 text-[#C9007A]">
            <Plus v-if="!editing" :size="22" />
            <Pencil v-else :size="22" />
          </div>

          <div>
            <h2 class="text-xl font-bold text-gray-800">
              {{ editing ? 'Editar endereço' : 'Adicionar endereço' }}
            </h2>
            <p class="text-sm text-gray-500">
              {{ editing ? 'Atualize os dados deste endereço.' : 'Cadastre um novo endereço de entrega.' }}
            </p>
          </div>
        </div>

        <form @submit.prevent="saveAddress" class="space-y-5">
          <div>
            <label for="recipient-name" class="mb-2 block text-sm font-bold text-gray-700">
              Nome do destinatário
            </label>
            <input
              id="recipient-name"
              v-model.trim="address.recipient_name"
              required
              autocomplete="name"
              placeholder="Nome de quem receberá o pedido"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
            />
          </div>

          <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div>
              <label for="postal-code" class="mb-2 block text-sm font-bold text-gray-700">
                CEP
              </label>
              <input
                id="postal-code"
                v-model.trim="address.postal_code"
                required
                autocomplete="postal-code"
                placeholder="00000-000"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
              />
            </div>

            <div class="sm:col-span-2">
              <label for="address-street" class="mb-2 block text-sm font-bold text-gray-700">
                Rua
              </label>
              <input
                id="address-street"
                v-model.trim="address.street"
                required
                autocomplete="address-line1"
                placeholder="Nome da rua ou avenida"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
              <label for="address-number" class="mb-2 block text-sm font-bold text-gray-700">
                Número
              </label>
              <input
                id="address-number"
                v-model.trim="address.number"
                required
                placeholder="Número do imóvel"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
              />
            </div>

            <div>
              <label for="address-complement" class="mb-2 block text-sm font-bold text-gray-700">
                Complemento
                <span class="font-normal text-gray-400">(opcional)</span>
              </label>
              <input
                id="address-complement"
                v-model.trim="address.complement"
                autocomplete="address-line2"
                placeholder="Apartamento, bloco, casa..."
                class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
              />
            </div>
          </div>

          <div>
            <label for="address-neighborhood" class="mb-2 block text-sm font-bold text-gray-700">
              Bairro
            </label>
            <input
              id="address-neighborhood"
              v-model.trim="address.neighborhood"
              required
              placeholder="Nome do bairro"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
            />
          </div>

          <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="sm:col-span-2">
              <label for="address-city" class="mb-2 block text-sm font-bold text-gray-700">
                Cidade
              </label>
              <input
                id="address-city"
                v-model.trim="address.city"
                required
                autocomplete="address-level2"
                placeholder="Sua cidade"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
              />
            </div>

            <div>
              <label for="address-state" class="mb-2 block text-sm font-bold text-gray-700">
                Estado
              </label>
              <input
                id="address-state"
                v-model.trim="address.state"
                required
                autocomplete="address-level1"
                placeholder="UF"
                maxlength="2"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 uppercase outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
              />
            </div>
          </div>

          <div>
            <label for="address-country" class="mb-2 block text-sm font-bold text-gray-700">
              País
            </label>
            <input
              id="address-country"
              v-model.trim="address.country"
              required
              autocomplete="country-name"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
            />
          </div>

          <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
            <button
              v-if="editing"
              type="button"
              @click="resetAddress"
              class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-6 py-3 font-bold text-gray-600 transition hover:bg-gray-100"
            >
              <X :size="18" />
              Cancelar edição
            </button>

            <button
              type="submit"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-6 py-3 font-bold text-white transition hover:bg-[#A80065]"
            >
              <Save :size="18" />
              {{ editing ? 'Atualizar endereço' : 'Salvar endereço' }}
            </button>
          </div>
        </form>
      </section>
    </div>
  </main>
</template>