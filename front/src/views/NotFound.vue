<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { Gamepad2, SearchX, Home, ArrowLeft } from 'lucide-vue-next'
</script>

<template>
  <main
    class="flex min-h-[65vh] items-center justify-center bg-[#D9D9D9] px-4 py-16 font-HelveticaRounded"
  >
    <section
      class="w-full max-w-xl rounded-3xl border border-pink-100 bg-white px-6 py-12 text-center shadow-sm sm:px-12"
    >
      <div class="relative mx-auto mb-6 flex h-28 w-28 items-center justify-center rounded-full bg-[#fce4f1]">
        <Gamepad2 :size="62" class="text-[#C9007A]" />
        <span class="absolute -right-1 -top-1 flex h-10 w-10 items-center justify-center rounded-full bg-[#C9007A] text-white">
          <SearchX :size="22" />
        </span>
      </div>

      <p class="mb-2 text-sm font-bold uppercase tracking-widest text-[#C9007A]">
        Ops! Game over?
      </p>

      <h1 class="mb-4 text-4xl font-bold text-gray-800 sm:text-5xl">
        404
      </h1>

      <h2 class="mb-3 text-xl font-bold text-gray-800">
        Página não encontrada
      </h2>

      <p class="mx-auto mb-8 max-w-sm leading-relaxed text-gray-500">
        Parece que você encontrou uma fase secreta... mas esta página não existe.
        Que tal voltar para a loja?
      </p>

      <RouterLink
        to="/"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C9007A] px-7 py-3.5 font-bold text-white transition hover:bg-[#A80065]"
      >
        <Home :size="19" />
        Voltar ao catálogo
      </RouterLink>

      <div class="mt-5">
        <button
          type="button"
          @click="$router.back()"
          class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 transition hover:text-[#C9007A]"
        >
          <ArrowLeft :size="16" />
          Voltar à página anterior
        </button>
      </div>
    </section>
  </main>
</template>