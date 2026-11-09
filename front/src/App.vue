<script setup lang="ts">
import { onMounted } from 'vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const auth = useAuthStore()
const cart = useCartStore()

onMounted(async () => {
  await auth.restoreSession()
  try { await cart.fetchCart() } catch { }
})
</script>

<template>
  <AppHeader />
  <main>
    <RouterView />
  </main>
</template>