<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const auth = useAuthStore()
const cart = useCartStore()
const router = useRouter()

async function handleLogout(): Promise<void> {
  await auth.logout()
  await cart.clear()
  router.push({ name: 'login' })
}
</script>

<template>
  <header class="h-22 bg-[#D9D9D9] text-3xl w-full font-HelveticaRounded font-semibold">
    <nav class="flex h-full w-full p-0 items-center">
      <RouterLink
        to="/"
        class="flex h-full w-1/4 shrink-0 items-center justify-center"
      >
        <img
          src="./../../assets/LOGO.png"
          alt="Enjoystick"
          class="max-h-20 max-w-36 object-contain"
        />
      </RouterLink>
      <div class="flex h-full flex-1 items-stretch gap-1 bg-[#C9007A] rounded-tl-[18px] border-b-0 border-5 border-[#C9007A]">
        <RouterLink to="/" text-3xl
          class="flex flex-1 items-center justify-center gap-1 rounded-t-xl bg-[#D9D9D9] px-2 text-[#C9007A] hover:bg-[#f1abd5] whitespace-nowrap"
        >Catálogo</RouterLink>
        <RouterLink to="/carrinho"
          class="flex flex-1 items-center justify-center gap-1 rounded-t-xl bg-[#C9007A] px-2 text-white whitespace-nowrap"
        >
          Carrinho ({{ cart.itemCount }})
        </RouterLink>

        <template v-if="auth.isAuthenticated">
          <RouterLink to="/meus-pedidos"
            class="flex flex-1 items-center justify-center gap-1 rounded-t-xl bg-[#C9007A] px-2 text-white whitespace-nowrap"
          >Meus Pedidos</RouterLink>
          <RouterLink to="/perfil"
          class="flex flex-1 items-center justify-center gap-1 rounded-t-xl bg-[#C9007A] px-2 text-white whitespace-nowrap"
          >Perfil</RouterLink>
          <RouterLink v-if="auth.isAdmin" to="/admin"
            class="flex flex-1 items-center justify-center gap-1 rounded-t-xl bg-[#C9007A] px-2 text-white whitespace-nowrap"
          >Painel Admin</RouterLink>
          <button type="button" @click="handleLogout"
            class="flex flex-1 items-center justify-center rounded-t-xl bg-[#C9007A] px-2 text-white"
          >Sair</button>
        </template>

        <template v-else>
          <RouterLink to="/login"
          class="flex flex-1 items-center justify-center rounded-t-xl bg-[#C9007A] px-2 text-white"
          >Entrar</RouterLink>
          <RouterLink to="/registro"
          class="flex flex-1 items-center justify-center rounded-t-xl bg-[#C9007A] px-2 text-white"
          >Registrar</RouterLink>
        </template>
      </div>
    </nav>
  </header>
</template>