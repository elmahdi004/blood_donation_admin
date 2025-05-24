import axios, { type AxiosInstance } from "axios";
// import type { AxiosInstance } from "axios";

// declare module "#app" {
//   interface NuxtApp {
//     $api: AxiosInstance;
//   }
// }

// declare module "@vue/runtime-core" {
//   interface ComponentCustomProperties {
//     $api: AxiosInstance;
//   }
// }

// export default defineNuxtPlugin((nuxtApp) => {
//   const config = useRuntimeConfig();

const api: AxiosInstance = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
    "X-Requested-With": "XMLHttpRequest",
  },
  withCredentials: false,
});

// Add request interceptor to add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Add response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error("API Error:", error.response || error);
    if (error.response?.status === 401) {
      localStorage.removeItem("token");
      navigateTo("/login");
    }
    return Promise.reject(error);
  }
);

export default api;

