<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Créer un compte
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
        <div class="rounded-md space-y-4">
          <div>
            <label for="nom" class="block text-sm font-medium text-gray-700"
              >Nom</label
            >
            <input
              id="nom"
              v-model="form.nom"
              name="nom"
              type="text"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.nom }"
            />
            <p v-if="errors.nom" class="mt-1 text-sm text-red-600">
              {{ errors.nom }}
            </p>
          </div>

          <div>
            <label for="prenom" class="block text-sm font-medium text-gray-700"
              >Prénom</label
            >
            <input
              id="prenom"
              v-model="form.prenom"
              name="prenom"
              type="text"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.prenom }"
            />
            <p v-if="errors.prenom" class="mt-1 text-sm text-red-600">
              {{ errors.prenom }}
            </p>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700"
              >Email</label
            >
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.email }"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email }}
            </p>
          </div>

          <div>
            <label
              for="telephone"
              class="block text-sm font-medium text-gray-700"
              >Téléphone</label
            >
            <input
              id="telephone"
              v-model="form.telephone"
              name="telephone"
              type="tel"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.telephone }"
            />
            <p v-if="errors.telephone" class="mt-1 text-sm text-red-600">
              {{ errors.telephone }}
            </p>
          </div>

          <div>
            <label
              for="date_de_naissance"
              class="block text-sm font-medium text-gray-700"
              >Date de naissance</label
            >
            <input
              id="date_de_naissance"
              v-model="form.date_de_naissance"
              name="date_de_naissance"
              type="date"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.date_de_naissance }"
            />
            <p
              v-if="errors.date_de_naissance"
              class="mt-1 text-sm text-red-600"
            >
              {{ errors.date_de_naissance }}
            </p>
          </div>

          <div>
            <label
              for="groupe_sanguin"
              class="block text-sm font-medium text-gray-700"
              >Groupe sanguin</label
            >
            <select
              id="groupe_sanguin"
              v-model="form.groupe_sanguin"
              name="groupe_sanguin"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.groupe_sanguin }"
            >
              <option value="">Sélectionnez un groupe sanguin</option>
              <option value="A+">A+</option>
              <option value="A-">A-</option>
              <option value="B+">B+</option>
              <option value="B-">B-</option>
              <option value="AB+">AB+</option>
              <option value="AB-">AB-</option>
              <option value="O+">O+</option>
              <option value="O-">O-</option>
            </select>
            <p v-if="errors.groupe_sanguin" class="mt-1 text-sm text-red-600">
              {{ errors.groupe_sanguin }}
            </p>
          </div>

          <div>
            <label for="adresse" class="block text-sm font-medium text-gray-700"
              >Adresse</label
            >
            <textarea
              id="adresse"
              v-model="form.adresse"
              name="adresse"
              required
              rows="3"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.adresse }"
            ></textarea>
            <p v-if="errors.adresse" class="mt-1 text-sm text-red-600">
              {{ errors.adresse }}
            </p>
          </div>

          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700"
              >Mot de passe</label
            >
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.password }"
            />
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">
              {{ errors.password }}
            </p>
          </div>

          <div>
            <label
              for="password_confirmation"
              class="block text-sm font-medium text-gray-700"
              >Confirmer le mot de passe</label
            >
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :class="{ 'border-red-500': errors.password_confirmation }"
            />
            <p
              v-if="errors.password_confirmation"
              class="mt-1 text-sm text-red-600"
            >
              {{ errors.password_confirmation }}
            </p>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <span v-if="loading">Chargement...</span>
            <span v-else>S'inscrire</span>
          </button>
        </div>

        <div class="text-center">
          <p class="text-sm text-gray-600">
            Déjà un compte?
            <NuxtLink
              to="/login"
              class="font-medium text-indigo-600 hover:text-indigo-500"
            >
              Se connecter
            </NuxtLink>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";
import { useAuth } from "../composables/useAuth.ts";
import { registerSchema } from "../utils/validation.ts";
import { useToast } from "vue-toastification";

definePageMeta({
  layout: false,
});

const { register } = useAuth();
const toast = useToast();
const loading = ref(false);
const errors = reactive({
  nom: "",
  prenom: "",
  email: "",
  telephone: "",
  date_de_naissance: "",
  groupe_sanguin: "",
  adresse: "",
  password: "",
  password_confirmation: "",
});

const form = reactive({
  nom: "",
  prenom: "",
  email: "",
  telephone: "",
  date_de_naissance: "",
  groupe_sanguin: "",
  adresse: "",
  password: "",
  password_confirmation: "",
});


definePageMeta({
  layout: false,
});

const handleSubmit = async () => {
  try {
    // Reset errors
    Object.keys(errors).forEach((key) => {
      errors[key as keyof typeof errors] = "";
    });

    // Validate form
    const result = registerSchema.safeParse(form);
    if (!result.success) {
      const formattedErrors = result.error.format();
      Object.keys(formattedErrors).forEach((key) => {
        if (
          formattedErrors[key as keyof typeof formattedErrors]?._errors?.[0]
        ) {
          errors[key as keyof typeof errors] =
            formattedErrors[key as keyof typeof formattedErrors]._errors[0];
        }
      });
      return;
    }

    loading.value = true;
    await register(form);
    toast.success("Inscription réussie!");
    navigateTo("/");
  } catch (error: any) {
    toast.error(error.message || "Une erreur est survenue");
  } finally {
    loading.value = false;
  }
};
</script>
