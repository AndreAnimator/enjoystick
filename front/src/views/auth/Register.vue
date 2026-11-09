<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
const auth = useAuthStore()
const cart = useCartStore()
const router = useRouter()
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const error = ref('')
const loading = ref(false)
async function submit() {
  error.value = ''
  loading.value = true
  try {
    await auth.register(form.value)
    await cart.fetchCart()
    await router.replace('/')
  } catch (e: unknown) {
    const err = e as {
      response?: { data?: { message?: string; errors?: Record<string, string[]> } }
    }
    error.value = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : (err.response?.data?.message ?? 'Não foi possível criar a conta.')
  } finally {
    loading.value = false
  }
}
</script>
<template>
  <section>
    <h1>Criar conta</h1>
    <form @submit.prevent="submit">
      <p>
        <label for="name">Nome</label><br /><input
          id="name"
          v-model.trim="form.name"
          autocomplete="name"
          required
          maxlength="255"
        />
      </p>
      <p>
        <label for="email">E-mail</label><br /><input
          id="email"
          v-model.trim="form.email"
          type="email"
          autocomplete="email"
          required
        />
      </p>
      <p>
        <label for="password">Senha</label><br /><input
          id="password"
          v-model="form.password"
          type="password"
          autocomplete="new-password"
          required
          minlength="8"
        />
      </p>
      <p>
        <label for="confirmation">Confirme a senha</label><br /><input
          id="confirmation"
          v-model="form.password_confirmation"
          type="password"
          autocomplete="new-password"
          required
        />
      </p>
      <p v-if="error" role="alert">{{ error }}</p>
      <button type="submit" :disabled="loading">{{ loading ? 'Criando...' : 'Cadastrar' }}</button>
    </form>
    <p>Já tem conta? <RouterLink to="/login">Entrar</RouterLink>.</p>
  </section>
</template>
