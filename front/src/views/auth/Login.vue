<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
const auth = useAuthStore()
const cart = useCartStore()
const route = useRoute()
const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
async function submit() {
  error.value = ''
  loading.value = true
  try {
    await auth.login({ email: email.value, password: password.value })
    await cart.fetchCart()
    await router.replace(typeof route.query.redirect === 'string' ? route.query.redirect : '/')
  } catch (e: unknown) {
    error.value = getError(e)
  } finally {
    loading.value = false
  }
}
function getError(e: unknown): string {
  const err = e as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
  return err.response?.data?.errors
    ? Object.values(err.response.data.errors).flat().join(' ')
    : (err.response?.data?.message ?? 'Não foi possível entrar. Verifique suas credenciais.')
}
</script>
<template>
  <section>
    <h1>Entrar</h1>
    <form @submit.prevent="submit">
      <p>
        <label for="email">E-mail</label><br /><input
          id="email"
          v-model.trim="email"
          type="email"
          autocomplete="email"
          required
        />
      </p>
      <p>
        <label for="password">Senha</label><br /><input
          id="password"
          v-model="password"
          type="password"
          autocomplete="current-password"
          required
        />
      </p>
      <p v-if="error" role="alert">{{ error }}</p>
      <button type="submit" :disabled="loading">{{ loading ? 'Entrando...' : 'Entrar' }}</button>
    </form>
    <p>Não tem uma conta? <RouterLink to="/registro">Cadastre-se</RouterLink>.</p>
  </section>
</template>