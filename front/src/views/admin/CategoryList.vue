<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/api/client'
import type { Category } from '@/types/models'
import type { Resource, ResourceList } from '@/types/api'
import { Tags, Pencil, Trash2, Plus, X, AlertCircle, CheckCircle } from 'lucide-vue-next'

const categories = ref<Category[]>([])
const name = ref('')
const editing = ref<number | null>(null)
const error = ref('')
const message = ref('')

async function load() {
  const { data } = await api.get<ResourceList<Category>>('/api/admin/categories')
  categories.value = data.data
}

function edit(category: Category) {
  editing.value = category.id
  name.value = category.name
}

function reset() {
  editing.value = null
  name.value = ''
}

async function save() {
  error.value = ''
  message.value = ''

  try {
    if (editing.value) {
      await api.patch<Resource<Category>>(`/api/admin/categories/${editing.value}`, {
        name: name.value,
      })
    } else {
      await api.post('/api/admin/categories', { name: name.value })
    }

    await load()
    reset()
    message.value = 'Categoria salva com sucesso.'
  } catch (e: unknown) {
    const err = e as {
      response?: { data?: { message?: string; errors?: Record<string, string[]> } }
    }

    error.value = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : (err.response?.data?.message ?? 'Não foi possível salvar a categoria.')
  }
}

async function remove(id: number) {
  if (!window.confirm('Deseja realmente excluir esta categoria?')) return

  error.value = ''
  message.value = ''

  try {
    await api.delete(`/api/admin/categories/${id}`)
    await load()
    message.value = 'Categoria excluída com sucesso.'
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }
    error.value =
      err.response?.data?.message ??
      'Não foi possível excluir. Verifique se há produtos vinculados.'
  }
}

onMounted(() => {
  load().catch(() => {
    error.value = 'Não foi possível carregar as categorias.'
  })
})
</script>

<template>
  <section class="font-HelveticaRounded">
    <header class="mb-7 flex items-center gap-3">
      <div class="rounded-xl bg-[#fce4f1] p-3 text-[#C9007A]">
        <Tags :size="25" />
      </div>
      <div>
        <h2 class="text-2xl font-bold text-[#C9007A]">Categorias</h2>
        <p class="text-sm text-gray-500">Organize os produtos da sua loja.</p>
      </div>
    </header>

    <form
      @submit.prevent="save"
      class="mb-7 rounded-2xl border border-pink-100 bg-[#fff9fc] p-5"
    >
      <h3 class="mb-4 font-bold text-gray-800">
        {{ editing ? 'Editar categoria' : 'Cadastrar categoria' }}
      </h3>

      <div class="flex flex-col gap-3 sm:flex-row">
        <input
          id="category-name"
          v-model.trim="name"
          required
          maxlength="255"
          placeholder="Nome da categoria"
          class="min-w-0 flex-1 rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-[#C9007A] focus:ring-2 focus:ring-pink-100"
        />

        <button
          type="submit"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-5 py-3 font-bold text-white transition hover:bg-[#A80065]"
        >
          <Plus v-if="!editing" :size="18" />
          <Pencil v-else :size="18" />
          {{ editing ? 'Atualizar' : 'Criar categoria' }}
        </button>

        <button
          v-if="editing"
          type="button"
          @click="reset"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-5 py-3 font-bold text-gray-600 transition hover:bg-gray-100"
        >
          <X :size="18" />
          Cancelar
        </button>
      </div>
    </form>

    <div
      v-if="error"
      role="alert"
      class="mb-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
    >
      <AlertCircle :size="19" />
      {{ error }}
    </div>

    <div
      v-if="message"
      role="status"
      class="mb-4 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700"
    >
      <CheckCircle :size="19" />
      {{ message }}
    </div>

    <div class="mb-4 flex items-center justify-between">
      <h3 class="font-bold text-gray-800">Categorias cadastradas</h3>
      <span class="rounded-full bg-[#fce4f1] px-3 py-1 text-sm font-bold text-[#C9007A]">
        {{ categories.length }}
      </span>
    </div>

    <div v-if="categories.length" class="space-y-3">
      <article
        v-for="category in categories"
        :key="category.id"
        class="flex flex-col gap-3 rounded-xl border border-gray-100 p-4 transition hover:border-pink-200 sm:flex-row sm:items-center sm:justify-between"
      >
        <div class="flex items-center gap-3">
          <div class="rounded-lg bg-[#fce4f1] p-2 text-[#C9007A]">
            <Tags :size="19" />
          </div>
          <span class="font-bold text-gray-800">{{ category.name }}</span>
        </div>

        <div class="flex gap-2">
          <button
            type="button"
            @click="edit(category)"
            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-[#C9007A] px-4 py-2 text-sm font-bold text-[#C9007A] transition hover:bg-[#fce4f1] sm:flex-none"
          >
            <Pencil :size="16" />
            Editar
          </button>

          <button
            type="button"
            @click="remove(category.id)"
            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50 sm:flex-none"
          >
            <Trash2 :size="16" />
            Excluir
          </button>
        </div>
      </article>
    </div>

    <p v-else class="rounded-xl bg-gray-50 p-8 text-center text-gray-500">
      Nenhuma categoria cadastrada.
    </p>
  </section>
</template>