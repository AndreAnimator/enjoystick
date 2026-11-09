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
      const result = await authApi.fetchMe()
      user.value = result.data
      status.value = 'authenticated'
    } catch {
      user.value = null
      status.value = 'guest'
    }
  }
  async function login(payload: authApi.LoginPayload): Promise<User> {
    const result = await authApi.login(payload)
    user.value = result.data
    status.value = 'authenticated'
    return result.data
  }
  async function register(payload: authApi.RegisterPayload): Promise<User> {
    const result = await authApi.register(payload)
    user.value = result.data
    status.value = 'authenticated'
    return result.data
  }
  async function update(payload: {
    name?: string
    email?: string
    password?: string
    password_confirmation?: string
  }): Promise<void> {
    user.value = await authApi.updateProfile(payload)
  }
  async function logout(): Promise<void> {
    await authApi.logout()
    user.value = null
    status.value = 'guest'
  }
  return { user, status, isAuthenticated, isAdmin, restoreSession, login, register, update, logout }
})
