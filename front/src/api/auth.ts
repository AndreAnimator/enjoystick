import api from './client'
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

export interface AuthResponse {
  user: User
}

export async function getCsrfCookie(): Promise<void> {
  await api.get('/sanctum/csrf-cookie')
}

export async function login(payload: LoginPayload): Promise<AuthResponse> {
  await getCsrfCookie()
  const { data } = await api.post<AuthResponse>('/api/login', payload)
  return data
}

export async function register(payload: RegisterPayload): Promise<AuthResponse> {
  await getCsrfCookie()
  const { data } = await api.post<AuthResponse>('/api/register', payload)
  return data
}

export async function logout(): Promise<void> {
  await api.post('/api/logout')
}

export async function fetchMe(): Promise<AuthResponse> {
  const { data } = await api.get<AuthResponse>('/api/me')
  return data
}