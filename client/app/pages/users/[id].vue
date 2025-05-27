<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../../../plugins/axios";

const route = useRoute();
const router = useRouter();
const userId = route.params.id;

const demandes = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    const response = await api.get(`/users/${userId}/demandes`);
    demandes.value = response.data;
  } catch (err) {
    error.value = "Erreur lors du chargement des demandes";
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <UContainer>
    <UCard>
      <template #header>
        <h1 class="text-2xl font-bold mt-5">Demandes de l'utilisateur</h1>
      </template>
      <div v-if="loading">Chargement...</div>
      <div v-else-if="error">{{ error }}</div>
      <UTable
        v-else
        :data="demandes"
        :columns="[
          { accessorKey: 'demande_id', header: 'ID' },
          { accessorKey: 'groupe_sanguin', header: 'Groupe Sanguin' },
          { accessorKey: 'quantite', header: 'Quantité' },
          { accessorKey: 'ville', header: 'Ville' },
          { accessorKey: 'nom_hopital', header: 'Hôpital' },
          { accessorKey: 'status', header: 'Statut' },
          { accessorKey: 'created_at', header: 'Date de création' },
        ]"
      />
    </UCard>
  </UContainer>
</template>
