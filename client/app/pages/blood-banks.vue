<template>
  <div style="overflow-y: auto">
    <UDashboardPanel id="blood-banks">
      <template #header>
        <UDashboardNavbar title="Centres de Don de Sang">
          <template #leading>
            <UDashboardSidebarCollapse />
          </template>

          <template #right>
            <UButton
              color="primary"
              icon="i-heroicons-plus"
              @click="openCreateModal"
            >
              Ajouter un Centre
            </UButton>
          </template>
        </UDashboardNavbar>
      </template>

      <template #body>
        <UContainer class="min-h-screen">
          <UCard>
            <template #header>
              <h1 class="text-2xl font-bold mt-5">Centres de Don de Sang</h1>
            </template>

            <div class="flex flex-wrap gap-4 mb-4">
              <UInput
                v-model="filters.search"
                placeholder="Rechercher un centre..."
                icon="i-heroicons-magnifying-glass"
                class="w-64"
              />
              <USelect
                v-model="filters.region"
                :items="regions"
                placeholder="Filtrer par région"
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
              <div v-else-if="error">
                Erreur lors du chargement des centres.
              </div>
              <UTable v-else :data="filteredBloodBanks" :columns="columns">
                <template #actions-data="{ row }">
                  <div class="flex gap-2">
                    <UButton
                      color="neutral"
                      variant="ghost"
                      icon="i-heroicons-pencil-square"
                      @click="openEditModal(row.original)"
                    />
                    <UButton
                      color="error"
                      variant="ghost"
                      icon="i-heroicons-trash"
                      @click="confirmDelete(row.original)"
                    />
                  </div>
                </template>
              </UTable>
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

    <!-- Create/Edit Modal -->
    <UModal
      v-model:open="showModal"
      :title="isEditing ? 'Modifier le Centre' : 'Nouveau Centre'"
    >
      <template #body>
        <form
          @submit.prevent="handleSubmit"
          class="grid grid-cols-1 md:grid-cols-2 gap-4"
        >
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Nom du Centre</label
            >
            <UInput v-model="form.name" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Adresse</label
            >
            <UInput v-model="form.address" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Région</label
            >
            <USelect v-model="form.region" :items="regions" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Téléphone</label
            >
            <UInput v-model="form.phone" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Email</label
            >
            <UInput v-model="form.email" type="email" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Horaires d'ouverture</label
            >
            <UInput v-model="form.opening_hours" required />
          </div>
          <div v-if="!isEditing">
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Mot de passe</label
            >
            <UInput v-model="form.password" type="password" required />
          </div>
          <div v-if="!isEditing">
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Confirmer le mot de passe</label
            >
            <UInput
              v-model="form.password_confirmation"
              type="password"
              required
            />
          </div>
        </form>
      </template>
      <template #footer>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" @click="showModal = false">
            Annuler
          </UButton>
          <UButton color="primary" @click="handleSubmit" :loading="submitting">
            {{ isEditing ? "Modifier" : "Créer" }}
          </UButton>
        </div>
      </template>
    </UModal>

    <!-- Delete Confirmation Modal -->
    <UModal v-model:open="showDeleteModal" title="Confirmer la suppression">
      <template #body>
        <p>Êtes-vous sûr de vouloir supprimer ce centre de don de sang ?</p>
      </template>
      <template #footer>
        <div class="flex justify-end gap-2">
          <UButton
            color="neutral"
            variant="ghost"
            @click="showDeleteModal = false"
          >
            Annuler
          </UButton>
          <UButton color="error" @click="handleDelete" :loading="deleting">
            Supprimer
          </UButton>
        </div>
      </template>
    </UModal>

    <!-- Details Modal -->
    <UModal
      v-model:open="showDetailsModal"
      title="Détails du Centre de Don de Sang"
    >
      <template #body>
        <div v-if="detailsBank" class="space-y-2">
          <div><strong>Nom:</strong> {{ detailsBank.name }}</div>
          <div><strong>Adresse:</strong> {{ detailsBank.address }}</div>
          <div><strong>Région:</strong> {{ detailsBank.region }}</div>
          <div><strong>Téléphone:</strong> {{ detailsBank.phone }}</div>
          <div><strong>Email:</strong> {{ detailsBank.email }}</div>
          <div><strong>Horaires:</strong> {{ detailsBank.opening_hours }}</div>
          <div><strong>Statut:</strong> {{ detailsBank.status }}</div>
          <div>
            <strong>Date de création:</strong>
            {{ new Date(detailsBank.created_at).toLocaleString("fr-FR") }}
          </div>
          <div>
            <strong>Date de modification:</strong>
            {{ new Date(detailsBank.updated_at).toLocaleString("fr-FR") }}
          </div>
        </div>
      </template>
      <template #footer>
        <div class="flex justify-end">
          <UButton
            color="neutral"
            variant="ghost"
            @click="showDetailsModal = false"
            >Fermer</UButton
          >
        </div>
      </template>
    </UModal>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from "vue";
