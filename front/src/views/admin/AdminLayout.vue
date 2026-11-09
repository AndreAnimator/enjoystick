<template>
  <section>
    <h1>Administração</h1>
    <nav aria-label="Administração">
      <ul>
        <li><RouterLink to="/admin/produtos">Produtos</RouterLink></li>
        <li><RouterLink to="/admin/categorias">Categorias</RouterLink></li>
        <li><RouterLink to="/admin/pedidos">Pedidos</RouterLink></li>
        <li><RouterLink to="/">Voltar à loja</RouterLink></li>
      </ul>
    </nav>
    <RouterView />
  </section>
</template>
