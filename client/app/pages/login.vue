<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Connexion à votre compte
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
        <div class="rounded-md shadow-sm -space-y-px my-3">
          <div>
            <label for="email" class="sr-only">Email</label>
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-500': errors.email }"
              placeholder="Email"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email }}
            </p>
          </div>
          <div>
            <label for="password" class="sr-only">Mot de passe</label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-500': errors.password }"
              placeholder="Mot de passe"
            />
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">
              {{ errors.password }}
            </p>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember"
              v-model="form.remember"
              name="remember"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-900">
              Se souvenir de moi
            </label>
          </div>

          <div class="text-sm">
            <NuxtLink
              to="/forgot-password"
              class="font-medium text-indigo-600 hover:text-indigo-500"
            >
              Mot de passe oublié?
            </NuxtLink>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <span v-if="loading">Chargement...</span>
            <span v-else>Se connecter</span>
          </button>
        </div>

        <div class="text-center">
          <p class="text-sm text-gray-600">
            Pas encore de compte?
            <NuxtLink
              to="/register"
              class="font-medium text-indigo-600 hover:text-indigo-500"
            >
              S'inscrire
            </NuxtLink>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";
import { useAuth } from "../composables/useAuth";
import { loginSchema } from "../utils/validation";
import { useToast } from "vue-toastification";

definePageMeta({
  layout: false,
});
const { login } = useAuth();
const toast = useToast();
const loading = ref(false);
const errors = reactive({
  email: "",
  password: "",
});

const form = reactive({
  email: "",
  password: "",
  remember: false,
});

const handleSubmit = async () => {
  try {
    // Reset errors
    errors.email = "";
    errors.password = "";

    // Validate form
    const result = loginSchema.safeParse(form);
    if (!result.success) {
      const formattedErrors = result.error.format();
      if (formattedErrors.email)
        errors.email = formattedErrors.email._errors[0];
      if (formattedErrors.password)
        errors.password = formattedErrors.password._errors[0];
      return;
    }

    loading.value = true;
    await login(form.email, form.password, form.remember);
    toast.success("Connexion réussie!");
    await navigateTo("/dashboard");
  } catch (error: any) {
    toast.error(error.message || "Une erreur est survenue");
  } finally {
    loading.value = false;
  }
};
</script>
