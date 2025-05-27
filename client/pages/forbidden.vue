<template>
  <div class="flex flex-col items-center justify-center min-h-screen">
    <h1 class="text-5xl font-bold text-red-600 mb-4">403 Forbidden</h1>
    <p class="mb-6">You do not have permission to access this page.</p>
    <UButton @click="navigateTo('/login')" color="red">Return to Login</UButton>
  </div>
</template>

<script setup lang="ts">
function navigateToLogin() {
  navigateTo("/login");
}
</script>
