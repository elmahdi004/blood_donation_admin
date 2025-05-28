<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { Bar, Pie, Line } from "vue-chartjs";
import { Chart, registerables } from "chart.js";
import api from "../plugins/axios";

Chart.register(...registerables);

// 1. Blood Stock by Blood Group
const stockData = ref<any[]>([]);
const stockLoading = ref(true);
const stockError = ref<string | null>(null);
const selectedBloodGroup = ref<string>('all');

// 2. Appointments per Month
const appointmentsData = ref<any[]>([]);
const appointmentsLoading = ref(true);
const appointmentsError = ref<string | null>(null);
const filterStart = ref("");
const filterEnd = ref("");
const appointmentsChartType = ref<"bar" | "line">("bar");

// 3. Slot Usage
const slotsData = ref<any[]>([]);
const slotsLoading = ref(true);
const slotsError = ref<string | null>(null);

// 4. Appointments by Status
const appointmentStatusChartType = ref<"pie" | "bar">("pie");

const chartTypeOptions = ref([
  { label: "Barres", value: "bar" },
  { label: "Lignes", value: "line" },
]);
const statusChartTypeOptions = ref([
  { label: "Camembert", value: "pie" },
  { label: "Barres", value: "bar" },
]);

const staticBloodGroups = ref([
  { label: 'Tous', value: 'all' },
  { label: 'A+', value: 'A+' },
  { label: 'A-', value: 'A-' },
  { label: 'B+', value: 'B+' },
  { label: 'B-', value: 'B-' },
  { label: 'AB+', value: 'AB+' },
  { label: 'AB-', value: 'AB-' },
  { label: 'O+', value: 'O+' },
  { label: 'O-', value: 'O-' }
]);

// Add bloodGroups computed property
const bloodGroups = computed(() => {
  return stockData.value.map(item => item.groupe_sanguin).filter(Boolean);
});

onMounted(async () => {
  // Stock
  try {
    const response = await api.get("/stock");
    stockData.value = response.data;
    console.log("stockData", stockData.value);
  } catch (err) {
    stockError.value = "Erreur lors du chargement du stock";
  } finally {
    stockLoading.value = false;
    console.log("bloodGroups", bloodGroups.value);
  }

  // Appointments
  try {
    const response = await api.get("/rendez-vous");
    appointmentsData.value = response.data;
  } catch (err) {
    appointmentsError.value = "Erreur lors du chargement des rendez-vous";
  } finally {
    appointmentsLoading.value = false;
  }

  // Slots
  try {
    const response = await api.get("/slots");
    slotsData.value = response.data;
  } catch (err) {
    slotsError.value = "Erreur lors du chargement des créneaux";
  } finally {
    slotsLoading.value = false;
  }
});

const stockChartData = computed(() => ({
  labels: stockData.value.map((s) => s.groupe_sanguin),
  datasets: [
    {
      label: "Quantité",
      backgroundColor: "#3b82f6",
      data: stockData.value.map((s) => Number(s.quantite) || 0),
    },
  ],
}));

const filteredAppointments = computed(() => {
  if (!filterStart.value && !filterEnd.value) return appointmentsData.value;
  return appointmentsData.value.filter((a) => {
    const date = a.created_at ? a.created_at.substring(0, 10) : "";
    return (
      (!filterStart.value || date >= filterStart.value) &&
      (!filterEnd.value || date <= filterEnd.value)
    );
  });
});

const appointmentsChartData = computed(() => {
  const counts: Record<string, number> = {};
  filteredAppointments.value.forEach((a) => {
    const date = a.created_at ? a.created_at.substring(0, 7) : "Inconnu";
    counts[date] = (counts[date] || 0) + 1;
  });
  const labels = Object.keys(counts).sort();
  return {
    labels,
    datasets: [
      {
        label: "Rendez-vous",
        backgroundColor: "#f59e42",
        borderColor: "#f59e42",
        fill: appointmentsChartType.value === "bar",
        data: labels.map((l) => counts[l] ?? 0),
      },
    ],
  };
});

