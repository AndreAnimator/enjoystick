export type Role = 'client' | 'admin'
export type OrderStatus = 'waiting' | 'paid' | 'sent' | 'arrived' | 'canceled'

export interface User {
  id: number
  name: string
  email: string
  role: Role
}
export interface Category {
  id: number
  name: string
}
export interface Product {
  id: number
  name: string
  description: string | null
  price: string
  stock: number
  active: boolean | number
  category_id?: number
  category?: Category
  image?: string | null
  created_at?: string
  updated_at?: string
}
export interface CartLine {
  id: number
  product_id: number
  name: string
  price: string
  quantity: number
  subtotal: string
  available: boolean
  product?: Product
}
export interface Cart {
  items: CartLine[]
  total: string
  token?: string
  hasUnavailableItems?: boolean
}
export interface Address {
  id: number
  user_id: number
  recipient_name: string
  postal_code: string
  street: string
  number: string
  complement: string | null
  neighborhood: string
  city: string
  state: string
  country: string
}
export interface OrderItem {
  order_id: number
  product_id: number
  product_name: string
  unit_price: string
  quantity: number
}
export interface Order {
  id: number
  user_id: number
  status: OrderStatus
  subtotal: string
  shipping_cost: string
  total: string
  shipping_recipient_name: string
  shipping_postal_code: string
  shipping_street: string
  shipping_number: string
  shipping_complement: string | null
  shipping_neighborhood: string
  shipping_city: string
  shipping_state: string
  shipping_country: string
  paid_at: string | null
  sent_at: string | null
  arrived_at: string | null
  cancelled_at: string | null
  created_at: string
  items: OrderItem[]
}
