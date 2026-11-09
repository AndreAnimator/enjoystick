<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { listCategories, getAdminProduct, saveProduct } from '@/api/products'
import type { Category, Product } from '@/types/models'
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
        price: Number(form.value.price),
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
  <section>
    <h2>{{ editing ? 'Editar produto' : 'Novo produto' }}</h2>
    <form @submit.prevent="submit">
      <p>
        <label for="name">Nome</label><br /><input
          id="name"
          v-model.trim="form.name"
          required
          maxlength="255"
        />
      </p>
      <p>
        <label for="description">Descrição</label><br /><textarea
          id="description"
          v-model="form.description"
        ></textarea>
      </p>
      <p>
        <label for="category">Categoria</label><br /><select
          id="category"
          v-model.number="form.category_id"
          required
        >
          <option :value="0" disabled>Selecione</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </p>
      <p>
        <label for="price">Preço</label><br /><input
          id="price"
          v-model="form.price"
          type="number"
          min="0"
          step="0.01"
          required
        />
      </p>
      <p>
        <label for="stock">Estoque</label><br /><input
          id="stock"
          v-model.number="form.stock"
          type="number"
          min="0"
          required
        />
      </p>
      <p>
        <label for="image">URL da imagem (opcional)</label><br /><input
          id="image"
          v-model="form.image"
          type="url"
        />
      </p>
      <p>
        <label
          ><input v-model.number="form.active" type="checkbox" :true-value="1" :false-value="0" />
          Produto ativo</label
        >
      </p>
      <p v-if="error" role="alert">{{ error }}</p>
      <button type="submit" :disabled="loading">{{ loading ? 'Salvando...' : 'Salvar' }}</button>
      <RouterLink to="/admin/produtos">Cancelar</RouterLink>
    </form>
  </section>
</template>