import { useToast } from "vue-toastification";
import api from "../plugins/axios";
import { h, resolveComponent } from "vue";
import type { TableColumn } from "@nuxt/ui";

const UBadge = resolveComponent("UBadge");

interface BloodBank {
  id: number;
  name: string;
  address: string;
  region: string;
  phone: string;
  email: string;
  opening_hours: string;
  status: "pending" | "approved" | "rejected";
  created_at: string;
  updated_at: string;
}

const toast = useToast();
const loading = ref(false);
const error = ref<string | null>(null);
const page = ref(1);
const perPage = ref(10);
const total = ref(0);
const submitting = ref(false);
const deleting = ref(false);
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const selectedBloodBank = ref<BloodBank | null>(null);
const showDetailsModal = ref(false);
const detailsBank = ref<BloodBank | null>(null);

const filters = ref({
  search: "",
  region: "",
  status: "",
});

const regions = [
  { label: "Tanger-Tétouan-Al Hoceïma", value: "Tanger-Tétouan-Al Hoceïma" },
  { label: "Oriental", value: "Oriental" },
  { label: "Fès-Meknès", value: "Fès-Meknès" },
  { label: "Rabat-Salé-Kénitra", value: "Rabat-Salé-Kénitra" },
  { label: "Béni Mellal-Khénifra", value: "Béni Mellal-Khénifra" },
  { label: "Casablanca-Settat", value: "Casablanca-Settat" },
  { label: "Marrakech-Safi", value: "Marrakech-Safi" },
  { label: "Drâa-Tafilalet", value: "Drâa-Tafilalet" },
  { label: "Souss-Massa", value: "Souss-Massa" },
  { label: "Guelmim-Oued Noun", value: "Guelmim-Oued Noun" },
  { label: "Laâyoune-Sakia El Hamra", value: "Laâyoune-Sakia El Hamra" },
  { label: "Dakhla-Oued Ed-Dahab", value: "Dakhla-Oued Ed-Dahab" },
];

const statusOptions = [
  { label: "En attente", value: "pending" },
  { label: "Approuvé", value: "approved" },
  { label: "Rejeté", value: "rejected" },
];

