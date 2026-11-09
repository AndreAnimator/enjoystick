import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as authApi from '@/api/auth'
import type { User } from '@/types/models'

type AuthStatus = 'unknown' | 'authenticated' | 'guest'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const status = ref<AuthStatus>('unknown')

  const isAuthenticated = computed(() => status.value === 'authenticated')
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function restoreSession(): Promise<void> {
    try {
      const data = await authApi.fetchMe()
      user.value = data.user
      status.value = 'authenticated'
    } catch {
      user.value = null
      status.value = 'guest'
    }
  }

  async function login(payload: authApi.LoginPayload): Promise<User> {
    const data = await authApi.login(payload)
    user.value = data.user
    status.value = 'authenticated'
    return data.user
  }

  async function register(payload: authApi.RegisterPayload): Promise<User> {
    const data = await authApi.register(payload)
    user.value = data.user
    status.value = 'authenticated'
    return data.user
  }

  async function logout(): Promise<void> {
    await authApi.logout()
    user.value = null
    status.value = 'guest'
  }

  return {
    user,
    status,
    isAuthenticated,
    isAdmin,
    restoreSession,
    login,
    register,
    logout,
  }
})