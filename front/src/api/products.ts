import api from './client'
import type { Category, Product } from '@/types/models'
import type { Resource, ResourceList } from '@/types/api'

export interface ProductQuery {
  search?: string
  category_id?: number
  sort?: 'price_asc' | 'price_desc' | 'recent'
  page?: number
  per_page?: number
}
function queryParams(query: ProductQuery) {
  const sort =
    query.sort === 'price_asc'
      ? ['price', 'asc']
      : query.sort === 'price_desc'
        ? ['price', 'desc']
        : query.sort === 'recent'
          ? ['created_at', 'desc']
          : ['name', 'asc']
  return {
    q: query.search || undefined,
    category: query.category_id || undefined,
    column: sort[0],
    order: sort[1],
    page: query.page,
  }
}
export async function listProducts(
  query: ProductQuery = {},
): Promise<{ data: Product[]; current_page: number; last_page: number; total: number }> {
  const { data } = await api.get<ResourceList<Product>>('/api/products', {
    params: queryParams(query),
  })
  return {
    data: data.data,
    current_page: data.meta?.current_page ?? 1,
    last_page: data.meta?.last_page ?? 1,
    total: data.meta?.total ?? data.data.length,
  }
}
export async function listAdminProducts(query: ProductQuery = {}) {
  const params = queryParams(query)
  const first = await api.get<ResourceList<Product>>('/api/admin/products', { params })
  const products = [...first.data.data]
  const lastPage = first.data.meta?.last_page ?? 1
  for (let page = 2; page <= lastPage; page++) {
    const { data } = await api.get<ResourceList<Product>>('/api/admin/products', {
      params: { ...params, page },
    })
    products.push(...data.data)
  }
  return { data: products, current_page: 1, last_page: 1, total: products.length }
}
export async function getProduct(id: number | string): Promise<Product> {
  const { data } = await api.get<Resource<Product>>(`/api/products/${id}`)
  return data.data
}
export async function getAdminProduct(id: number | string): Promise<Product> {
  const { data } = await api.get<Resource<Product>>(`/api/admin/products/${id}`)
  return data.data
}
export async function listCategories(): Promise<Category[]> {
  const { data } = await api.get<ResourceList<Category>>('/api/categories')
  return data.data
}
export async function saveProduct(
  payload: Partial<Product> & { category_id: number; name: string; price: number | string },
  id?: number,
): Promise<Product> {
  const { data } = id
    ? await api.patch<Resource<Product>>(`/api/admin/products/${id}`, payload)
    : await api.post<Resource<Product>>('/api/admin/products', payload)
  return data.data
}
export async function deleteProduct(id: number): Promise<void> {
  await api.delete(`/api/admin/products/${id}`)
}
