<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { Bar, Pie, Line } from "vue-chartjs";
import { Chart, registerables } from "chart.js";
import api from "../plugins/axios";

Chart.register(...registerables);

// Admin Statistics
const adminData = ref<any[]>([]);
const adminLoading = ref(true);
const adminError = ref<string | null>(null);

// Request Statistics
const requestsData = ref<any[]>([]);
const requestsLoading = ref(true);
const requestsError = ref<string | null>(null);
const requestChartType = ref<"bar" | "line">("bar");

// Stock Statistics
const stockData = ref<any[]>([]);
const stockLoading = ref(true);
const stockError = ref<string | null>(null);

// Intention Don Statistics
const intentionsData = ref<any[]>([]);
const intentionsLoading = ref(true);
const intentionsError = ref<string | null>(null);
const intentionChartType = ref<"bar" | "line">("bar");

// Center Performance Statistics
const performanceData = ref<any[]>([]);
const performanceLoading = ref(true);
const performanceError = ref<string | null>(null);

// Blood Group Distribution Statistics
const distributionData = ref<any[]>([]);
const distributionLoading = ref(true);
const distributionError = ref<string | null>(null);

const chartTypeOptions = ref([
  { label: "Barres", value: "bar" },
  { label: "Lignes", value: "line" },
]);

onMounted(async () => {
  // Load Requests Data
  try {
    const response = await api.get("/admin/requests/stats");
    requestsData.value = response.data;
  } catch (err) {
    requestsError.value =
      "Erreur lors du chargement des statistiques des demandes";
  } finally {
    requestsLoading.value = false;
  }

  // Load Stock Data
  try {
    const response = await api.get("/admin/stock/stats");
    stockData.value = response.data;
  } catch (err) {
    stockError.value = "Erreur lors du chargement des statistiques du stock";
  } finally {
    stockLoading.value = false;
  }

  // Load Intentions Data
  try {
    const response = await api.get("/admin/intentions/stats");
    intentionsData.value = response.data;
  } catch (err) {
    intentionsError.value =
      "Erreur lors du chargement des statistiques des intentions de don";
  } finally {
    intentionsLoading.value = false;
  }

  // Load Performance Data
  try {
    const response = await api.get("/admin/performance/stats");
    performanceData.value = response.data;
  } catch (err) {
    performanceError.value =
      "Erreur lors du chargement des statistiques de performance";
  } finally {
    performanceLoading.value = false;
  }

  // Load Distribution Data
  try {
    const response = await api.get("/admin/blood-group-distribution/stats");
    distributionData.value = response.data;
  } catch (err) {
    distributionError.value =
      "Erreur lors du chargement des statistiques de distribution";
  } finally {
    distributionLoading.value = false;
  }
});

// Requests by Day Chart
const requestsByDayChartData = computed(() => {
  const counts: Record<string, number> = {};
  requestsData.value.forEach((request) => {
    const date = request.created_at
      ? request.created_at.substring(0, 10)
      : "Inconnu";
    counts[date] = (counts[date] || 0) + 1;
  });
  const labels = Object.keys(counts).sort();
  return {
    labels,
    datasets: [
      {
        label: "Demandes par jour",
        backgroundColor: "#3b82f6",
        borderColor: "#3b82f6",
        fill: requestChartType.value === "bar",
        data: labels.map((l) => counts[l] ?? 0),
      },
    ],
  };
});

// Stock by Center Chart
const stockByCenterChartData = computed(() => {
  const centerData: Record<string, Record<string, number>> = {};

  stockData.value.forEach((item) => {
    const center = item.center_name || "Inconnu";
    const bloodGroup = item.groupe_sanguin || "Inconnu";

    if (!centerData[center]) {
      centerData[center] = {};
    }
    centerData[center][bloodGroup] =
      (centerData[center][bloodGroup] || 0) + Number(item.quantite);
  });

  const bloodGroups = Array.from(
    new Set(stockData.value.map((item) => item.groupe_sanguin))
  ).sort();
  const centers = Object.keys(centerData);

  return {
    labels: centers,
    datasets: bloodGroups.map((group, index) => ({
      label: group,
      backgroundColor: [
        "#3b82f6",
        "#ef4444",
        "#22c55e",
        "#f59e0b",
        "#8b5cf6",
        "#ec4899",
        "#14b8a6",
        "#fbbf24",
      ][index % 8],
      data: centers.map((center) => centerData[center][group] || 0),
    })),
  };
});

// Request Status Distribution
const requestStatusChartData = computed(() => {
  const statusCounts: Record<string, number> = {};
  requestsData.value.forEach((request) => {
    const status = request.status || "Inconnu";
    statusCounts[status] = (statusCounts[status] || 0) + 1;
  });
  const labels = Object.keys(statusCounts);
  return {
    labels,
    datasets: [
      {
        label: "Statut des demandes",
        backgroundColor: [
          "#3b82f6", // En attente
          "#22c55e", // Approuvé
          "#ef4444", // Rejeté
          "#f59e0b", // En cours
          "#8b5cf6", // Terminé
        ],
        data: labels.map((l) => statusCounts[l] ?? 0),
      },
    ],
  };
});

