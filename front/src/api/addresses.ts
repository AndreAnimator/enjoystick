import api from './client'
import type { Address } from '@/types/models'
import type { Resource, ResourceList } from '@/types/api'
export type AddressPayload = Omit<Address, 'id' | 'user_id'>
export async function listAddresses(): Promise<Address[]> {
  const { data } = await api.get<ResourceList<Address>>('/api/addresses')
  return data.data
}
export async function createAddress(payload: AddressPayload): Promise<Address> {
  const { data } = await api.post<Resource<Address>>('/api/addresses', payload)
  return data.data
}
export async function updateAddress(
  id: number,
  payload: Partial<AddressPayload>,
): Promise<Address> {
  const { data } = await api.patch<Resource<Address>>(`/api/addresses/${id}`, payload)
  return data.data
}
export async function deleteAddress(id: number): Promise<void> {
  await api.delete(`/api/addresses/${id}`)
}
