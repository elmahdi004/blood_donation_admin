<template>
  <div style="overflow-y: auto">
    <UDashboardPanel id="users">
      <template #header>
        <UDashboardNavbar title="Donneurs / Receveurs">
          <template #leading>
            <UDashboardSidebarCollapse />
          </template>
          <template #right>
            <UButton
              color="primary"
              icon="i-heroicons-plus"
              @click="openAddModal"
              >Ajouter un utilisateur</UButton
            >
          </template>
        </UDashboardNavbar>
      </template>
      <template #body>
        <UContainer class="min-h-screen">
          <UCard>
            <template #header>
              <h1 class="text-2xl font-bold mt-5">
                Liste des Donneurs / Receveurs
              </h1>
            </template>
            <div class="flex flex-wrap gap-4 mb-4">
              <UInput
                v-model="filters.search"
                placeholder="Rechercher un utilisateur..."
                icon="i-heroicons-magnifying-glass"
                class="w-64"
              />
              <USelect
                v-model="filters.groupe_sanguin"
                :items="bloodGroups"
                placeholder="Filtrer par groupe sanguin"
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
                Erreur lors du chargement des utilisateurs.
              </div>
              <!-- <div v-else style="max-height: 500px; overflow-y: auto"> -->
              <UTable v-else :data="filteredUsers" :columns="columns" />
              <!-- </div> -->
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

    <!-- Details Modal -->
    <UModal v-model:open="showDetailsModal" title="Détails de l'utilisateur">
      <template #body>
        <div v-if="detailsUser" class="space-y-2">
          <div><strong>Nom:</strong> {{ detailsUser.nom }}</div>
          <div><strong>Prénom:</strong> {{ detailsUser.prenom }}</div>
          <div><strong>Email:</strong> {{ detailsUser.email }}</div>
          <div><strong>Téléphone:</strong> {{ detailsUser.telephone }}</div>
          <div>
            <strong>Date de naissance:</strong>
            {{ detailsUser.date_de_naissance }}
          </div>
          <div>
            <strong>Groupe sanguin:</strong> {{ detailsUser.groupe_sanguin }}
          </div>
          <div><strong>Adresse:</strong> {{ detailsUser.adresse }}</div>
          <div><strong>Statut:</strong> {{ detailsUser.status }}</div>
          <div>
            <strong>Date de création:</strong>
            {{ new Date(detailsUser.created_at).toLocaleString("fr-FR") }}
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

    <UModal v-model:open="showEditModal" title="Modifier l'utilisateur">
      <template #body>
        <div v-if="editUser" class="space-y-2">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Nom</label
            >
            <UInput v-model="editForm.nom" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Prénom</label
            >
            <UInput v-model="editForm.prenom" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Email</label
            >
            <UInput v-model="editForm.email" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Téléphone</label
            >
            <UInput v-model="editForm.telephone" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Date de naissance</label
            >
            <UInput v-model="editForm.date_de_naissance" type="date" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Groupe sanguin</label
            >
            <USelect v-model="editForm.groupe_sanguin" :items="bloodGroups" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Adresse</label
            >
            <UInput v-model="editForm.adresse" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Statut</label
            >
            <USelect v-model="editForm.status" :items="statusOptions" />
          </div>
        </div>
      </template>
      <template #footer>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" @click="closeEditModal"
            >Annuler</UButton
          >
          <UButton color="primary" @click="submitEdit">Enregistrer</UButton>
        </div>
      </template>
    </UModal>

    <UModal v-model:open="showDeleteModal" title="Confirmer la suppression">
      <template #body>
        <div v-if="deleteUser">
          Voulez-vous vraiment supprimer l'utilisateur
          <strong>{{ deleteUser.nom }} {{ deleteUser.prenom }}</strong> ?
        </div>
      </template>
      <template #footer>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" @click="closeDeleteModal"
            >Annuler</UButton
          >
          <UButton color="error" @click="submitDelete">Supprimer</UButton>
        </div>
      </template>
    </UModal>

    <UModal v-model:open="showAddModal" title="Ajouter un utilisateur">
      <template #body>
        <div class="space-y-2">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Nom</label
            >
            <UInput v-model="addForm.nom" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Prénom</label
            >
            <UInput v-model="addForm.prenom" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Email</label
            >
            <UInput v-model="addForm.email" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Téléphone</label
            >
            <UInput v-model="addForm.telephone" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Date de naissance</label
            >
            <UInput v-model="addForm.date_de_naissance" type="date" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Groupe sanguin</label
            >
            <USelect v-model="addForm.groupe_sanguin" :items="bloodGroups" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Adresse</label
            >
            <UInput v-model="addForm.adresse" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Statut</label
            >
            <USelect v-model="addForm.status" :items="statusOptions" />
          </div>
        </div>
      </template>
      <template #footer>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" @click="closeAddModal"
            >Annuler</UButton
          >
          <UButton color="primary" @click="submitAdd">Ajouter</UButton>
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

const UButton = resolveComponent("UButton");

interface User {
  id: number;
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  date_de_naissance: string;
  groupe_sanguin: string;
  adresse: string;
  status: string;
  created_at: string;
}

