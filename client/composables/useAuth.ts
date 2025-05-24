import { ref } from "vue";

interface User {
  id: number;
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  date_de_naissance: string;
  groupe_sanguin: string;
  adresse: string;
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

export const useAuth = () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);

  const login = async (
    email: string,
    password: string,
    remember: boolean = false
  ) => {
    try {
      const response = await $fetch("/api/login", {
        method: "POST",
        body: { email, password, remember },
      });

      token.value = response.token;
      user.value = response.user;

      // Store token in localStorage if remember is true
      if (remember) {
        localStorage.setItem("token", response.token);
      }

      // Set default headers for future requests
      const config = useRuntimeConfig();
      $fetch.defaults.headers.common[
        "Authorization"
      ] = `Bearer ${response.token}`;
    } catch (error: any) {
      throw new Error(error.data?.message || "Une erreur est survenue");
    }
  };

  const register = async (data: RegisterData) => {
    try {
      const response = await $fetch("/api/register", {
        method: "POST",
        body: data,
      });

      token.value = response.token;
      user.value = response.user;

      // Set default headers for future requests
      const config = useRuntimeConfig();
      $fetch.defaults.headers.common[
        "Authorization"
      ] = `Bearer ${response.token}`;
    } catch (error: any) {
      throw new Error(error.data?.message || "Une erreur est survenue");
    }
  };

  const logout = async () => {
    try {
      await $fetch("/api/logout", {
        method: "POST",
      });

      // Clear user data
      user.value = null;
      token.value = null;
      localStorage.removeItem("token");

      // Remove Authorization header
      delete $fetch.defaults.headers.common["Authorization"];
    } catch (error: any) {
      throw new Error(error.data?.message || "Une erreur est survenue");
    }
  };

  const checkAuth = async () => {
    const storedToken = localStorage.getItem("token");
    if (storedToken) {
      try {
        // Set the token in headers
        $fetch.defaults.headers.common[
          "Authorization"
        ] = `Bearer ${storedToken}`;

        // Get user data
        const response = await $fetch("/api/user");
        user.value = response;
        token.value = storedToken;
      } catch (error) {
        // If token is invalid, clear everything
        user.value = null;
        token.value = null;
        localStorage.removeItem("token");
        delete $fetch.defaults.headers.common["Authorization"];
      }
    }
  };

  return {
    user,
    token,
    login,
    register,
    logout,
    checkAuth,
  };
};