// Intentions by Day Chart
const intentionsByDayChartData = computed(() => {
  const counts: Record<string, number> = {};
  intentionsData.value.forEach((intention) => {
    const date = intention.date;
    counts[date] = intention.count;
  });
  const labels = Object.keys(counts).sort();
  return {
    labels,
    datasets: [
      {
        label: "Intentions de don par jour",
        backgroundColor: "#10b981",
        borderColor: "#10b981",
        fill: intentionChartType.value === "bar",
        data: labels.map((l) => counts[l] ?? 0),
      },
    ],
  };
});

// Center Performance Chart
const centerPerformanceChartData = computed(() => {
  return {
    labels: performanceData.value.map((item) => item.center_name),
    datasets: [
      {
        label: "Donations totales",
        backgroundColor: "#3b82f6",
        data: performanceData.value.map((item) => item.total_donations),
      },
      {
        label: "Donations complétées",
        backgroundColor: "#22c55e",
        data: performanceData.value.map((item) => item.completed_donations),
      },
    ],
  };
});

// Blood Group Distribution Chart
const bloodGroupDistributionChartData = computed(() => {
  return {
    labels: distributionData.value.map((item) => item.groupe_sanguin),
    datasets: [
      {
        label: "Quantité totale",
        backgroundColor: [
          "#3b82f6",
          "#ef4444",
          "#22c55e",
          "#f59e0b",
          "#8b5cf6",
          "#ec4899",
          "#14b8a6",
          "#fbbf24",
        ],
        data: distributionData.value.map((item) => item.total_quantity),
      },
    ],
  };
});
</script>

<template>
  <UDashboardPanel id="charts-admin">
    <template #header>
      <UDashboardNavbar title="Statistiques Administrateur">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>
    <template #body>
      <UContainer class="min-h-screen">
        <div class="space-y-8 py-8">
          <!-- Requests by Day Chart -->
          <UCard>
            <template #header>
              <div class="flex flex-col md:flex-row md:items-center gap-4">
                <h2 class="text-xl font-semibold">Demandes par jour</h2>
                <div class="flex gap-2 items-center">
                  <USelect
                    v-model="requestChartType"
                    :items="chartTypeOptions"
                    class="w-32"
                  />
                </div>
              </div>
            </template>
            <div v-if="requestsLoading">Chargement...</div>
            <div v-else-if="requestsError">{{ requestsError }}</div>
            <div v-else>
              <component
                :is="requestChartType === 'bar' ? Bar : Line"
                :data="requestsByDayChartData"
              />
            </div>
          </UCard>

          <!-- Stock by Center Chart -->
          <UCard>
            <template #header>
              <h2 class="text-xl font-semibold">Stock par centre</h2>
            </template>
            <div v-if="stockLoading">Chargement...</div>
            <div v-else-if="stockError">{{ stockError }}</div>
            <div v-else>
              <Bar :data="stockByCenterChartData" />
            </div>
          </UCard>

          <!-- Request Status Distribution -->
          <UCard>
            <template #header>
              <h2 class="text-xl font-semibold">
                Répartition des statuts des demandes
              </h2>
            </template>
            <div v-if="requestsLoading">Chargement...</div>
            <div v-else-if="requestsError">{{ requestsError }}</div>
            <div v-else>
              <Pie :data="requestStatusChartData" />
            </div>
          </UCard>

          <!-- Intentions de Don Chart -->
          <UCard>
            <template #header>
              <div class="flex flex-col md:flex-row md:items-center gap-4">
                <h2 class="text-xl font-semibold">
                  Intentions de don par jour
                </h2>
                <div class="flex gap-2 items-center">
                  <USelect
                    v-model="intentionChartType"
                    :items="chartTypeOptions"
                    class="w-32"
                  />
                </div>
              </div>
            </template>
            <div v-if="intentionsLoading">Chargement...</div>
            <div v-else-if="intentionsError">{{ intentionsError }}</div>
            <div v-else>
              <component
                :is="intentionChartType === 'bar' ? Bar : Line"
                :data="intentionsByDayChartData"
              />
            </div>
          </UCard>

          <!-- Center Performance Chart -->
          <UCard>
            <template #header>
              <h2 class="text-xl font-semibold">Performance des centres</h2>
            </template>
            <div v-if="performanceLoading">Chargement...</div>
            <div v-else-if="performanceError">{{ performanceError }}</div>
            <div v-else>
              <Bar :data="centerPerformanceChartData" />
            </div>
          </UCard>

          <!-- Blood Group Distribution Chart -->
          <UCard>
            <template #header>
              <h2 class="text-xl font-semibold">
                Distribution des groupes sanguins
              </h2>
            </template>
            <div v-if="distributionLoading">Chargement...</div>
            <div v-else-if="distributionError">{{ distributionError }}</div>
            <div v-else>
              <Pie :data="bloodGroupDistributionChartData" />
            </div>
          </UCard>
        </div>
      </UContainer>
    </template>
  </UDashboardPanel>
</template>
