<template>
  <main class="min-h-screen bg-[#D9D9D9] px-4 py-8 font-HelveticaRounded sm:px-6">
    <div class="mx-auto max-w-7xl">
      <header class="mb-8 rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="mb-1 text-sm font-bold uppercase tracking-wider text-[#C9007A]">
              Enjoystick
            </p>
            <h1 class="text-3xl font-bold text-gray-800">Administração</h1>
            <p class="mt-1 text-sm text-gray-500">
              Gerencie produtos, categorias e pedidos da loja.
            </p>
          </div>

          <RouterLink
            to="/"
            class="inline-flex items-center justify-center rounded-xl border-2 border-[#C9007A] px-5 py-3 font-bold text-[#C9007A] transition hover:bg-[#fce4f1]"
          >
            Voltar à loja
          </RouterLink>
        </div>
      </header>

      <nav
        aria-label="Administração"
        class="mb-6 rounded-2xl bg-white p-3 shadow-sm"
      >
        <ul class="flex flex-wrap gap-2">
          <li>
            <RouterLink
              to="/admin/produtos"
              class="block rounded-xl px-5 py-3 font-bold text-gray-600 transition hover:bg-[#fce4f1] hover:text-[#C9007A]"
              active-class="!bg-[#C9007A] !text-white"
            >
              Produtos
            </RouterLink>
          </li>

          <li>
            <RouterLink
              to="/admin/categorias"
              class="block rounded-xl px-5 py-3 font-bold text-gray-600 transition hover:bg-[#fce4f1] hover:text-[#C9007A]"
              active-class="!bg-[#C9007A] !text-white"
            >
              Categorias
            </RouterLink>
          </li>

          <li>
            <RouterLink
              to="/admin/pedidos"
              class="block rounded-xl px-5 py-3 font-bold text-gray-600 transition hover:bg-[#fce4f1] hover:text-[#C9007A]"
              active-class="!bg-[#C9007A] !text-white"
            >
              Pedidos
            </RouterLink>
          </li>
        </ul>
      </nav>

      <section class="rounded-2xl bg-white p-5 shadow-sm sm:p-8">
        <RouterView />
      </section>
    </div>
  </main>
</template>