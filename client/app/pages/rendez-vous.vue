<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../plugins/axios";

const rendezVous = ref([]);
const loading = ref(true);
const error = ref(null);

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editRdv = ref(null);
const deleteRdv = ref(null);
const editForm = ref({
  user_id: "",
  date: "",
  status: "pending",
});

const fetchRendezVous = async () => {
  loading.value = true;
  try {
    const response = await api.get("/rendez-vous");
    rendezVous.value = response.data;
  } catch (err) {
    error.value = "Erreur lors du chargement des rendez-vous";
  } finally {
    loading.value = false;
  }
};

const openEditModal = (entry) => {
  editRdv.value = entry;
  editForm.value = {
    user_id: entry.user_id,
    date: entry.date,
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

const openDeleteModal = (entry) => {
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
          <div v-if="loading">Chargement...</div>
          <div v-else-if="error">{{ error }}</div>
          <UTable
            v-else
            :data="rendezVous"
            :columns="[
              { accessorKey: 'user_id', header: 'Utilisateur' },
              { accessorKey: 'date', header: 'Date' },
              { accessorKey: 'status', header: 'Statut' },
              {
                accessorKey: 'actions',
                header: 'Actions',
                cell: ({ row }) => [
                  h(
                    'button',
                    {
                      class: 'text-blue-600 hover:underline mr-2',
                      onClick: () => openEditModal(row.original),
                      type: 'button',
                    },
                    'Modifier'
                  ),
                  h(
                    'button',
                    {
                      class: 'text-red-600 hover:underline',
                      onClick: () => openDeleteModal(row.original),
                      type: 'button',
                    },
                    'Supprimer'
                  ),
                ],
                sortable: false,
              },
            ]"
          />
        </UCard>
      </UContainer>
      <UModal v-model:open="showEditModal" title="Modifier le rendez-vous">
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
              <UInput v-model="editForm.date" type="date" />
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
      <UModal v-model:open="showDeleteModal" title="Confirmer la suppression">
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
