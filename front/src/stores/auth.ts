import { defineStore } from 'pinia';
import { ref } from 'vue';
import * as authApi from '@/api/auth';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const status = ref('unknown'); // 'unknown' | 'authenticated' | 'guest'

  async function restoreSession() {
    try {
      const data = await authApi.fetchMe();
      user.value = data.user;
      status.value = 'authenticated';
    } catch {
      user.value = null;
      status.value = 'guest';
    }
  }

  async function login(credentials) {
    const data = await authApi.login(credentials);
    user.value = data.user;
    status.value = 'authenticated';
    return data;
  }

  async function register(payload) {
    const data = await authApi.register(payload);
    user.value = data.user;
    status.value = 'authenticated';
    return data;
  }

  async function logout() {
    await authApi.logout();
    user.value = null;
    status.value = 'guest';
  }

  return { user, status, restoreSession, login, register, logout };
});