import api from './client'
import type { Order, OrderStatus } from '@/types/models'
import type { Resource, ResourceList } from '@/types/api'
export type ShippingPayload = Pick<
  Order,
  | 'shipping_recipient_name'
  | 'shipping_postal_code'
  | 'shipping_street'
  | 'shipping_number'
  | 'shipping_complement'
  | 'shipping_neighborhood'
  | 'shipping_city'
  | 'shipping_state'
  | 'shipping_country'
>
export async function listOrders(): Promise<Order[]> {
  const { data } = await api.get<ResourceList<Order>>('/api/orders')
  return data.data
}
export async function getOrder(id: number): Promise<Order> {
  const { data } = await api.get<Resource<Order>>(`/api/orders/${id}`)
  return data.data
}
export async function createOrder(payload: ShippingPayload & { user_id: number }): Promise<Order> {
  const { data } = await api.post<Resource<Order>>('/api/orders', payload)
  return data.data
}
export async function payOrder(id: number): Promise<Order> {
  const { data } = await api.post<Resource<Order>>(`/api/orders/${id}/pay`, { status: 'paid' })
  return data.data
}
export async function cancelOrder(id: number): Promise<Order> {
  const { data } = await api.post<Resource<Order>>(`/api/orders/${id}/cancel`)
  return data.data
}
export async function listAllOrders(status?: OrderStatus): Promise<Order[]> {
  const first = await api.get<ResourceList<Order>>('/api/admin/orders')
  const all = [...first.data.data]
  const lastPage = first.data.meta?.last_page ?? 1
  for (let page = 2; page <= lastPage; page++) {
    const { data } = await api.get<ResourceList<Order>>('/api/admin/orders', { params: { page } })
    all.push(...data.data)
  }
  return status ? all.filter((order) => order.status === status) : all
}
export async function updateOrderStatus(id: number, status: OrderStatus): Promise<Order> {
  const { data } = await api.patch<Resource<Order>>(`/api/admin/orders/${id}/status`, { status })
  return data.data
}
