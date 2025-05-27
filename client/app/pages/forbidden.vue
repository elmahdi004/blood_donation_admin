<template>
  <div
    class="flex flex-col items-center justify-center min-h-screen bg-gray-50"
  >
    <h1 class="text-4xl font-bold text-red-600 mb-4">403 - Accès interdit</h1>
    <p class="text-lg mb-6">Vous n'êtes pas autorisé à accéder à cette page.</p>
    <UButton color="primary" @click="goToLogin">Retour à la connexion</UButton>
  </div>
</template>

<script setup lang="ts">
const goToLogin = () => {
  navigateTo("/login");
};
</script>
