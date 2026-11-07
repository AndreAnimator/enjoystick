<template>
  <header>
    <nav>
      <RouterLink to="/">Catálogo</RouterLink>
      <RouterLink to="/carrinho">
        Carrinho ({{ cart.itemCount }})
      </RouterLink>
      <template v-if="auth.status === 'authenticated'">
        <RouterLink to="/meus-pedidos">Meus Pedidos</RouterLink>
        <RouterLink to="/perfil">Perfil</RouterLink>
        <RouterLink v-if="auth.user?.role === 'admin'" to="/admin">Painel Admin</RouterLink>
        <button @click="handleLogout">Sair</button>
      </template>
      <template v-else>
        <RouterLink to="/login">Entrar</RouterLink>
        <RouterLink to="/registro">Registrar</RouterLink>
      </template>
    </nav>
  </header>
</template>