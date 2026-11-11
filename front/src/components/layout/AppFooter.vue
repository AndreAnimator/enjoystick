<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { Gamepad2, ArrowUpRight } from 'lucide-vue-next'
</script>

<template>
  <footer class="bg-[#C9007A] font-HelveticaRounded text-white">
    <div class="mx-auto max-w-7xl px-5 pb-9 pt-10 sm:px-8">
      <!-- Logo -->
      <div class="mb-10 flex justify-center">
        <RouterLink to="/" aria-label="Enjoystick - Página inicial">
          <img
            src="@/assets/LOGO BANNER.png"
            alt="Enjoystick"
            class="h-auto max-h-20 max-w-[230px] object-contain"
          />
        </RouterLink>
      </div>

      <!-- Informações -->
      <div class="grid grid-cols-1 gap-9 md:grid-cols-3 md:gap-0">
        <!-- Sobre -->
        <section class="px-4 text-center md:border-r md:border-[#A80065] md:px-8">
          <h2 class="mb-5 text-xl font-extrabold uppercase tracking-wide text-white">
            Sobre a Enjoystick
          </h2>

          <p class="mx-auto mb-8 max-w-sm leading-relaxed text-pink-100">
            A Enjoystick é o lugar que permite que todos possam amar os games.
            Nosso objetivo é reunir o máximo de público possível disponibilizando alternativas acessíveis e controles customizados para que você deixe o seu dia a dia ainda mais divertido.
          </p>

          <RouterLink
            to="/"
            class="inline-flex min-w-48 items-center justify-center gap-2 rounded-xl bg-[#fce4f1] px-6 py-3 font-extrabold text-[#A80065] transition hover:bg-white"
          >
            CONHEÇA A LOJA
            <ArrowUpRight :size="17" />
          </RouterLink>
        </section>

        <!-- Ajuda -->
        <section class="px-4 text-center md:border-r md:border-[#A80065] md:px-8">
          <h2 class="mb-5 text-xl font-extrabold uppercase tracking-wide text-white">
            Ajuda
          </h2>

          <p class="mx-auto mb-8 max-w-sm leading-relaxed text-pink-100">
            Precisa de ajuda com seus pedidos?
            Acesse sua área de pedidos para acompanhar suas compras.
          </p>

          <RouterLink
            to="/meus-pedidos"
            class="inline-flex min-w-48 items-center justify-center gap-2 rounded-xl bg-[#fce4f1] px-6 py-3 font-extrabold text-[#A80065] transition hover:bg-white"
          >
            MEUS PEDIDOS
            <ArrowUpRight :size="17" />
          </RouterLink>
        </section>

        <!-- Loja -->
        <section class="px-4 text-center md:px-8">
          <h2 class="mb-5 text-xl font-extrabold uppercase tracking-wide text-white">
            Explore
          </h2>

          <p class="mx-auto mb-8 max-w-sm leading-relaxed text-pink-100">
            Encontre produtos para sua necessidade, descubra novidades
            e escolha seus próximos favoritos!
          </p>

          <div class="flex justify-center">
            <RouterLink
              to="/"
              class="flex min-w-36 flex-col items-center justify-center gap-2 rounded-xl border border-pink-200 bg-[#fce4f1] px-5 py-5 transition hover:border-white hover:bg-white"
            >
              <Gamepad2 :size="32" class="text-[#C9007A]" />
              <span class="text-sm font-extrabold text-[#A80065]">
                CATÁLOGO
              </span>
              <span class="text-xs text-gray-600">
                Nossos produtos
              </span>
            </RouterLink>
          </div>
        </section>
      </div>
    </div>

    <div class="border-t border-pink-200 bg-[#fce4f1] px-4 py-5 text-center">
      <p class="text-sm font-semibold text-gray-700">
        {{ new Date().getFullYear() }} Enjoystick.
      </p>

      <div class="mt-2 flex justify-center gap-5 text-xs text-[#A80065]">
        <RouterLink
          to="/"
          class="underline transition hover:text-[#C9007A]"
        >
          
        </RouterLink>

        <RouterLink
          to="/"
          class="underline transition hover:text-[#C9007A]"
        >
          
        </RouterLink>
      </div>
    </div>
  </footer>
</template>