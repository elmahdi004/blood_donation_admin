<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import api from "../plugins/axios";
import { h, resolveComponent } from "vue";

const UBadge = resolveComponent("UBadge");

const rendezVous = ref([]);
const loading = ref(true);
const error = ref<string | null>(null);

const filters = ref({
  slot_date: "",
  status: "",
});

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editRdv = ref<any>(null);
const deleteRdv = ref<any>(null);
const editForm = ref({
  user_id: "",
  created_at: "",
  status: "pending",
});

const fetchRendezVous = async () => {
  loading.value = true;
  try {
    const params: Record<string, string> = {};
    if (filters.value.slot_date) {
      params["slot_date"] = filters.value.slot_date;
    }
    if (filters.value.status) {
      params["status"] = filters.value.status;
    }
    const response = await api.get("/rendez-vous", { params });
    rendezVous.value = response.data;
  } catch (err) {
    error.value = "Erreur lors du chargement des rendez-vous";
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  filters.value.slot_date = "";
  filters.value.status = "";
};

const openEditModal = (entry: any) => {
  editRdv.value = entry;
  editForm.value = {
    user_id: entry.user_id,
    created_at: entry.created_at,
    status: entry.status,
  };
  showEditModal.value = true;
};

const submitEdit = async () => {
  if (!editRdv.value) return;
  try {
    await api.put(`/rendez-vous/${editRdv.value.id}`, editForm.value);
    showEditModal.value = false;
    fetchRendezVous();
  } catch (err) {
    error.value = "Erreur lors de la modification";
  }
};

const openDeleteModal = (entry: any) => {
  deleteRdv.value = entry;
  showDeleteModal.value = true;
};

const submitDelete = async () => {
  if (!deleteRdv.value) return;
  try {
    await api.delete(`/rendez-vous/${deleteRdv.value.id}`);
    showDeleteModal.value = false;
    fetchRendezVous();
  } catch (err) {
    error.value = "Erreur lors de la suppression";
  }
};

onMounted(fetchRendezVous);
watch(filters, fetchRendezVous, { deep: true });

const columns = [
  {
    header: "Utilisateur",
    cell: ({ row }: any) => {
      const user = row.original.user;
      if (!user) return "-";
      return `${user.nom || ""} ${user.prenom || ""}`.trim();
    },
  },
  // { accessorKey: "created_at", header: "Date" },
  {
    accessorKey: "slot.start_time",
    header: "Début",
    cell: ({ row }: any) => row.original.slot?.start_time || "-",
  },
  {
    accessorKey: "slot.end_time",
    header: "Fin",
    cell: ({ row }: any) => row.original.slot?.end_time || "-",
  },
  {
    accessorKey: "slot.date",
    header: "Date du créneau",
    cell: ({ row }: any) => row.original.slot?.date || "-",
  },
  {
    accessorKey: "status",
    header: "Statut",
    cell: ({ row }: any) => {
      const status: string = row.getValue("status");
      const colorMap: Record<string, string> = {
        pending: "warning",
        confirmed: "success",
        completed: "info",
        cancelled: "error",
      };
      const color = colorMap[status] || "default";
      return h(
        UBadge,
        { class: "capitalize px-3 py-1 text-sm", variant: "solid", color },
        () => status.charAt(0).toUpperCase() + status.slice(1)
      );
    },
  },
  {
    header: "Changer le statut",
    cell: ({ row }: any) => {
      const status: string = row.getValue("status");
      const rendezVousId = row.original.rendez_vous_id;
      const colorMap: Record<string, string> = {
        pending: "warning",
        confirmed: "success",
        completed: "info",
        cancelled: "error",
      };
      return h(
        "select",
        {
          value: status,
          onChange: async (event: Event) => {
            const newStatus = (event.target as HTMLSelectElement).value;
            await api.put(`/rendez-vous/${rendezVousId}`, {
              ...row.original,
              status: newStatus,
            });
            fetchRendezVous();
          },
          class: "capitalize border rounded px-2 py-1",
        },
        [
          ["pending", "confirmed", "completed", "cancelled"].map((s) =>
            h(
              "option",
              {
                value: s,
                selected: s === status,
              },
              s.charAt(0).toUpperCase() + s.slice(1)
            )
          ),
        ]
      );
    },
  },
  // {
  //   accessorKey: "actions",
  //   header: "Actions",
  //   cell: ({ row }: any) => [
  //     h(
  //       "button",
  //       {
  //         class: "text-blue-600 hover:underline mr-2",
  //         onClick: () => openEditModal(row.original),
  //         type: "button",
  //       },
  //       "Modifier"
  //     ),
  //     h(
  //       "button",
  //       {
  //         class: "text-red-600 hover:underline",
  //         onClick: () => openDeleteModal(row.original),
  //         type: "button",
  //       },
  //       "Supprimer"
  //     ),
  //   ],
  // },
];
</script>

<template>
  <UDashboardPanel id="rendez-vous">
    <template #header>
      <UDashboardNavbar title="Gestion des Rendez-vous">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>
    <template #body>
      <UContainer class="min-h-screen">
        <UCard>
          <template #header>
            <h1 class="text-2xl font-bold mt-5">Rendez-vous</h1>
          </template>
          <div class="flex flex-wrap gap-4 mb-4">
            <UInput
              v-model="filters.slot_date"
              type="date"
              placeholder="Filtrer par date du créneau"
              class="w-48"
            />
            <USelect
              v-model="filters.status"
              :options="[
                { label: 'Tous', value: '' },
                { label: 'En attente', value: 'pending' },
                { label: 'Confirmé', value: 'confirmed' },
                { label: 'Terminé', value: 'completed' },
                { label: 'Annulé', value: 'cancelled' },
              ]"
              placeholder="Filtrer par statut"
              class="w-48"
            />
            <UButton @click="resetFilters">Réinitialiser</UButton>
          </div>
          <div v-if="loading">Chargement...</div>
          <div v-else-if="error">{{ error }}</div>
          <UTable v-else :data="rendezVous" :columns="columns" />
        </UCard>
      </UContainer>
      <UModal v-model="showEditModal" title="Modifier le rendez-vous">
        <template #body>
          <div class="space-y-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Utilisateur</label
              >
              <UInput v-model="editForm.user_id" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Date</label
              >
              <UInput v-model="editForm.created_at" type="date" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Statut</label
              >
              <UInput v-model="editForm.status" />
            </div>
          </div>
        </template>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton
              color="neutral"
              variant="ghost"
              @click="showEditModal = false"
              >Annuler</UButton
            >
            <UButton color="primary" @click="submitEdit">Enregistrer</UButton>
          </div>
        </template>
      </UModal>
      <UModal v-model="showDeleteModal" title="Confirmer la suppression">
        <template #body>
          <div>Voulez-vous vraiment supprimer ce rendez-vous ?</div>
        </template>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton
              color="neutral"
              variant="ghost"
              @click="showDeleteModal = false"
              >Annuler</UButton
            >
            <UButton color="error" @click="submitDelete">Supprimer</UButton>
          </div>
        </template>
      </UModal>
    </template>
  </UDashboardPanel>
</template>
