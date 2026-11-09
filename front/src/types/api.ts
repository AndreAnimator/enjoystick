export interface Resource<T> {
  data: T
}
export interface ResourceList<T> {
  data: T[]
  links?: Record<string, unknown>
  meta?: { current_page?: number; last_page?: number; total?: number; per_page?: number }
}
export interface Paginated<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}
export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
