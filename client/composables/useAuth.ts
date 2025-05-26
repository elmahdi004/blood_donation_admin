import { ref } from "vue";
// import { useNuxtApp } from "#app";
// import axios from "axios";
// import type { AxiosInstance } from "axios";
import api from "../plugins/axios";

// Create a new axios instance
// const api: AxiosInstance = axios.create({
//     baseURL: "http://127.0.0.1:8000/api",
//     headers: {
//       "Content-Type": "application/json",
//       Accept: "application/json",
//       "X-Requested-With": "XMLHttpRequest",
//     },
//     withCredentials: false,
//   });

// // Add request interceptor to add token to requests
// api.interceptors.request.use((config) => {
//   const token = localStorage.getItem("token");
//   if (token) {
//     config.headers.Authorization = `Bearer ${token}`;
//   }
//   return config;
// });

// // Add response interceptor to handle errors
// api.interceptors.response.use(
//   (response) => response,
//   (error) => {
//     if (error.response?.status === 401) {
//       // Handle unauthorized access
//       localStorage.removeItem("token");
//       navigateTo("/login");
//     }
//     return Promise.reject(error);
//   }
// );

interface User {
  id: number;
  nom?: string;
  prenom?: string;
  name?: string; // For blood banks
  email: string;
  telephone?: string;
  phone?: string; // For blood banks
  date_de_naissance?: string;
  groupe_sanguin?: string;
  adresse?: string;
  address?: string; // For blood banks
  type?: "admin" | "blood_bank";
}

interface RegisterData {
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  date_de_naissance: string;
  groupe_sanguin: string;
  adresse: string;
  password: string;
  password_confirmation: string;
}

interface AuthResponse {
  message: string;
  user: User;
  token: string;
  type: "admin" | "blood_bank";
}

export const useAuth = () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const userType = ref<"admin" | "blood_bank" | null>(null);

  const login = async (
    email: string,
    password: string,
    remember: boolean = false
  ) => {
    try {
      console.log("Attempting login with:", { email, password, remember });

      const response = await api.post<AuthResponse>("/login", {
        email,
        password,
        remember,
      });

      console.log("Login response:", response.data);
      if (response.data.token) {
        token.value = response.data.token;
        user.value = response.data.user;
        userType.value = response.data.type;

        // Store token and user type in localStorage if remember is true
        if (remember) {
          localStorage.setItem("token", response.data.token);
          localStorage.setItem("userType", response.data.type);
        }

        return response.data;
      } else {
        throw new Error("No token received from server");
      }
    } catch (error: any) {
      console.error("Login error:", error);
      throw new Error(
        error.response?.data?.message ||
          "Une erreur est survenue lors de la connexion"
      );
    }
  };

  const register = async (data: RegisterData) => {
    try {
      const response = await api.post<AuthResponse>("/register", data);

      if (response.data.token) {
        token.value = response.data.token;
        user.value = response.data.user;
        localStorage.setItem("token", response.data.token);
        return response.data;
      } else {
        throw new Error("No token received from server");
      }
    } catch (error: any) {
      console.error("Register error:", error);
      throw new Error(
        error.response?.data?.message ||
          "Une erreur est survenue lors de l'inscription"
      );
    }
  };

  const logout = async () => {
    try {
      await api.post("/logout");
      user.value = null;
      token.value = null;
      localStorage.removeItem("token");
    } catch (error: any) {
      console.error("Logout error:", error);
      throw new Error(
        error.response?.data?.message ||
          "Une erreur est survenue lors de la déconnexion"
      );
    }
  };

  const checkAuth = async () => {
    const storedToken = localStorage.getItem("token");
    const storedType = localStorage.getItem("userType") as
      | "admin"
      | "blood_bank"
      | null;

    if (storedToken) {
      try {
        const response = await api.get<User>("/user");
        user.value = response.data;
        token.value = storedToken;
        userType.value = storedType;
      } catch (error) {
        console.error("Auth check error:", error);
        user.value = null;
        token.value = null;
        userType.value = null;
        localStorage.removeItem("token");
        localStorage.removeItem("userType");
      }
    }
  };

  return {
    user,
    token,
    userType,
    login,
    register,
    logout,
    checkAuth,
  };
};
