<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import api from "../plugins/axios";
import { h } from "vue";

const slots = ref([]);
const loading = ref(true);
const error = ref(null);
const selectedDate = ref(null);
const showPublishModal = ref(false);
const publishForm = ref({
  morning_start: "",
  morning_end: "",
  morning_max: 1,
  evening_start: "",
  evening_end: "",
  evening_max: 1,
});

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

const openPublishModal = (date) => {
  selectedDate.value = date;
  publishForm.value = {
    morning_start: "",
    morning_end: "",
    morning_max: 1,
    evening_start: "",
    evening_end: "",
    evening_max: 1,
  };
  showPublishModal.value = true;
};

const submitPublish = async () => {
  if (!selectedDate.value) return;
  try {
    await api.post("/slots", {
      date: selectedDate.value,
      ...publishForm.value,
    });
    showPublishModal.value = false;
    fetchSlots();
  } catch (err) {
    error.value = "Erreur lors de la publication des créneaux";
  }
};

const filteredSlots = computed(() => {
  if (!selectedDate.value) return [];
  return slots.value.filter(slot => slot.date === selectedDate.value);
});

const morningSlots = computed(() => {
  return filteredSlots.value.filter(slot => {
    const hour = parseInt(slot.start_time.split(':')[0]);
    return hour < 12;
  });
});

const eveningSlots = computed(() => {
  return filteredSlots.value.filter(slot => {
    const hour = parseInt(slot.start_time.split(':')[0]);
    return hour >= 12;
  });
});

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
          <div v-else>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Sélectionner une date</label>
              <UInput v-model="selectedDate" type="date" @change="openPublishModal(selectedDate)" />
            </div>
            <div v-if="selectedDate">
              <h2 class="text-xl font-semibold mb-2">Période du matin</h2>
              <div class="grid grid-cols-4 gap-2 mb-4">
                <div v-for="slot in morningSlots" :key="slot.id" class="p-2 border rounded" :class="slot.rendez_vous_id ? 'bg-red-200' : 'bg-green-200'">
                  <div class="text-sm">{{ slot.start_time }} - {{ slot.end_time }}</div>
                  <div class="text-xs">{{ slot.rendezVous ? 'Réservé' : 'Disponible' }}</div>
                </div>
              </div>
              <h2 class="text-xl font-semibold mb-2">Période du soir</h2>
              <div class="grid grid-cols-4 gap-2">
                <div v-for="slot in eveningSlots" :key="slot.id" class="p-2 border rounded" :class="slot.rendez_vous_id ? 'bg-red-200' : 'bg-green-200'">
                  <div class="text-sm">{{ slot.start_time }} - {{ slot.end_time }}</div>
                  <div class="text-xs">{{ slot.rendez_vous_id ? 'Réservé' : 'Disponible' }}</div>
                </div>
              </div>
            </div>
          </div>
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
      <UModal v-model:open="showPublishModal" title="Publier les créneaux">
        <template #body>
          <div class="space-y-4">
            <div>
              <h3 class="font-medium">Période du matin</h3>
              <div class="grid grid-cols-3 gap-2">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Début</label>
                  <UInput v-model="publishForm.morning_start" type="time" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Fin</label>
                  <UInput v-model="publishForm.morning_end" type="time" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Max RDV</label>
                  <UInput v-model="publishForm.morning_max" type="number" min="1" />
                </div>
              </div>
            </div>
            <div>
              <h3 class="font-medium">Période du soir</h3>
              <div class="grid grid-cols-3 gap-2">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Début</label>
                  <UInput v-model="publishForm.evening_start" type="time" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Fin</label>
                  <UInput v-model="publishForm.evening_end" type="time" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Max RDV</label>
                  <UInput v-model="publishForm.evening_max" type="number" min="1" />
                </div>
              </div>
            </div>
          </div>
        </template>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton color="neutral" variant="ghost" @click="showPublishModal = false">Annuler</UButton>
            <UButton color="primary" @click="submitPublish">Publier</UButton>
          </div>
        </template>
      </UModal>
    </template>
  </UDashboardPanel>
</template>
