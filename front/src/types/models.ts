export type Role = 'cliente' | 'admin'

export type OrderStatus =
  | 'aguardando_pagamento'
  | 'pago'
  | 'enviado'
  | 'entregue'
  | 'cancelado'

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
  description: string
  price: string          // decimal no backend → string no JSON
  stock: number
  active: boolean
  category_id: number
  category?: Category
  image_url?: string | null
}

export interface CartItem {
  id: number
  product: Product
  quantity: number
  subtotal: string       // calculado no servidor
  available: boolean     // produto ativo e com estoque
}

export interface Cart {
  items: CartItem[]
  total: string
}

export interface Address {
  id: number
  street: string
  number: string
  complement?: string | null
  district: string
  city: string
  state: string
  zipcode: string
}

export interface OrderItem {
  id: number
  product_name: string
  unit_price: string
  quantity: number
  subtotal: string
}

export interface Order {
  id: number
  status: OrderStatus
  total: string
  shipping: string
  created_at: string
  paid_at?: string | null
  address: Address
  items: OrderItem[]
}