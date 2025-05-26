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
          <div>
            <div v-if="loading">Chargement...</div>
            <div v-else-if="error">Erreur lors du chargement des demandes.</div>
            <UTable v-else :data="demandes" :columns="columns" />
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

<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import api from "../plugins/axios";
import axios from "axios";
import { h, resolveComponent } from "vue";
import type { TableColumn } from "@nuxt/ui";

const UBadge = resolveComponent("UBadge");

// definePageMeta({
//   middleware: "auth",
// });

type Demande = {
  demande_id: number;
  groupe_sanguin: string;
  urgent: boolean;
  quantite: number;
  ville: string;
  nom_hopital: string;
  status: "pending" | "approved" | "rejected" | "completed";
  message: string | null;
  user_id: number;
  created_at: string;
  updated_at: string;
};

const demandes = ref<Demande[]>([]);
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

const columns: TableColumn<Demande>[] = [
  {
    accessorKey: "demande_id",
    header: "ID",
  },
  {
    accessorKey: "groupe_sanguin",
    header: "Groupe Sanguin",
  },
  {
    accessorKey: "urgent",
    header: "Urgent",
    cell: ({ row }) => {
      return row.getValue("urgent") ? "Oui" : "Non";
    },
  },
  {
    accessorKey: "quantite",
    header: "Quantité",
  },
  {
    accessorKey: "ville",
    header: "Ville",
  },
  {
    accessorKey: "nom_hopital",
    header: "Hôpital",
  },
  {
    accessorKey: "status",
    header: "Status",
    cell: ({ row }) => {
      const color = {
        pending: "warning" as const,
        approved: "success" as const,
        rejected: "error" as const,
        completed: "info" as const,
      }[row.getValue("status") as string];

      return h(UBadge, { class: "capitalize", variant: "subtle", color }, () =>
        row.getValue("status")
      );
    },
  },
  {
    accessorKey: "message",
    header: "Message",
    cell: ({ row }) => {
      const message = row.getValue("message");
      return message || "-";
    },
  },
  {
    accessorKey: "created_at",
    header: "Date de création",
    cell: ({ row }) => {
      return new Date(row.getValue("created_at")).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
      });
    },
  },
];

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
    const response = await api.get("/demandes", {
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
