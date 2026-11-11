<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { createOrder } from '@/api/orders'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import type { Address } from '@/types/models'
import { listAddresses } from '@/api/addresses'
import {
  MapPin,
  User,
  Mail,
  Home,
  Building2,
  Map,
  Globe,
  Package,
  ShoppingBag,
  Truck,
  CreditCard,
  CheckCircle,
  AlertCircle,
  ArrowLeft,
  ArrowRight,
  Plus,
} from 'lucide-vue-next'

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

const shippingCost = 10

const canSubmit = computed(
  () => cart.items.length > 0 && !cart.hasUnavailableItems,
)

const total = computed(
  () => Number(cart.total) + shippingCost,
)

function formatPrice(price: number | string) {
  return Number(price).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
}

function useAddress() {
  const a = addresses.value.find(
    (item) => item.id === selected.value,
  )

  if (a) {
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
}

async function submit() {
  if (!canSubmit.value || loading.value) return

  loading.value = true
  error.value = ''

  try {
    const order = await createOrder({
      ...form.value,
      shipping_complement: form.value.shipping_complement || null,
      user_id: auth.user!.id,
    })

    await cart.clear()

    await router.push({
      name: 'order-detail',
      params: { id: order.id },
    })
  } catch (e: unknown) {
    const err = e as {
      response?: {
        data?: {
          message?: string
          errors?: Record<string, string[]>
        }
      }
    }

    error.value = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : (err.response?.data?.message ??
          'Não foi possível finalizar o pedido.')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    await cart.fetchCart()
  } catch {
    error.value = 'Não foi possível carregar o carrinho.'
  }

  try {
    addresses.value = await listAddresses()
  } catch {
    // O usuário pode informar um endereço novo.
  }
})
</script>

