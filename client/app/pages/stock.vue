<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../plugins/axios";

const stock = ref([]);
const loading = ref(true);
const error = ref(null);

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editStock = ref(null);
const deleteStock = ref(null);
const editForm = ref({
  groupe_sanguin: "",
  quantite: 0,
});

const fetchStock = async () => {
  loading.value = true;
  try {
    const response = await api.get("/stock");
    stock.value = response.data;
  } catch (err) {
    error.value = "Erreur lors du chargement du stock";
  } finally {
    loading.value = false;
  }
};

const openEditModal = (entry) => {
  editStock.value = entry;
  editForm.value = {
    groupe_sanguin: entry.groupe_sanguin,
    quantite: entry.quantite,
  };
  showEditModal.value = true;
};

const submitEdit = async () => {
  if (!editStock.value) return;
  try {
    await api.put(`/stock/${editStock.value.stock_id}`, editForm.value);
    showEditModal.value = false;
    fetchStock();
  } catch (err) {
    error.value = "Erreur lors de la modification";
  }
};

const openDeleteModal = (entry) => {
  deleteStock.value = entry;
  showDeleteModal.value = true;
};

const submitDelete = async () => {
  if (!deleteStock.value) return;
  try {
    await api.delete(`/stock/${deleteStock.value.stock_id}`);
    showDeleteModal.value = false;
    fetchStock();
  } catch (err) {
    error.value = "Erreur lors de la suppression";
  }
};

onMounted(fetchStock);
</script>

<template>
  <UDashboardPanel id="stock">
    <template #header>
      <UDashboardNavbar title="Gestion du Stock">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>
    <template #body>
      <UContainer class="min-h-screen">
        <UCard>
          <template #header>
            <h1 class="text-2xl font-bold mt-5">Stock de Sang</h1>
          </template>
          <div v-if="loading">Chargement...</div>
          <div v-else-if="error">{{ error }}</div>
          <UTable
            v-else
            :data="stock"
            :columns="[
              { accessorKey: 'groupe_sanguin', header: 'Groupe Sanguin' },
              { accessorKey: 'quantite', header: 'Quantité' },
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
                    // 'Supprimer'
                  ),
                ],
                sortable: false,
              },
            ]"
          />
        </UCard>
      </UContainer>
      <UModal v-model:open="showEditModal" title="Modifier le stock">
        <template #body>
          <div class="space-y-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Groupe sanguin</label
              >
              <UInput v-model="editForm.groupe_sanguin" disabled />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Quantité</label
              >
              <UInput v-model="editForm.quantite" type="number" min="0" />
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
          <div>Voulez-vous vraiment supprimer cette entrée de stock ?</div>
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