const slotUsageChartData = computed(() => {
  let reserved = 0;
  let available = 0;
  slotsData.value.forEach((slot) => {
    if (slot.rendez_vous_id) reserved++;
    else available++;
  });
  return {
    labels: ["Réservés", "Disponibles"],
    datasets: [
      {
        label: "Utilisation des créneaux",
        backgroundColor: ["#ef4444", "#22c55e"],
        data: [reserved, available],
      },
    ],
  };
});

const appointmentsByStatusChartData = computed(() => {
  const counts: Record<string, number> = {};
  appointmentsData.value.forEach((a) => {
    const status = a.status || "Inconnu";
    counts[status] = (counts[status] || 0) + 1;
  });
  const labels = Object.keys(counts);
  return {
    labels,
    datasets: [
      {
        label: "Rendez-vous par statut",
        backgroundColor: [
          "#f59e42",
          "#3b82f6",
          "#ef4444",
          "#22c55e",
          "#a855f7",
          "#fbbf24",
        ],
        data: labels.map((l) => counts[l] ?? 0),
      },
    ],
  };
});
</script>

<template>
  <UDashboardPanel id="charts">
    <template #header>
      <UDashboardNavbar title="Statistiques du Centre de Sang">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>
    <template #body>
      <UContainer class="min-h-screen">
        <div class="space-y-8 py-8">
          <!-- 1. Stock par groupe sanguin -->
          <UCard>
            <template #header>
              <div class="flex items-center gap-4">
                <h2 class="text-xl font-semibold">Stock par groupe sanguin</h2>
                <USelect v-model="selectedBloodGroup" :items="staticBloodGroups" placeholder="Filtrer par groupe sanguin" class="w-48" />
              </div>
            </template>
            <div v-if="stockLoading">Chargement...</div>
            <div v-else-if="stockError">{{ stockError }}</div>
            <div v-else>
              <Bar :data="stockChartData" />
            </div>
          </UCard>
          <!-- 2. Rendez-vous par mois -->
          <UCard>
            <template #header>
              <div class="flex flex-col md:flex-row md:items-center gap-4">
                <h2 class="text-xl font-semibold">Rendez-vous par mois</h2>
                <div class="flex gap-2 items-center">
                  <label
                    >Date de début: <UInput v-model="filterStart" type="date"
                  /></label>
                  <label
                    >Date de fin: <UInput v-model="filterEnd" type="date"
                  /></label>
                  <USelect
                    v-model="appointmentsChartType"
                    :items="chartTypeOptions"
                    class="w-32"
                  />
                </div>
              </div>
            </template>
            <div v-if="appointmentsLoading">Chargement...</div>
            <div v-else-if="appointmentsError">{{ appointmentsError }}</div>
            <div v-else>
              <component
                :is="appointmentsChartType === 'bar' ? Bar : Line"
                :data="appointmentsChartData"
              />
            </div>
          </UCard>
          <!-- 3. Utilisation des créneaux -->
          <UCard>
            <template #header>
              <h2 class="text-xl font-semibold">Utilisation des créneaux</h2>
            </template>
            <div v-if="slotsLoading">Chargement...</div>
            <div v-else-if="slotsError">{{ slotsError }}</div>
            <div v-else>
              <Pie :data="slotUsageChartData" />
            </div>
          </UCard>
          <!-- 4. Rendez-vous par statut -->
          <UCard>
            <template #header>
              <div class="flex items-center gap-4">
                <h2 class="text-xl font-semibold">Rendez-vous par statut</h2>
                <USelect
                  v-model="appointmentStatusChartType"
                  :items="statusChartTypeOptions"
                  class="w-32"
                />
              </div>
            </template>
            <div v-if="appointmentsLoading">Chargement...</div>
            <div v-else-if="appointmentsError">{{ appointmentsError }}</div>
            <div v-else>
              <component
                :is="appointmentStatusChartType === 'pie' ? Pie : Bar"
                :data="appointmentsByStatusChartData"
              />
            </div>
          </UCard>
        </div>
      </UContainer>
    </template>
  </UDashboardPanel>
</template>
