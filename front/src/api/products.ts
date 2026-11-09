import api from './client'
import type { Category, Product } from '@/types/models'
import type { Paginated } from '@/types/api'

export interface ProductQuery {
  search?: string
  category_id?: number
  sort?: 'price_asc' | 'price_desc' | 'recent'
  page?: number
  per_page?: number
}

export async function listProducts(query: ProductQuery = {}): Promise<Paginated<Product>> {
  const { data } = await api.get<Paginated<Product>>('/api/products', { params: query })
  return data
}

export async function getProduct(id: number | string): Promise<Product> {
  const { data } = await api.get<Product>(`/api/products/${id}`)
  return data
}

export async function listCategories(): Promise<Category[]> {
  const { data } = await api.get<Category[]>('/api/categories')
  return data
}