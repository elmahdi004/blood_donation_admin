<template>
    <UDashboardPanel id="demandes">
    <template #header>
      <UDashboardNavbar title="Demandes">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <CustomersAddModal />
        </template>
      </UDashboardNavbar>
    </template>
    <template #body>
  <UContainer class="min-h-screen">
    <UCard>
      <template #header>
        <h1 class="text-2xl font-bold mt-5">Demandes</h1>
      </template>
      <div class="flex flex-wrap gap-4 mb-4">
        <USelect
          v-model="filters.groupe_sanguin"
          :items="groupeSanguinOptions"
          placeholder="Groupe Sanguin"
          class="w-48"
        />
        <USelect
          v-model="filters.status"
          :items="statusOptions"
          placeholder="Statut"
          class="w-48"
        />
        <UButton @click="resetFilters">Réinitialiser</UButton>
      </div>
      <!-- <template #body> -->
      <div>
        <div v-if="loading">Chargement...</div>
        <div v-else-if="error">Erreur lors du chargement des demandes.</div>
        <UTable v-else :data="demandes" />
        <UPagination
          v-if="total > perPage"
          v-model:page="page"
          :total="total"
          :items-per-page="perPage"
          class="mt-4"
        />
      </div>
      <!-- </template> -->
    </UCard>
  </UContainer>
  </template>
  </UDashboardPanel>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import axios from "axios";

const demandes = ref([]);
const loading = ref(true);
const error = ref(null);
const page = ref(1);
const perPage = ref(10);
const total = ref(0);
const filters = ref({
  groupe_sanguin: "",
  status: "",
});

const groupeSanguinOptions = ref([
  { label: "A+", value: "A+" },
  { label: "A-", value: "A-" },
  { label: "B+", value: "B+" },
  { label: "B-", value: "B-" },
  { label: "AB+", value: "AB+" },
  { label: "AB-", value: "AB-" },
  { label: "O+", value: "O+" },
  { label: "O-", value: "O-" },
]);
const statusOptions = ref([
  { label: "pending", value: "pending" },
  { label: "approved", value: "approved" },
  { label: "rejected", value: "rejected" },
  { label: "completed", value: "completed" },
]);

onMounted(() => {
  fetchDemandes();
});

const fetchDemandes = async () => {
  loading.value = true;
  try {
    const params = {
      page: page.value,
      per_page: perPage.value,
      ...filters.value,
    };
    // Remove empty string values from params
    Object.keys(params).forEach((key) => {
      if (params[key] === "") delete params[key];
    });
    const response = await axios.get("http://localhost:8000/api/demandes", {
      params,
    });
    demandes.value = response.data.data;
    total.value = response.data.total;
  } catch (err) {
    error.value = err;
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  filters.value.groupe_sanguin = "";
  filters.value.status = "";
};

watch([page, perPage], fetchDemandes, { deep: true });
watch(filters, fetchDemandes, { deep: true });
</script>
