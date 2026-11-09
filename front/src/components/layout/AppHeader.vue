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
  <header>
    <nav>
      <RouterLink to="/">Catálogo</RouterLink>
      <RouterLink to="/carrinho">
        Carrinho ({{ cart.itemCount }})
      </RouterLink>

      <template v-if="auth.isAuthenticated">
        <RouterLink to="/meus-pedidos">Meus Pedidos</RouterLink>
        <RouterLink to="/perfil">Perfil</RouterLink>
        <RouterLink v-if="auth.isAdmin" to="/admin">Painel Admin</RouterLink>
        <button type="button" @click="handleLogout">Sair</button>
      </template>

      <template v-else>
        <RouterLink to="/login">Entrar</RouterLink>
        <RouterLink to="/registro">Registrar</RouterLink>
      </template>
    </nav>
  </header>
</template>