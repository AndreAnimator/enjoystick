import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000',
  withCredentials: true,        // envia/recebe cookies
  withXSRFToken: true,          // envia o header X-XSRF-TOKEN automaticamente
});

export default api;