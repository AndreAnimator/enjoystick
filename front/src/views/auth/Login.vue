<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { Mail, Lock, LogIn, AlertCircle, UserPlus } from 'lucide-vue-next'

import logoImage from '@/assets/LOGO BANNER.png'

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
    await auth.login({
      email: email.value,
      password: password.value,
    })

    await cart.fetchCart()

    await router.replace(
      typeof route.query.redirect === 'string'
        ? route.query.redirect
        : '/',
    )
  } catch (e: unknown) {
    error.value = getError(e)
  } finally {
    loading.value = false
  }
}

function getError(e: unknown): string {
  const err = e as {
    response?: {
      data?: {
        message?: string
        errors?: Record<string, string[]>
      }
    }
  }

  return err.response?.data?.errors
    ? Object.values(err.response.data.errors).flat().join(' ')
    : (err.response?.data?.message ??
        'Não foi possível entrar. Verifique suas credenciais.')
}
</script>

<template>
  <main
    class="flex min-h-[calc(100vh-88px)] w-full items-center justify-center bg-[#D9D9D9] px-4 py-10 font-HelveticaRounded"
  >
    <section
      class="w-full max-w-md overflow-hidden rounded-2xl border-[3px] border-[#C9007A] bg-white shadow-xl"
    >
      <!-- Cabeçalho -->
      <div
        class="flex flex-col items-center gap-3 bg-[#C9007A] px-6 py-7"
      >
        <RouterLink to="/">
          <img
            :src="logoImage"
            alt="Enjoystick"
            class="h-20 max-w-48 object-contain"
          />
        </RouterLink>

        <h1 class="text-3xl font-bold text-white">
          Bem-vindo de volta!
        </h1>

        <p class="text-center text-sm text-white">
          Entre na sua conta para continuar comprando.
        </p>
      </div>

      <!-- Formulário -->
      <form
        @submit.prevent="submit"
        class="flex flex-col gap-5 p-6 sm:p-8"
      >
        <!-- E-mail -->
        <div class="flex flex-col gap-2">
          <label
            for="email"
            class="font-bold text-[#C9007A]"
          >
            E-mail
          </label>

          <div
            class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/60 px-3 transition-colors focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
          >
            <Mail class="h-5 w-5 shrink-0 text-[#C9007A]" />

            <input
              id="email"
              v-model.trim="email"
              type="email"
              autocomplete="email"
              placeholder="seuemail@exemplo.com"
              required
              class="h-full w-full bg-transparent text-gray-800 outline-none placeholder:text-gray-400"
            />
          </div>
        </div>

        <!-- Senha -->
        <div class="flex flex-col gap-2">
          <label
            for="password"
            class="font-bold text-[#C9007A]"
          >
            Senha
          </label>

          <div
            class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/60 px-3 transition-colors focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
          >
            <Lock class="h-5 w-5 shrink-0 text-[#C9007A]" />

            <input
              id="password"
              v-model="password"
              type="password"
              autocomplete="current-password"
              placeholder="Sua senha"
              required
              class="h-full w-full bg-transparent text-gray-800 outline-none placeholder:text-gray-400"
            />
          </div>
        </div>

        <!-- Erro -->
        <div
          v-if="error"
          role="alert"
          class="flex items-start gap-2 rounded-lg border border-red-400 bg-red-50 p-3 text-sm font-semibold text-red-600"
        >
          <AlertCircle class="mt-0.5 h-5 w-5 shrink-0" />
          {{ error }}
        </div>

        <!-- Entrar -->
        <button
          type="submit"
          :disabled="loading"
          class="flex h-12 w-full items-center justify-center gap-2 rounded-full bg-[#C9007A] px-4 font-bold text-white transition-colors hover:bg-[#A80065] disabled:cursor-not-allowed disabled:opacity-60"
        >
          <LogIn class="h-5 w-5" />

          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>

        <!-- Cadastro -->
        <div class="border-t border-gray-200 pt-5 text-center">
          <p class="text-sm text-gray-600">
            Ainda não tem uma conta?
          </p>

          <RouterLink
            to="/registro"
            class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-full border-2 border-[#C9007A] font-bold text-[#C9007A] transition-colors hover:bg-[#fce4f1]"
          >
            <UserPlus class="h-5 w-5" />
            Criar minha conta
          </RouterLink>
        </div>

      </form>
    </section>
  </main>
</template>