// src/api/auth.js
import api from './client';

export async function getCsrfCookie() {
  await api.get('/sanctum/csrf-cookie');
}

export async function login(credentials) {
  await getCsrfCookie();
  const { data } = await api.post('/api/login', credentials);
  return data;
}

export async function register(payload) {
  await getCsrfCookie();
  const { data } = await api.post('/api/register', payload);
  return data;
}

export async function logout() {
  await api.post('/api/logout');
}

export async function fetchMe() {
  const { data } = await api.get('/api/me');
  return data;
}