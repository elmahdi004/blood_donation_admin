<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../plugins/axios";
import { h } from "vue";

const slots = ref([]);
const loading = ref(true);
const error = ref(null);

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editSlot = ref(null);
const deleteSlot = ref(null);
const editForm = ref({
  date: "",
  start_time: "",
  end_time: "",
  max_appointments: 1,
});

const fetchSlots = async () => {
  loading.value = true;
  try {
    const response = await api.get("/slots");
    slots.value = response.data;
  } catch (err) {
    error.value = "Erreur lors du chargement des créneaux";
  } finally {
    loading.value = false;
  }
};

const openEditModal = (entry) => {
  editSlot.value = entry;
  editForm.value = {
    date: entry.date,
    start_time: entry.start_time,
    end_time: entry.end_time,
    max_appointments: entry.max_appointments,
  };
  showEditModal.value = true;
};

const submitEdit = async () => {
  if (!editSlot.value) return;
  try {
    await api.put(`/slots/${editSlot.value.id}`, editForm.value);
    showEditModal.value = false;
    fetchSlots();
  } catch (err) {
    error.value = "Erreur lors de la modification";
  }
};

const openDeleteModal = (entry) => {
  deleteSlot.value = entry;
  showDeleteModal.value = true;
};

const submitDelete = async () => {
  if (!deleteSlot.value) return;
  try {
    await api.delete(`/slots/${deleteSlot.value.id}`);
    showDeleteModal.value = false;
    fetchSlots();
  } catch (err) {
    error.value = "Erreur lors de la suppression";
  }
};

onMounted(fetchSlots);
</script>

<template>
  <UDashboardPanel id="slots">
    <template #header>
      <UDashboardNavbar title="Créneaux Disponibles">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>
    <template #body>
      <UContainer class="min-h-screen">
        <UCard>
          <template #header>
            <h1 class="text-2xl font-bold mt-5">Créneaux Disponibles</h1>
          </template>
          <div v-if="loading">Chargement...</div>
          <div v-else-if="error">{{ error }}</div>
          <UTable
            v-else
            :data="slots"
            :columns="[
              { accessorKey: 'date', header: 'Date' },
              { accessorKey: 'start_time', header: 'Heure de début' },
              { accessorKey: 'end_time', header: 'Heure de fin' },
              { accessorKey: 'max_appointments', header: 'Max RDV' },
              {
                accessorKey: 'actions',
                header: 'Actions',
                cell: ({ row }) => [
                  h('button', {
                    class: 'text-blue-600 hover:underline mr-2',
                    onClick: () => openEditModal(row.original),
                    type: 'button'
                  }, 'Modifier'),
                  h('button', {
                    class: 'text-red-600 hover:underline',
                    onClick: () => openDeleteModal(row.original),
                    type: 'button'
                  }, 'Supprimer')
                ],
              }
            ]"
          />
        </UCard>
      </UContainer>
      <UModal v-model:open="showEditModal" title="Modifier le créneau">
        <template #body>
          <div class="space-y-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
              <UInput v-model="editForm.date" type="date" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Heure de début</label>
              <UInput v-model="editForm.start_time" type="time" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Heure de fin</label>
              <UInput v-model="editForm.end_time" type="time" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Max RDV</label>
              <UInput v-model="editForm.max_appointments" type="number" min="1" />
            </div>
          </div>
        </template>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton color="neutral" variant="ghost" @click="showEditModal = false">Annuler</UButton>
            <UButton color="primary" @click="submitEdit">Enregistrer</UButton>
          </div>
        </template>
      </UModal>
      <UModal v-model:open="showDeleteModal" title="Confirmer la suppression">
        <template #body>
          <div>Voulez-vous vraiment supprimer ce créneau ?</div>
        </template>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton color="neutral" variant="ghost" @click="showDeleteModal = false">Annuler</UButton>
            <UButton color="error" @click="submitDelete">Supprimer</UButton>
          </div>
        </template>
      </UModal>
    </template>
  </UDashboardPanel>
</template> 