<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { listCategories, getAdminProduct, saveProduct } from '@/api/products'
import type { Category, Product } from '@/types/models'
import { Package, Save, ArrowLeft, AlertCircle, Image as ImageIcon } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()

const categories = ref<Category[]>([])
const editing = typeof route.params.id === 'string'
const loading = ref(false)
const error = ref('')

const form = ref({
  name: '',
  description: '',
  category_id: 0,
  price: '',
  stock: 0,
  active: 1,
  image: '',
})

async function submit() {
  error.value = ''
  loading.value = true

  try {
    await saveProduct(
      {
        ...form.value,
        price: form.value.price,
        description: form.value.description || null,
        image: form.value.image || null,
      },
      editing ? Number(route.params.id) : undefined,
    )

    await router.push('/admin/produtos')
  } catch (e: unknown) {
    const err = e as {
      response?: { data?: { message?: string; errors?: Record<string, string[]> } }
    }

    error.value = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : (err.response?.data?.message ?? 'Não foi possível salvar o produto.')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    categories.value = await listCategories()

    if (editing) {
      const p: Product = await getAdminProduct(Number(route.params.id))

      form.value = {
        name: p.name,
        description: p.description ?? '',
        category_id: p.category_id ?? p.category?.id ?? 0,
        price: String(p.price),
        stock: p.stock,
        active: p.active ? 1 : 0,
        image: p.image ?? '',
      }
    }
  } catch {
    error.value = 'Não foi possível carregar os dados do produto.'
  }
})
</script>

<template>
  <section class="mx-auto max-w-3xl font-HelveticaRounded">
    <header class="mb-7 flex items-center gap-3">
      <div class="rounded-xl bg-[#fce4f1] p-3 text-[#C9007A]">
        <Package :size="25" />
      </div>
      <div>
        <h2 class="text-2xl font-bold text-[#C9007A]">
          {{ editing ? 'Editar produto' : 'Novo produto' }}
        </h2>
        <p class="text-sm text-gray-500">
          Preencha as informações do produto.
        </p>
      </div>
    </header>

    <form @submit.prevent="submit" class="space-y-5">
      <div>
        <label for="name" class="mb-2 block text-sm font-bold text-gray-700">
          Nome do produto
        </label>
        <input
          id="name"
          v-model.trim="form.name"
          required
          maxlength="255"
          placeholder="Digite o nome do produto"
          class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
        />
      </div>

      <div>
        <label for="description" class="mb-2 block text-sm font-bold text-gray-700">
          Descrição
        </label>
        <textarea
          id="description"
          v-model="form.description"
          rows="4"
          placeholder="Descreva o produto..."
          class="w-full resize-y rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
        ></textarea>
      </div>

      <div>
        <label for="category" class="mb-2 block text-sm font-bold text-gray-700">
          Categoria
        </label>
        <select
          id="category"
          v-model.number="form.category_id"
          required
          class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
        >
          <option :value="0" disabled>Selecione uma categoria</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">
            {{ c.name }}
          </option>
        </select>
      </div>

      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label for="price" class="mb-2 block text-sm font-bold text-gray-700">
            Preço (R$)
          </label>
          <input
            id="price"
            v-model="form.price"
            type="number"
            min="0"
            step="0.01"
            required
            placeholder="0,00"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
          />
        </div>

        <div>
          <label for="stock" class="mb-2 block text-sm font-bold text-gray-700">
            Quantidade em estoque
          </label>
          <input
            id="stock"
            v-model.number="form.stock"
            type="number"
            min="0"
            required
            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
          />
        </div>
      </div>

      <div>
        <label for="image" class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700">
          <ImageIcon :size="17" />
          URL da imagem (opcional)
        </label>
        <input
          id="image"
          v-model="form.image"
          type="url"
          placeholder="https://..."
          class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
        />
      </div>

      <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-pink-100 bg-[#fff9fc] p-4">
        <input
          v-model.number="form.active"
          type="checkbox"
          :true-value="1"
          :false-value="0"
          class="h-5 w-5 accent-[#C9007A]"
        />
        <span>
          <span class="block font-bold text-gray-800">Produto ativo</span>
          <span class="text-sm text-gray-500">
            Produtos ativos ficam disponíveis na loja.
          </span>
        </span>
      </label>

      <div
        v-if="error"
        role="alert"
        class="flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
      >
        <AlertCircle :size="19" />
        {{ error }}
      </div>

      <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
        <RouterLink
          to="/admin/produtos"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-6 py-3 font-bold text-gray-600 transition hover:bg-gray-100"
        >
          <ArrowLeft :size="18" />
          Cancelar
        </RouterLink>

        <button
          type="submit"
          :disabled="loading"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-6 py-3 font-bold text-white transition hover:bg-[#A80065] disabled:cursor-not-allowed disabled:opacity-50"
        >
          <Save :size="18" />
          {{ loading ? 'Salvando...' : 'Salvar produto' }}
        </button>
      </div>
    </form>
  </section>
</template>