const toast = useToast();
const loading = ref(false);
const error = ref<string | null>(null);
const page = ref(1);
const perPage = ref(10);
const total = ref(0);
const showDetailsModal = ref(false);
const detailsUser = ref<User | null>(null);
const showEditModal = ref(false);
const editUser = ref<User | null>(null);
const editForm = ref({
  nom: "",
  prenom: "",
  email: "",
  telephone: "",
  date_de_naissance: "",
  groupe_sanguin: "",
  adresse: "",
  status: "",
});
const showDeleteModal = ref(false);
const deleteUser = ref<User | null>(null);
const showAddModal = ref(false);
const addForm = ref({
  nom: "",
  prenom: "",
  email: "",
  telephone: "",
  date_de_naissance: "",
  groupe_sanguin: "",
  adresse: "",
  status: "active",
});

const filters = ref({
  search: "",
  groupe_sanguin: "",
  status: "",
});

const bloodGroups = [
  { label: "A+", value: "A+" },
  { label: "A-", value: "A-" },
  { label: "B+", value: "B+" },
  { label: "B-", value: "B-" },
  { label: "AB+", value: "AB+" },
  { label: "AB-", value: "AB-" },
  { label: "O+", value: "O+" },
  { label: "O-", value: "O-" },
];

const statusOptions = [
  { label: "Actif", value: "active" },
  { label: "Inactif", value: "inactive" },
  { label: "En attente", value: "pending" },
];

const users = ref<User[]>([]);

const filteredUsers = computed(() => {
  return users.value.filter((user) => {
    const matchesSearch =
      !filters.value.search ||
      user.nom.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      user.prenom.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      user.email.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      user.telephone.toLowerCase().includes(filters.value.search.toLowerCase());
    const matchesGroup =
      !filters.value.groupe_sanguin ||
      user.groupe_sanguin === filters.value.groupe_sanguin;
    const matchesStatus =
      !filters.value.status || user.status === filters.value.status;
    return matchesSearch && matchesGroup && matchesStatus;
  });
});

const columns = [
  { accessorKey: "nom", header: "Nom" },
  { accessorKey: "prenom", header: "Prénom" },
  { accessorKey: "email", header: "Email" },
  { accessorKey: "telephone", header: "Téléphone" },
  { accessorKey: "groupe_sanguin", header: "Groupe sanguin" },
  { accessorKey: "status", header: "Statut" },
  {
    accessorKey: "actions",
    header: "Actions",
    cell: ({ row }: any) => {
      const user = row.original;
      return h("div", { class: "flex gap-2 flex-wrap" }, [
        h(
          "button",
          {
            class: "text-gray-600 hover:underline",
            onClick: () => openDetailsModal(user),
            type: "button",
          },
          "Détails"
        ),
        h(
          "button",
          {
            class: "text-blue-600 hover:underline",
            onClick: () => openEditModal(user),
            type: "button",
          },
          "Modifier"
        ),
        h(
          "button",
          {
            class: "text-red-600 hover:underline",
            onClick: () => confirmDelete(user),
            type: "button",
          },
          "Supprimer"
        ),
      ]);
    },
    sortable: false,
  },
];

const openDetailsModal = (user: User) => {
  detailsUser.value = user;
  showDetailsModal.value = true;
};

const openEditModal = (user: User) => {
  editUser.value = user;
  editForm.value = { ...user };
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  editUser.value = null;
};

const submitEdit = async () => {
  if (!editUser.value) return;
  try {
    await api.put(`/users/${editUser.value.id}`, editForm.value);
    toast.success("Utilisateur mis à jour");
    showEditModal.value = false;
    fetchUsers();
  } catch (err: any) {
    toast.error(
      err?.response?.data?.message || "Erreur lors de la mise à jour"
    );
  }
};

const confirmDelete = (user: User) => {
  deleteUser.value = user;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  deleteUser.value = null;
};

const submitDelete = async () => {
  if (!deleteUser.value) return;
  try {
    await api.delete(`/users/${deleteUser.value.id}`);
    toast.success("Utilisateur supprimé");
    showDeleteModal.value = false;
    fetchUsers();
  } catch (err: any) {
    toast.error("Erreur lors de la suppression");
  }
};

const fetchUsers = async () => {
  loading.value = true;
  try {
    const params: any = {
      page: page.value,
      per_page: perPage.value,
      ...filters.value,
    };
    Object.keys(params).forEach((key) => {
      if (params[key] === "") delete params[key];
    });
    const response = await api.get("/users", { params });
    users.value = response.data.data || response.data;
    total.value = response.data.total || users.value.length;
  } catch (err: any) {
    error.value = err?.message || "Erreur inconnue";
    toast.error("Erreur lors du chargement des utilisateurs");
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  filters.value.search = "";
  filters.value.groupe_sanguin = "";
  filters.value.status = "";
};

const openAddModal = () => {
  addForm.value = {
    nom: "",
    prenom: "",
    email: "",
    telephone: "",
    date_de_naissance: "",
    groupe_sanguin: "",
    adresse: "",
    status: "active",
  };
  showAddModal.value = true;
};

const closeAddModal = () => {
  showAddModal.value = false;
};

const submitAdd = async () => {
  try {
    await api.post("/users", addForm.value);
    toast.success("Utilisateur ajouté");
    showAddModal.value = false;
    fetchUsers();
  } catch (err: any) {
    toast.error(err?.response?.data?.message || "Erreur lors de l'ajout");
  }
};

onMounted(fetchUsers);
watch([page, perPage], fetchUsers, { deep: true });
watch(filters, fetchUsers, { deep: true });
</script>
