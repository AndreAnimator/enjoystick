import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
  { path: '/', name: 'catalog', component: () => import('@/views/catalog/ProductList.vue') },
  { path: '/produto/:id', name: 'product', component: () => import('@/views/catalog/ProductDetail.vue') },
  { path: '/carrinho', name: 'cart', component: () => import('@/views/cart/CartView.vue') },
  { path: '/login', name: 'login', component: () => import('@/views/auth/Login.vue') },
  { path: '/registro', name: 'register', component: () => import('@/views/auth/Register.vue') },
  {
    path: '/checkout',
    name: 'checkout',
    component: () => import('@/views/checkout/CheckoutView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/meus-pedidos',
    name: 'orders',
    component: () => import('@/views/orders/OrderList.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/pedido/:id',
    name: 'order-detail',
    component: () => import('@/views/orders/OrderDetail.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/perfil',
    name: 'profile',
    component: () => import('@/views/profile/ProfileView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin',
    component: () => import('@/views/admin/AdminLayout.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      { path: '', redirect: '/admin/produtos' },
      { path: 'produtos', name: 'admin-products', component: () => import('@/views/admin/ProductList.vue') },
      { path: 'produtos/novo', name: 'admin-product-new', component: () => import('@/views/admin/ProductForm.vue') },
      { path: 'produtos/:id/editar', name: 'admin-product-edit', component: () => import('@/views/admin/ProductForm.vue') },
      { path: 'categorias', name: 'admin-categories', component: () => import('@/views/admin/CategoryList.vue') },
      { path: 'pedidos', name: 'admin-orders', component: () => import('@/views/admin/OrderManagement.vue') },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to) => {
  const auth = useAuthStore();
  if (auth.status === 'unknown') await auth.restoreSession();

  if (to.meta.requiresAuth && auth.status !== 'authenticated') {
    return { name: 'login', query: { redirect: to.fullPath } };
  }
  if (to.meta.requiresAdmin && auth.user?.role !== 'admin') {
    return { name: 'catalog' };
  }
});

export default router
