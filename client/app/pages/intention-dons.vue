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

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editIntention = ref(null);
const deleteIntention = ref(null);
const editForm = ref({
  groupe_sanguin: "",
  date_disponibilite: "",
  valide: false,
  notes: "",
});

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
  {
    accessorKey: "actions",
    header: "Actions",
    cell: ({ row }) => {
      const intention = row.original;
      return [
        h(
          "button",
          {
            class: "text-blue-600 hover:underline mr-2",
            onClick: () => openEditModal(intention),
            type: "button",
          },
          "Modifier"
        ),
        h(
          "button",
          {
            class: "text-red-600 hover:underline",
            onClick: () => openDeleteModal(intention),
            type: "button",
          },
          "Supprimer"
        ),
      ];
    },
    sortable: false,
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

const openEditModal = (intention) => {
  editIntention.value = intention;
  editForm.value = {
    groupe_sanguin: intention.groupe_sanguin,
    date_disponibilite: intention.date_disponibilite,
    valide: intention.valide,
    notes: intention.notes,
  };
  showEditModal.value = true;
};

const submitEdit = async () => {
  if (!editIntention.value) return;
  try {
    await api.put(`/intention-dons/${editIntention.value.id}`, editForm.value);
    showEditModal.value = false;
    fetchIntentions();
  } catch (err) {
    error.value = "Erreur lors de la modification";
  }
};

const openDeleteModal = (intention) => {
  deleteIntention.value = intention;
  showDeleteModal.value = true;
};

const submitDelete = async () => {
  if (!deleteIntention.value) return;
  try {
    await api.delete(`/intention-dons/${deleteIntention.value.id}`);
    showDeleteModal.value = false;
    fetchIntentions();
  } catch (err) {
    error.value = "Erreur lors de la suppression";
  }
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
      <UModal v-model:open="showEditModal" title="Modifier l'intention de don">
        <template #body>
          <div class="space-y-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Groupe sanguin</label
              >
              <USelect
                v-model="editForm.groupe_sanguin"
                :items="groupeSanguinOptions"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Date de disponibilité</label
              >
              <UInput v-model="editForm.date_disponibilite" type="date" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Validé</label
              >
              <USelect v-model="editForm.valide" :items="valideOptions" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Notes</label
              >
              <UInput v-model="editForm.notes" />
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
          <div>Voulez-vous vraiment supprimer cette intention de don ?</div>
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
