<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/api/client'
import type { Category } from '@/types/models'
import type { Resource, ResourceList } from '@/types/api'
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
    if (editing.value)
      await api.patch<Resource<Category>>(`/api/admin/categories/${editing.value}`, {
        name: name.value,
      })
    else await api.post('/api/admin/categories', { name: name.value })
    await load()
    reset()
    message.value = 'Categoria salva.'
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
  if (!window.confirm('Excluir esta categoria?')) return
  try {
    await api.delete(`/api/admin/categories/${id}`)
    await load()
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
  <section>
    <h2>Categorias</h2>
    <form @submit.prevent="save">
      <label for="category-name">Nome</label>
      <input id="category-name" v-model.trim="name" required maxlength="255" /><button
        type="submit"
      >
        {{ editing ? 'Atualizar' : 'Criar categoria' }}</button
      ><button v-if="editing" type="button" @click="reset">Cancelar</button>
    </form>
    <p v-if="error" role="alert">{{ error }}</p>
    <p v-if="message" role="status">{{ message }}</p>
    <ul>
      <li v-for="category in categories" :key="category.id">
        {{ category.name }} <button type="button" @click="edit(category)">Editar</button>
        <button type="button" @click="remove(category.id)">Excluir</button>
      </li>
    </ul>
  </section>
</template>
