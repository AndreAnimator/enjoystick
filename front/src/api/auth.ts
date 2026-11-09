import api, { refreshCsrf } from './client'
import type { User } from '@/types/models'

export interface LoginPayload {
  email: string
  password: string
}
export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}
interface UserEnvelope {
  data: User
  warnings?: string[]
}

export async function getCsrfCookie(): Promise<void> {
  await refreshCsrf()
}
export async function login(payload: LoginPayload): Promise<UserEnvelope> {
  await refreshCsrf()
  const { data } = await api.post<UserEnvelope>('/api/login', payload)
  return data
}
export async function register(payload: RegisterPayload): Promise<UserEnvelope> {
  await refreshCsrf()
  const { data } = await api.post<UserEnvelope>('/api/register', payload)
  return data
}
export async function logout(): Promise<void> {
  await refreshCsrf()
  await api.post('/api/logout')
}
export async function fetchMe(): Promise<UserEnvelope> {
  const { data } = await api.get<UserEnvelope>('/api/me')
  return data
}
export async function updateProfile(payload: {
  name?: string
  email?: string
  password?: string
  password_confirmation?: string
}): Promise<User> {
  const { data } = await api.patch<{ data: User }>('/api/user', payload)
  return data.data
}