<template>
  <main
    class="min-h-screen w-full bg-[#D9D9D9] font-HelveticaRounded"
  >
    <!-- Cabeçalho -->
    <section
      class="flex min-h-16 items-center gap-3 bg-[#C9007A] px-5 py-3 text-white"
    >
      <CreditCard class="h-7 w-7" />

      <h1 class="text-2xl font-bold">
        Finalizar compra
      </h1>
    </section>

    <div class="mx-auto w-full max-w-7xl px-4 py-8">

      <!-- Carrinho vazio -->
      <section
        v-if="!cart.loading && !cart.items.length"
        class="flex min-h-80 flex-col items-center justify-center gap-5 rounded-2xl border-2 border-[#C9007A] bg-white p-8 text-center shadow-md"
      >
        <ShoppingBag class="h-20 w-20 text-[#C9007A]" />

        <h2 class="text-2xl font-bold text-[#C9007A]">
          Seu carrinho está vazio!
        </h2>

        <p class="text-gray-600">
          Adicione produtos antes de finalizar sua compra.
        </p>

        <RouterLink
          to="/carrinho"
          class="flex items-center gap-2 rounded-full bg-[#C9007A] px-6 py-3 font-bold text-white hover:bg-[#A80065]"
        >
          <ArrowLeft class="h-5 w-5" />
          Voltar ao carrinho
        </RouterLink>
      </section>

      <!-- Carregamento -->
      <div
        v-else-if="cart.loading"
        class="flex min-h-80 items-center justify-center"
      >
        <p class="text-xl font-bold text-[#C9007A]">
          Carregando informações...
        </p>
      </div>

      <template v-else>

        <!-- Etapas -->
        <div
          class="mb-7 flex items-center justify-center gap-3 text-sm font-bold sm:text-base"
        >
          <div class="flex items-center gap-2 text-[#C9007A]">
            <CheckCircle class="h-6 w-6" />
            <span>Carrinho</span>
          </div>

          <div class="h-1 w-8 rounded bg-[#C9007A] sm:w-16"></div>

          <div class="flex items-center gap-2 text-[#C9007A]">
            <MapPin class="h-6 w-6" />
            <span>Entrega</span>
          </div>

          <div class="h-1 w-8 rounded bg-gray-400 sm:w-16"></div>

          <div class="flex items-center gap-2 text-gray-400">
            <CreditCard class="h-6 w-6" />
            <span>Conclusão</span>
          </div>
        </div>

        <div
          class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_350px]"
        >

          <!-- Formulário de entrega -->
          <section class="flex min-w-0 flex-col gap-5">

            <div
              class="overflow-hidden rounded-xl border-2 border-[#C9007A] bg-white shadow-sm"
            >

              <!-- Título -->
              <div
                class="flex items-center gap-3 bg-[#C9007A] px-5 py-4 text-white"
              >
                <MapPin class="h-6 w-6" />

                <div>
                  <h2 class="text-xl font-bold">
                    Endereço de entrega
                  </h2>

                  <p class="text-sm text-white/80">
                    Informe onde deseja receber seu pedido.
                  </p>
                </div>
              </div>

              <form
                @submit.prevent="submit"
                class="flex flex-col gap-6 p-5 sm:p-7"
              >

                <!-- Endereços cadastrados -->
                <fieldset
                  v-if="addresses.length"
                  class="flex flex-col gap-3 rounded-xl border-2 border-[#C9007A]/40 bg-[#fdf2f8] p-4"
                >
                  <legend
                    class="px-2 font-bold text-[#C9007A]"
                  >
                    Endereços cadastrados
                  </legend>

                  <label
                    for="saved-address"
                    class="text-sm font-semibold text-gray-600"
                  >
                    Escolha um endereço salvo:
                  </label>

                  <div class="flex items-center gap-3">
                    <MapPin class="h-5 w-5 shrink-0 text-[#C9007A]" />

                    <select
                      id="saved-address"
                      v-model.number="selected"
                      @change="useAddress"
                      class="h-12 w-full rounded-lg border-2 border-[#C9007A]/50 bg-white px-3 text-gray-700 outline-none focus:border-[#C9007A] focus:ring-2 focus:ring-[#C9007A]/20"
                    >
                      <option value="">
                        Informar novo endereço
                      </option>

                      <option
                        v-for="a in addresses"
                        :key="a.id"
                        :value="a.id"
                      >
                        {{ a.street }}, {{ a.number }} — {{ a.city }}
                      </option>
                    </select>
                  </div>
                </fieldset>

                <!-- Destinatário -->
                <div class="flex flex-col gap-4">

                  <h3
                    class="flex items-center gap-2 border-b-2 border-[#C9007A]/20 pb-3 text-lg font-bold text-[#C9007A]"
                  >
                    <User class="h-5 w-5" />
                    Dados do destinatário
                  </h3>

                  <div class="flex flex-col gap-2">
                    <label
                      for="recipient"
                      class="font-bold text-gray-700"
                    >
                      Nome do destinatário *
                    </label>

                    <div
                      class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                    >
                      <User class="h-5 w-5 shrink-0 text-[#C9007A]" />

                      <input
                        id="recipient"
                        v-model="form.shipping_recipient_name"
                        type="text"
                        autocomplete="name"
                        placeholder="Nome completo"
                        required
                        class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                      />
                    </div>
                  </div>

                </div>

                <!-- Endereço -->
                <div class="flex flex-col gap-4">

                  <h3
                    class="flex items-center gap-2 border-b-2 border-[#C9007A]/20 pb-3 text-lg font-bold text-[#C9007A]"
                  >
                    <Home class="h-5 w-5" />
                    Localização
                  </h3>

                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <!-- CEP -->
                    <div class="flex flex-col gap-2">
                      <label
                        for="postal"
                        class="font-bold text-gray-700"
                      >
                        CEP *
                      </label>

                      <div
                        class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                      >
                        <MapPin class="h-5 w-5 shrink-0 text-[#C9007A]" />

                        <input
                          id="postal"
                          v-model="form.shipping_postal_code"
                          type="text"
                          autocomplete="postal-code"
                          placeholder="00000-000"
                          required
                          class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                        />
                      </div>
                    </div>

                    <!-- Rua -->
                    <div class="flex flex-col gap-2">
                      <label
                        for="street"
                        class="font-bold text-gray-700"
                      >
                        Rua / Avenida *
                      </label>

                      <div
                        class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                      >
                        <Home class="h-5 w-5 shrink-0 text-[#C9007A]" />

                        <input
                          id="street"
                          v-model="form.shipping_street"
                          type="text"
                          autocomplete="address-line1"
                          placeholder="Nome da rua"
                          required
                          class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                        />
                      </div>
                    </div>

                    <!-- Número -->
                    <div class="flex flex-col gap-2">
                      <label
                        for="number"
                        class="font-bold text-gray-700"
                      >
                        Número *
                      </label>

                      <div
                        class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                      >
                        <Building2 class="h-5 w-5 shrink-0 text-[#C9007A]" />

                        <input
                          id="number"
                          v-model="form.shipping_number"
                          type="text"
                          placeholder="Número"
                          required
                          class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                        />
                      </div>
                    </div>

                    <!-- Complemento -->
                    <div class="flex flex-col gap-2">
                      <label
                        for="complement"
                        class="font-bold text-gray-700"
                      >
                        Complemento
                      </label>

                      <div
                        class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                      >
                        <Building2 class="h-5 w-5 shrink-0 text-[#C9007A]" />

                        <input
                          id="complement"
                          v-model="form.shipping_complement"
                          type="text"
                          placeholder="Apartamento, bloco..."
                          class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                        />
                      </div>
                    </div>

                    <!-- Bairro -->
                    <div class="flex flex-col gap-2">
                      <label
                        for="neighborhood"
                        class="font-bold text-gray-700"
                      >
                        Bairro *
                      </label>

                      <div
                        class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                      >
                        <MapPin class="h-5 w-5 shrink-0 text-[#C9007A]" />

                        <input
                          id="neighborhood"
                          v-model="form.shipping_neighborhood"
                          type="text"
                          autocomplete="address-level3"
                          placeholder="Seu bairro"
                          required
                          class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                        />
                      </div>
                    </div>

                    <!-- Cidade -->
                    <div class="flex flex-col gap-2">
                      <label
                        for="city"
                        class="font-bold text-gray-700"
                      >
                        Cidade *
                      </label>

                      <div
                        class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                      >
                        <Building2 class="h-5 w-5 shrink-0 text-[#C9007A]" />

                        <input
                          id="city"
                          v-model="form.shipping_city"
                          type="text"
                          autocomplete="address-level2"
                          placeholder="Sua cidade"
                          required
                          class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                        />
                      </div>
                    </div>

                    <!-- Estado -->
                    <div class="flex flex-col gap-2">
                      <label
                        for="state"
                        class="font-bold text-gray-700"
                      >
                        Estado *
                      </label>

                      <div
                        class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                      >
                        <Map class="h-5 w-5 shrink-0 text-[#C9007A]" />

                        <input
                          id="state"
                          v-model="form.shipping_state"
                          type="text"
                          autocomplete="address-level1"
                          placeholder="UF ou estado"
                          required
                          class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                        />
                      </div>
                    </div>

                    <!-- País -->
                    <div class="flex flex-col gap-2">
                      <label
                        for="country"
                        class="font-bold text-gray-700"
                      >
                        País *
                      </label>

                      <div
                        class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/50 px-3 focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
                      >
                        <Globe class="h-5 w-5 shrink-0 text-[#C9007A]" />

                        <input
                          id="country"
                          v-model="form.shipping_country"
                          type="text"
                          autocomplete="country-name"
                          placeholder="País"
                          required
                          class="h-full w-full bg-transparent outline-none placeholder:text-gray-400"
                        />
                      </div>
                    </div>

                  </div>
                </div>

                <!-- Erro -->
                <div
                  v-if="error"
                  role="alert"
                  class="flex items-start gap-3 rounded-lg border border-red-400 bg-red-50 p-4 text-sm font-semibold text-red-600"
                >
                  <AlertCircle class="mt-0.5 h-5 w-5 shrink-0" />

                  {{ error }}
                </div>

                <!-- Aviso de indisponibilidade -->
                <div
                  v-if="cart.hasUnavailableItems"
                  class="flex items-start gap-3 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-600"
                >
                  <AlertCircle class="mt-0.5 h-5 w-5 shrink-0" />

                  <p>
                    Existem produtos indisponíveis no carrinho.
                    Remova-os antes de finalizar o pedido.
                  </p>
                </div>

                <!-- Botões -->
                <div
                  class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-between"
                >
                  <RouterLink
                    to="/carrinho"
                    class="flex h-12 items-center justify-center gap-2 rounded-full border-2 border-[#C9007A] px-5 font-bold text-[#C9007A] transition-colors hover:bg-[#fce4f1]"
                  >
                    <ArrowLeft class="h-5 w-5" />
                    Voltar ao carrinho
                  </RouterLink>

                  <button
                    type="submit"
                    :disabled="loading || !canSubmit"
                    class="flex h-12 items-center justify-center gap-2 rounded-full bg-[#C9007A] px-7 font-bold text-white transition-colors hover:bg-[#A80065] disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    <CheckCircle class="h-5 w-5" />

                    {{ loading ? 'Processando...' : 'Confirmar pedido' }}

                    <ArrowRight
                      v-if="!loading"
                      class="h-5 w-5"
                    />
                  </button>
                </div>

              </form>
            </div>
          </section>

          <!-- Resumo do pedido -->
          <aside
            class="flex h-fit flex-col gap-4 rounded-xl border-2 border-[#C9007A] bg-white p-5 shadow-md lg:sticky lg:top-5"
          >

            <h2
              class="flex items-center gap-2 border-b-2 border-[#C9007A]/30 pb-3 text-xl font-bold text-[#C9007A]"
            >
              <ShoppingBag class="h-6 w-6" />
              Resumo do pedido
            </h2>

            <!-- Produtos -->
            <div class="flex flex-col gap-3">

              <div
                v-for="item in cart.items"
                :key="item.id"
                class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3"
              >
                <div class="min-w-0">
                  <p class="break-words font-semibold text-gray-800">
                    {{ item.name }}
                  </p>

                  <p class="text-sm text-gray-500">
                    Quantidade: {{ item.quantity }}
                  </p>
                </div>

                <strong class="shrink-0 text-[#C9007A]">
                  {{ formatPrice(item.subtotal) }}
                </strong>
              </div>

            </div>

            <!-- Totais -->
            <div class="flex flex-col gap-3 border-t border-gray-200 pt-4">

              <div class="flex justify-between gap-3 text-gray-600">
                <span>Quantidade de itens</span>

                <strong>
                  {{ cart.itemCount }}
                </strong>
              </div>

              <div class="flex justify-between gap-3 text-gray-600">
                <span>Subtotal</span>

                <strong>
                  {{ formatPrice(Number(cart.total)) }}
                </strong>
              </div>

              <div class="flex justify-between gap-3 text-gray-600">
                <span class="flex items-center gap-2">
                  <Truck class="h-5 w-5 text-[#C9007A]" />
                  Frete
                </span>

                <strong>
                  {{ formatPrice(shippingCost) }}
                </strong>
              </div>

            </div>

            <!-- Total -->
            <div
              class="rounded-lg bg-[#fce4f1] p-4"
            >
              <p class="text-sm font-semibold text-gray-600">
                Total previsto
              </p>

              <p class="mt-1 text-3xl font-bold text-[#C9007A]">
                {{ formatPrice(total) }}
              </p>
            </div>

            <!-- Aviso -->
            <div
              class="flex items-start gap-2 text-xs text-gray-500"
            >
              <Truck class="mt-0.5 h-4 w-4 shrink-0 text-[#C9007A]" />

              <p>
                O valor do frete é uma estimativa. Confira os valores
                definitivos na confirmação do pedido.
              </p>
            </div>

            <div
              class="flex items-center justify-center gap-2 border-t border-gray-200 pt-4 text-sm font-semibold text-[#C9007A]"
            >
              <CheckCircle class="h-5 w-5" />
              Compra segura
            </div>

          </aside>

        </div>
      </template>
    </div>
  </main>
</template>