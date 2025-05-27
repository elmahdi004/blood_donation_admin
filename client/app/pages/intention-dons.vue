<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import api from "../plugins/axios";

const intentions = ref([]);
const loading = ref(true);
const error = ref(null);
const page = ref(1);
const perPage = ref(10);
const total = ref(0);
const filters = ref({
  groupe_sanguin: "",
  valide: "",
});

const groupeSanguinOptions = [
  { label: "A+", value: "A+" },
  { label: "A-", value: "A-" },
  { label: "B+", value: "B+" },
  { label: "B-", value: "B-" },
  { label: "AB+", value: "AB+" },
  { label: "AB-", value: "AB-" },
  { label: "O+", value: "O+" },
  { label: "O-", value: "O-" },
];
const valideOptions = [
  { label: "Oui", value: "1" },
  { label: "Non", value: "0" },
];

const columns = [
  { accessorKey: "id", header: "ID" },
  { accessorKey: "groupe_sanguin", header: "Groupe Sanguin" },
  { accessorKey: "date_disponibilite", header: "Date de disponibilité" },
  {
    accessorKey: "valide",
    header: "Validé",
    cell: ({ row }) => (row.getValue("valide") ? "Oui" : "Non"),
  },
  { accessorKey: "notes", header: "Notes" },
  {
    accessorKey: "user_id",
    header: "Utilisateur",
    cell: ({ row }) => {
      const user = row.original.user;
      return user
        ? `${user.nom || ""} ${user.prenom || ""}`.trim()
        : row.getValue("user_id");
    },
  },
];

const fetchIntentions = async () => {
  loading.value = true;
  try {
    const params = {
      page: page.value,
      per_page: perPage.value,
      ...filters.value,
    };
    Object.keys(params).forEach((key) => {
      if (params[key] === "") delete params[key];
    });
    const response = await api.get("/intention-dons", { params });
    intentions.value = response.data.data || response.data;
    total.value = response.data.total || intentions.value.length;
  } catch (err) {
    error.value = "Erreur lors du chargement des intentions de don";
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  filters.value.groupe_sanguin = "";
  filters.value.valide = "";
};

onMounted(fetchIntentions);
watch([page, perPage], fetchIntentions, { deep: true });
watch(filters, fetchIntentions, { deep: true });
</script>

<template>
  <UDashboardPanel id="intention-dons">
    <template #header>
      <UDashboardNavbar title="Intentions de Don">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>
    <template #body>
      <UContainer class="min-h-screen">
        <UCard>
          <template #header>
            <h1 class="text-2xl font-bold mt-5">Intentions de Don</h1>
          </template>
          <div class="flex flex-wrap gap-4 mb-4">
            <USelect
              v-model="filters.groupe_sanguin"
              :items="groupeSanguinOptions"
              placeholder="Groupe Sanguin"
              class="w-48"
            />
            <USelect
              v-model="filters.valide"
              :items="valideOptions"
              placeholder="Validé"
              class="w-48"
            />
            <UButton @click="resetFilters">Réinitialiser</UButton>
          </div>
          <div>
            <div v-if="loading">Chargement...</div>
            <div v-else-if="error">{{ error }}</div>
            <UTable v-else :data="intentions" :columns="columns" />
            <UPagination
              v-if="total > perPage"
              v-model="page"
              :total="total"
              :items-per-page="perPage"
              class="mt-4"
            />
          </div>
        </UCard>
      </UContainer>
    </template>
  </UDashboardPanel>
</template>
