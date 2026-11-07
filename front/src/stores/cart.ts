import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/api/client';

export const useCartStore = defineStore('cart', () => {
  const items = ref([]);
  const total = ref(0);
  const loading = ref(false);

  const itemCount = computed(() => items.value.reduce((sum, i) => sum + i.quantity, 0));

  async function fetchCart() {
    loading.value = true;
    try {
      const { data } = await api.get('/api/cart');
      items.value = data.items;
      total.value = data.total;
    } finally {
      loading.value = false;
    }
  }

  async function addItem(productId, quantity = 1) {
    const { data } = await api.post('/api/cart/items', { product_id: productId, quantity });
    items.value = data.items;
    total.value = data.total;
    return data;
  }

  async function updateQuantity(itemId, quantity) {
    const { data } = await api.patch(`/api/cart/items/${itemId}`, { quantity });
    items.value = data.items;
    total.value = data.total;
  }

  async function removeItem(itemId) {
    const { data } = await api.delete(`/api/cart/items/${itemId}`);
    items.value = data.items;
    total.value = data.total;
  }

  return { items, total, loading, itemCount, fetchCart, addItem, updateQuantity, removeItem };
});