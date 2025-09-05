<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable no-unused-vars -->
<script setup>
import { sweetAlert } from "@/helpers/swalHelper";
import { ref, onMounted, watch } from "vue";
import { Chart } from "chart.js/auto";
import { useDashboardStore } from "@/stores/dashboard";
import { useTicketStore } from "@/stores/ticket";
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { capitalize } from "lodash";
import { DateTime } from "luxon";
import feather from "feather-icons";

const authStore = useAuthStore();

const dashboardStore = useDashboardStore();
const { statistic } = storeToRefs(dashboardStore);
const { fetchStatistics } = dashboardStore;

const ticketStore = useTicketStore();
const { tickets } = storeToRefs(ticketStore);
const { fetchTickets } = ticketStore;

const toggleTicketMenu = (ticket) => {
  ticket.showMenu = !ticket.showMenu;
}

let chart = null;
watch(statistic, () => {
  if (statistic.value && chart) {
    chart.data.datasets[0].data = [
      statistic.value?.dataPerStatus?.open,
      statistic.value?.dataPerStatus?.inProgress,
      statistic.value?.dataPerStatus?.completed,
      statistic.value?.dataPerStatus?.rejected,
    ];
    chart.update();
  }
}, { deep: true });

onMounted(async () => {
  await fetchTickets();
  await fetchStatistics();

  // if (authStore.justLoggedIn) {
  //   sweetAlert("Berhasil!", "Selamat datang di Admin Dashboard!.", "success");
  // }

  const statusCtx = document.getElementById('statusChart')?.getContext('2d')
  if (statusCtx && statistic.value) {
    chart = new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: ['Open', 'In Progress', 'Completed', 'Rejected'],
        datasets: [{
          data: [
            statistic.value?.dataPerStatus?.open,
            statistic.value?.dataPerStatus?.inProgress,
            statistic.value?.dataPerStatus?.completed,
            statistic.value?.dataPerStatus?.rejected,
          ],
          backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#EF4444']
        }],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
          }
        },
        cutout: '70%',
      }
    })
  }

  feather.replace();
});
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800">
      Halo, {{ authStore.user?.name }} ! Selamat datang ^_^
    </h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
      <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Total Tiket</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ statistic?.totalTickets }}</h3>
          </div>
          <div class="p-3 bg-blue-50 rounded-lg">
            <i data-feather="tag" class="w-6 h-6 text-blue-600"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span class="text-green-500 flex items-center">
            <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
            12%
          </span>
          <span class="text-gray-500 ml-2">vs bulan lalu</span>
        </div>
      </div>

      <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Tiket Aktif</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ statistic?.activeTickets }}</h3>
          </div>
          <div class="p-3 bg-yellow-50 rounded-lg">
            <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span class="text-red-500 flex items-center">
            <i data-feather="trending-down" class="w-4 h-4 mr-1"></i>
            8%
          </span>
          <span class="text-gray-500 ml-2">vs bulan lalu</span>
        </div>
      </div>

      <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Tiket Selesai</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ statistic?.completedTickets }}</h3>
          </div>
          <div class="p-3 bg-green-50 rounded-lg">
            <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span class="text-green-500 flex items-center">
            <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
            12%
          </span>
          <span class="text-gray-500 ml-2">vs bulan lalu</span>
        </div>
      </div>

      <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Rata-rata waktu penyelesaian</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ statistic?.avgCompletionTime }}</h3>
          </div>
          <div class="p-3 bg-purple-50 rounded-lg">
            <i data-feather="clock" class="w-6 h-6 text-purple-600"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
          <span class="text-green-500 flex items-center">
            <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
            10%
          </span>
          <span class="text-gray-500 ml-2">vs bulan lalu</span>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Ticket List Section -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Tiket Terbaru</h2>
            <button class="text-blue-600 text-sm font-medium flex items-center">
              Lihat Semua
              <i data-feather="chevron-right" class="w-4 h-4 ml-1"></i>
            </button>
          </div>

          <div class="divide-y divide-gray-100">
            <!-- Ticket Item -->
            <div v-for="ticket in tickets" :key="ticket.id" class="p-6 ticket-card relative">
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <h3 class="font-medium text-gray-800 text-lg mb-2">
                    {{ ticket.title }}
                  </h3>
                  <div class="flex items-center mb-3">
                    <span class="text-sm text-gray-500">#{{ ticket.code }}</span>
                  </div>
                  <span class="status-badge bg-blue-100 text-blue-800 rounded-full font-medium">
                    {{ ticket.status.name }}
                  </span>
                </div>

                <div class="text-right">
                  <p class="text-sm text-gray-500 mb-4">{{ ticket.time }}</p>
                  <button @click="toggleTicketMenu(ticket)" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-feather="more-vertical" class="w-5 h-5"></i>
                  </button>

                  <!-- Dropdown Menu -->
                  <div v-if="ticket.showMenu"
                    class="absolute right-6 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Edit Tiket</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Ubah Status</a>
                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Hapus Tiket</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Status Distribution Section -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Keterangan</h2>

        <div class="mb-6 h-64 flex items-center justify-center">
          <canvas id="statusChart"></canvas>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
              <span class="text-sm text-gray-600">Open</span>
            </div>
            <span class="text-sm font-medium text-gray-800">{{ statistic?.dataPerStatus?.open }}</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
              <span class="text-sm text-gray-600">On Progress</span>
            </div>
            <span class="text-sm font-medium text-gray-800">{{ statistic?.dataPerStatus?.inProgress }}</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
              <span class="text-sm text-gray-600">Resolved</span>
            </div>
            <span class="text-sm font-medium text-gray-800">{{ statistic?.dataPerStatus?.completed }}</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
              <span class="text-sm text-gray-600">Rejected</span>
            </div>
            <span class="text-sm font-medium text-gray-800">{{ statistic?.dataPerStatus?.rejected }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
