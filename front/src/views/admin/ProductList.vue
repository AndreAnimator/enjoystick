<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { listAdminProducts, deleteProduct } from '@/api/products'
import type { Product } from '@/types/models'
import { Package, Plus, Pencil, Power, AlertCircle, RefreshCw } from 'lucide-vue-next'

const products = ref<Product[]>([])
const loading = ref(false)
const error = ref('')

function formatPrice(value: string | number) {
  return Number(value).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  })
}

async function load() {
  loading.value = true
  error.value = ''

  try {
    products.value = (await listAdminProducts({ sort: 'recent' })).data
  } catch {
    error.value = 'Não foi possível carregar os produtos.'
  } finally {
    loading.value = false
  }
}

async function remove(id: number) {
  if (!window.confirm('Deseja realmente desativar este produto?')) return

  error.value = ''

  try {
    await deleteProduct(id)
    await load()
  } catch {
    error.value = 'Não foi possível desativar o produto.'
  }
}

onMounted(load)
</script>

<template>
  <section class="font-HelveticaRounded">
    <header class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-3">
        <div class="rounded-xl bg-[#fce4f1] p-3 text-[#C9007A]">
          <Package :size="25" />
        </div>
        <div>
          <h2 class="text-2xl font-bold text-[#C9007A]">Produtos</h2>
          <p class="text-sm text-gray-500">Gerencie o catálogo da sua loja.</p>
        </div>
      </div>

      <div class="flex flex-wrap gap-2">
        <button
          type="button"
          @click="load"
          :disabled="loading"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#C9007A] px-4 py-3 font-bold text-[#C9007A] transition hover:bg-[#fce4f1] disabled:opacity-50"
        >
          <RefreshCw :size="17" :class="{ 'animate-spin': loading }" />
          Atualizar
        </button>

        <RouterLink
          to="/admin/produtos/novo"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-5 py-3 font-bold text-white transition hover:bg-[#A80065]"
        >
          <Plus :size="19" />
          Cadastrar produto
        </RouterLink>
      </div>
    </header>

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
      Carregando produtos...
    </div>

    <div v-else-if="products.length" class="overflow-x-auto rounded-xl border border-gray-100">
      <table class="w-full min-w-[700px] text-left text-sm">
        <thead class="bg-[#fce4f1] text-[#A80065]">
          <tr>
            <th class="px-5 py-4 font-bold">Produto</th>
            <th class="px-5 py-4 font-bold">Preço</th>
            <th class="px-5 py-4 font-bold">Estoque</th>
            <th class="px-5 py-4 font-bold">Situação</th>
            <th class="px-5 py-4 font-bold">Ações</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 bg-white">
          <tr
            v-for="p in products"
            :key="p.id"
            class="transition hover:bg-[#fff9fc]"
          >
            <td class="px-5 py-4 font-bold text-gray-800">
              {{ p.name }}
            </td>

            <td class="px-5 py-4 font-semibold text-[#C9007A]">
              {{ formatPrice(p.price) }}
            </td>

            <td class="px-5 py-4 text-gray-700">
              {{ p.stock }}
            </td>

            <td class="px-5 py-4">
              <span
                class="rounded-full px-3 py-1.5 text-xs font-bold"
                :class="p.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
              >
                {{ p.active ? 'Ativo' : 'Inativo' }}
              </span>
            </td>

            <td class="px-5 py-4">
              <div class="flex items-center gap-2">
                <RouterLink
                  :to="`/admin/produtos/${p.id}/editar`"
                  class="inline-flex items-center gap-1.5 rounded-lg border border-[#C9007A] px-3 py-2 font-bold text-[#C9007A] transition hover:bg-[#fce4f1]"
                >
                  <Pencil :size="15" />
                  Editar
                </RouterLink>

                <button
                  type="button"
                  @click="remove(p.id)"
                  :disabled="!p.active"
                  class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 font-bold text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40"
                >
                  <Power :size="15" />
                  Desativar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <p
      v-else
      class="rounded-xl bg-gray-50 p-10 text-center text-gray-500"
    >
      Nenhum produto cadastrado.
    </p>
  </section>
</template>