const columns: TableColumn<BloodBank>[] = [
  {
    accessorKey: "name",
    header: "Nom du Centre",
  },
  {
    accessorKey: "address",
    header: "Adresse",
  },
  {
    accessorKey: "region",
    header: "Région",
  },
  {
    accessorKey: "phone",
    header: "Téléphone",
  },
  {
    accessorKey: "email",
    header: "Email",
  },
  // {
  //   accessorKey: "opening_hours",
  //   header: "Horaires",
  // },
  {
    accessorKey: "status",
    header: "Statut",
    cell: ({ row }) => {
      const color = {
        pending: "warning" as const,
        approved: "success" as const,
        rejected: "error" as const,
      }[row.getValue("status") as string];

      return h(UBadge, { class: "capitalize", variant: "subtle", color }, () =>
        row.getValue("status")
      );
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
  {
    accessorKey: "actions",
    header: "Actions",
    cell: ({ row }) => {
      const bank = row.original;
      const actions = [
        h(
          "button",
          {
            class: "text-blue-600 hover:underline",
            onClick: () => openEditModal(bank),
            type: "button",
          },
          "Modifier"
        ),
        h(
          "button",
          {
            class: "text-red-600 hover:underline",
            onClick: () => confirmDelete(bank),
            type: "button",
          },
          "Supprimer"
        ),
        h(
          "button",
          {
            class: "text-gray-600 hover:underline",
            onClick: () => openDetailsModal(bank),
            type: "button",
          },
          "Détails"
        ),
      ];
      if (bank.status === "pending") {
        actions.push(
          h(
            "button",
            {
              class: "text-green-600 hover:underline ml-2",
              onClick: () => approveBank(bank),
              type: "button",
            },
            "Accepter"
          ),
          h(
            "button",
            {
              class: "text-yellow-600 hover:underline ml-2",
              onClick: () => rejectBank(bank),
              type: "button",
            },
            "Rejeter"
          )
        );
      }
      return h("div", { class: "flex gap-2 flex-wrap" }, actions);
    },
  },
];

const form = ref({
  name: "",
  address: "",
  region: "",
  phone: "",
  email: "",
  opening_hours: "",
  password: "",
  password_confirmation: "",
});

const bloodBanks = ref<BloodBank[]>([]);

const filteredBloodBanks = computed(() => {
  return bloodBanks.value.filter((bank) => {
    const matchesSearch =
      !filters.value.search ||
      bank.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      bank.address.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      bank.email.toLowerCase().includes(filters.value.search.toLowerCase());
    const matchesRegion =
      !filters.value.region || bank.region === filters.value.region;
    const matchesStatus =
      !filters.value.status || bank.status === filters.value.status;
    return matchesSearch && matchesRegion && matchesStatus;
  });
});

onMounted(() => {
  fetchBloodBanks();
});

const fetchBloodBanks = async () => {
  loading.value = true;
  try {
    const params: any = {
      page: page.value,
      per_page: perPage.value,
      ...filters.value,
    };
    // Remove empty string values from params
    Object.keys(params).forEach((key) => {
      if (params[key] === "") delete params[key];
    });
    const response = await api.get("/blood-banks", {
      params,
    });
    console.log(response.data);
    bloodBanks.value = response.data;
    total.value = response.data.total;
  } catch (err: any) {
    error.value = err?.message || "Erreur inconnue";
    toast.error("Erreur lors du chargement des centres");
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  filters.value.search = "";
  filters.value.region = "";
  filters.value.status = "";
};

const openCreateModal = () => {
  isEditing.value = false;
  form.value = {
    name: "",
    address: "",
    region: "",
    phone: "",
    email: "",
    opening_hours: "",
    password: "",
    password_confirmation: "",
  };
  showModal.value = true;
};

const openEditModal = (bloodBank: BloodBank) => {
  isEditing.value = true;
  selectedBloodBank.value = bloodBank;
  form.value = {
    name: bloodBank.name,
    address: bloodBank.address,
    region: bloodBank.region,
    phone: bloodBank.phone,
    email: bloodBank.email,
    opening_hours: bloodBank.opening_hours,
    password: "",
    password_confirmation: "",
  };
  showModal.value = true;
};

const confirmDelete = (bloodBank: BloodBank) => {
  selectedBloodBank.value = bloodBank;
  showDeleteModal.value = true;
};

const handleSubmit = async () => {
  try {
    submitting.value = true;
    if (isEditing.value) {
      await api.put(`/blood-banks/${selectedBloodBank.value?.id}`, form.value);
      toast.success("Centre modifié avec succès");
    } else {
      await api.post("/blood-banks/register", form.value);
      toast.success("Centre créé avec succès");
    }
    showModal.value = false;
    fetchBloodBanks();
  } catch (error: any) {
    toast.error(error?.message || "Une erreur est survenue");
  } finally {
    submitting.value = false;
  }
};

const handleDelete = async () => {
  try {
    deleting.value = true;
    if (selectedBloodBank.value) {
      await api.delete(`/blood-banks/${selectedBloodBank.value.id}`);
      toast.success("Centre supprimé avec succès");
      showDeleteModal.value = false;
      fetchBloodBanks();
    }
  } catch (error: any) {
    toast.error(error?.message || "Une erreur est survenue");
  } finally {
    deleting.value = false;
  }
};

const approveBank = async (bank: BloodBank) => {
  try {
    await api.patch(`/blood-banks/${bank.id}/status`, { status: "approved" });
    toast.success("Centre approuvé avec succès");
    fetchBloodBanks();
  } catch (error: any) {
    toast.error(error?.message || "Erreur lors de l'approbation");
  }
};

const rejectBank = async (bank: BloodBank) => {
  try {
    await api.patch(`/blood-banks/${bank.id}/status`, { status: "rejected" });
    toast.success("Centre rejeté avec succès");
    fetchBloodBanks();
  } catch (error: any) {
    toast.error(error?.message || "Erreur lors du rejet");
  }
};

const openDetailsModal = (bank: BloodBank) => {
  detailsBank.value = bank;
  showDetailsModal.value = true;
};

watch([page, perPage], fetchBloodBanks, { deep: true });
watch(filters, fetchBloodBanks, { deep: true });

// definePageMeta({
//   middleware: [function (to, from) {
//     console.log(to, from);
//   },'auth'],
// });
</script>
