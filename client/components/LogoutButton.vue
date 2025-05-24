<template>
  <UButton
    color="gray"
    variant="ghost"
    :loading="loading"
    @click="handleLogout"
  >
    <template #leading>
      <UIcon name="i-lucide-log-out" />
    </template>
    Se déconnecter
  </UButton>
</template>

<script setup lang="ts">
import { useAuth } from "~/composables/useAuth";

const router = useRouter();
const toast = useToast();
const { logout } = useAuth();
const loading = ref(false);

const handleLogout = async () => {
  loading.value = true;
  try {
    await $fetch("/api/logout", {
      method: "POST",
    });

    logout();
    router.push("/login");

    toast.add({
      title: "Déconnexion réussie",
      description: "À bientôt!",
      color: "green",
    });
  } catch (error: any) {
    toast.add({
      title: "Erreur de déconnexion",
      description: error.data?.message || "Une erreur est survenue",
      color: "red",
    });
  } finally {
    loading.value = false;
  }
};
</script>
