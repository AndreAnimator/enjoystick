<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { Menu, X } from 'lucide-vue-next'

const auth = useAuthStore()
const cart = useCartStore()
const router = useRouter()

const menuOpen = ref(false)

function closeMenu() {
  menuOpen.value = false
}

async function handleLogout(): Promise<void> {
  closeMenu()
  await auth.logout()
  await cart.clear()
  router.push({ name: 'login' })
}
</script>

<template>
  <header
    class="relative z-50 w-full bg-[#D9D9D9] font-HelveticaRounded font-semibold"
  >
    <nav class="flex min-h-[76px] w-full items-center md:h-22 md:min-h-0 md:p-0">

      <!-- Logo -->
      <RouterLink
        to="/"
        @click="closeMenu"
        class="flex h-full flex-1 items-center justify-start border-b-5 border-[#C9007A] px-4 md:w-1/4 md:flex-none md:justify-center md:px-0"
      >
        <img
          src="./../../assets/LOGO.png"
          alt="Enjoystick"
          class="max-h-16 max-w-36 object-contain md:max-h-20"
        />
      </RouterLink>

      <!-- Botão Hambúrguer: somente mobile -->
      <button
        type="button"
        @click="menuOpen = !menuOpen"
        :aria-expanded="menuOpen"
        aria-label="Abrir ou fechar menu"
        class="mr-4 flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#C9007A] text-white transition hover:bg-[#A80065] md:hidden"
      >
        <X v-if="menuOpen" :size="30" />
        <Menu v-else :size="30" />
      </button>

      <!-- Navegação Desktop -->
      <div
        class="hidden h-full flex-1 items-stretch gap-1 rounded-tl-[18px] border-5 border-[#C9007A] border-b-0 bg-[#C9007A] md:flex"
      >
        <RouterLink
          to="/"
          exact-active-class="!bg-[#D9D9D9] !text-[#C9007A] hover:!border-b-0"
          class="flex flex-1 items-center justify-center gap-1 whitespace-nowrap rounded-t-xl bg-[#C9007A] px-2 text-white transition hover:border-b-5 hover:bg-[#f1abd5] hover:text-[#C9007A]"
        >
          Catálogo
        </RouterLink>

        <RouterLink
          to="/carrinho"
          exact-active-class="!bg-[#D9D9D9] !text-[#C9007A] hover:!border-b-0"
          class="flex flex-1 items-center justify-center gap-1 whitespace-nowrap rounded-t-xl bg-[#C9007A] px-2 text-white transition hover:border-b-5 hover:bg-[#f1abd5] hover:text-[#C9007A]"
        >
          Carrinho ({{ cart.itemCount }})
        </RouterLink>

        <template v-if="auth.isAuthenticated">
          <RouterLink
            to="/meus-pedidos"
            exact-active-class="!bg-[#D9D9D9] !text-[#C9007A] hover:!border-b-0"
            class="flex flex-1 items-center justify-center gap-1 whitespace-nowrap rounded-t-xl bg-[#C9007A] px-2 text-white transition hover:border-b-5 hover:bg-[#f1abd5] hover:text-[#C9007A]"
          >
            Meus Pedidos
          </RouterLink>

          <RouterLink
            to="/perfil"
            exact-active-class="!bg-[#D9D9D9] !text-[#C9007A] hover:!border-b-0"
            class="flex flex-1 items-center justify-center gap-1 whitespace-nowrap rounded-t-xl bg-[#C9007A] px-2 text-white transition hover:border-b-5 hover:bg-[#f1abd5] hover:text-[#C9007A]"
          >
            Perfil
          </RouterLink>

          <RouterLink
            v-if="auth.isAdmin"
            to="/admin"
            exact-active-class="!bg-[#D9D9D9] !text-[#C9007A] hover:!border-b-0"
            class="flex flex-1 items-center justify-center gap-1 whitespace-nowrap rounded-t-xl bg-[#C9007A] px-2 text-white transition hover:border-b-5 hover:bg-[#f1abd5] hover:text-[#C9007A]"
          >
            Painel Admin
          </RouterLink>

          <button
            type="button"
            @click="handleLogout"
            class="flex flex-1 items-center justify-center rounded-t-xl bg-[#C9007A] px-2 text-white transition hover:border-b-5 hover:bg-[#f1abd5] hover:text-[#C9007A]"
          >
            Sair
          </button>
        </template>

        <template v-else>
          <RouterLink
            to="/login"
            exact-active-class="!bg-[#D9D9D9] !text-[#C9007A] hover:!border-b-0"
            class="flex flex-1 items-center justify-center rounded-t-xl bg-[#C9007A] px-2 text-white transition hover:border-b-5 hover:bg-[#f1abd5] hover:text-[#C9007A]"
          >
            Entrar
          </RouterLink>

          <RouterLink
            to="/registro"
            exact-active-class="!bg-[#D9D9D9] !text-[#C9007A] hover:!border-b-0"
            class="flex flex-1 items-center justify-center rounded-t-xl bg-[#C9007A] px-2 text-white transition hover:border-b-5 hover:bg-[#f1abd5] hover:text-[#C9007A]"
          >
            Registrar
          </RouterLink>
        </template>
      </div>
    </nav>

    <!-- Menu Mobile -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="-translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="-translate-y-2 opacity-0"
    >
      <div
        v-if="menuOpen"
        class="absolute left-0 top-full w-full border-t-2 border-[#f1abd5] bg-[#C9007A] px-4 pb-5 pt-3 shadow-xl md:hidden"
      >
        <div class="flex flex-col gap-1">

          <RouterLink
            to="/"
            @click="closeMenu"
            exact-active-class="!bg-[#D9D9D9] !text-[#C9007A]"
            class="rounded-xl px-5 py-3 text-lg text-white transition hover:bg-[#f1abd5] hover:text-[#C9007A]"
          >
            Catálogo
          </RouterLink>

          <RouterLink
            to="/carrinho"
            @click="closeMenu"
            exact-active-class="!bg-[#D9D9D9] !text-[#C9007A]"
            class="rounded-xl px-5 py-3 text-lg text-white transition hover:bg-[#f1abd5] hover:text-[#C9007A]"
          >
            Carrinho ({{ cart.itemCount }})
          </RouterLink>

          <template v-if="auth.isAuthenticated">
            <div class="my-2 border-t border-pink-300/50"></div>

            <RouterLink
              to="/meus-pedidos"
              @click="closeMenu"
              exact-active-class="!bg-[#D9D9D9] !text-[#C9007A]"
              class="rounded-xl px-5 py-3 text-lg text-white transition hover:bg-[#f1abd5] hover:text-[#C9007A]"
            >
              Meus Pedidos
            </RouterLink>

            <RouterLink
              to="/perfil"
              @click="closeMenu"
              exact-active-class="!bg-[#D9D9D9] !text-[#C9007A]"
              class="rounded-xl px-5 py-3 text-lg text-white transition hover:bg-[#f1abd5] hover:text-[#C9007A]"
            >
              Perfil
            </RouterLink>

            <RouterLink
              v-if="auth.isAdmin"
              to="/admin"
              @click="closeMenu"
              exact-active-class="!bg-[#D9D9D9] !text-[#C9007A]"
              class="rounded-xl px-5 py-3 text-lg text-white transition hover:bg-[#f1abd5] hover:text-[#C9007A]"
            >
              Painel Admin
            </RouterLink>

            <div class="my-2 border-t border-pink-300/50"></div>

            <button
              type="button"
              @click="handleLogout"
              class="rounded-xl px-5 py-3 text-left text-lg text-white transition hover:bg-[#f1abd5] hover:text-[#C9007A]"
            >
              Sair da conta
            </button>
          </template>

          <template v-else>
            <div class="my-2 border-t border-pink-300/50"></div>

            <RouterLink
              to="/login"
              @click="closeMenu"
              exact-active-class="!bg-[#D9D9D9] !text-[#C9007A]"
              class="rounded-xl px-5 py-3 text-lg text-white transition hover:bg-[#f1abd5] hover:text-[#C9007A]"
            >
              Entrar
            </RouterLink>

            <RouterLink
              to="/registro"
              @click="closeMenu"
              exact-active-class="!bg-[#D9D9D9] !text-[#C9007A]"
              class="rounded-xl px-5 py-3 text-lg text-white transition hover:bg-[#f1abd5] hover:text-[#C9007A]"
            >
              Registrar
            </RouterLink>
          </template>

        </div>
      </div>
    </Transition>
  </header>
</template>