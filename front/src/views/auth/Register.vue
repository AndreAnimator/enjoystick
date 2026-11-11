<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import {
  User,
  Mail,
  Lock,
  UserPlus,
  AlertCircle,
  LogIn,
} from 'lucide-vue-next'

import logoImage from '@/assets/LOGO BANNER.png'

const auth = useAuthStore()
const cart = useCartStore()
const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

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
          'Não foi possível criar a conta.')
  } finally {
    loading.value = false
  }
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
          Criar conta
        </h1>

        <p class="text-center text-sm text-white">
          Faça parte da Enjoystick!
        </p>
      </div>

      <!-- Formulário -->
      <form
        @submit.prevent="submit"
        class="flex flex-col gap-4 p-6 sm:p-8"
      >

        <!-- Nome -->
        <div class="flex flex-col gap-2">
          <label
            for="name"
            class="font-bold text-[#C9007A]"
          >
            Nome
          </label>

          <div
            class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/60 px-3 transition-colors focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
          >
            <User class="h-5 w-5 shrink-0 text-[#C9007A]" />

            <input
              id="name"
              v-model.trim="form.name"
              type="text"
              autocomplete="name"
              placeholder="Seu nome completo"
              required
              maxlength="255"
              class="h-full w-full bg-transparent text-gray-800 outline-none placeholder:text-gray-400"
            />
          </div>
        </div>

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
              v-model.trim="form.email"
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
              v-model="form.password"
              type="password"
              autocomplete="new-password"
              placeholder="Mínimo de 8 caracteres"
              required
              minlength="8"
              class="h-full w-full bg-transparent text-gray-800 outline-none placeholder:text-gray-400"
            />
          </div>
        </div>

        <!-- Confirmação -->
        <div class="flex flex-col gap-2">
          <label
            for="confirmation"
            class="font-bold text-[#C9007A]"
          >
            Confirmar senha
          </label>

          <div
            class="flex h-12 items-center gap-3 rounded-lg border-2 border-[#C9007A]/60 px-3 transition-colors focus-within:border-[#C9007A] focus-within:ring-2 focus-within:ring-[#C9007A]/20"
          >
            <Lock class="h-5 w-5 shrink-0 text-[#C9007A]" />

            <input
              id="confirmation"
              v-model="form.password_confirmation"
              type="password"
              autocomplete="new-password"
              placeholder="Digite sua senha novamente"
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

        <!-- Cadastrar -->
        <button
          type="submit"
          :disabled="loading"
          class="mt-2 flex h-12 w-full items-center justify-center gap-2 rounded-full bg-[#C9007A] px-4 font-bold text-white transition-colors hover:bg-[#A80065] disabled:cursor-not-allowed disabled:opacity-60"
        >
          <UserPlus class="h-5 w-5" />

          {{ loading ? 'Criando conta...' : 'Cadastrar' }}
        </button>

        <!-- Login -->
        <div class="border-t border-gray-200 pt-5 text-center">
          <p class="text-sm text-gray-600">
            Já possui uma conta?
          </p>

          <RouterLink
            to="/login"
            class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-full border-2 border-[#C9007A] font-bold text-[#C9007A] transition-colors hover:bg-[#fce4f1]"
          >
            <LogIn class="h-5 w-5" />
            Entrar
          </RouterLink>
        </div>

      </form>
    </section>
  </main>
</template>