import axios, { type AxiosInstance, type InternalAxiosRequestConfig } from 'axios'

const CART_TOKEN_KEY = 'enjoystick_cart_token'
const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true,
  withXSRFToken: true,
  headers: { Accept: 'application/json' },
})

api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = localStorage.getItem(CART_TOKEN_KEY)
  if (token) config.headers.set('X-Cart-Token', token)
  return config
})

api.interceptors.response.use((response) => {
  const token = response.headers['x-cart-token'] as string | undefined
  if (token) localStorage.setItem(CART_TOKEN_KEY, token)
  return response
})

export async function refreshCsrf(): Promise<void> {
  await api.get('/sanctum/csrf-cookie')
}

export default api