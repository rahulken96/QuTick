<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable no-unused-vars -->
<script setup>
import { sweetAlert } from "@/helpers/swalHelper";
import { ref, onMounted, watch } from "vue";
import { Chart } from "chart.js/auto";
import { useDashboardStore } from "@/stores/dashboard";
import { useTicketStore } from "@/stores/ticket";
import { useAuthStore } from "@/stores/auth";
import { axiosInstance } from "@/plugins/axios";
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

const showExportModal = ref(false)
const exportDates = ref({
  start_date: '',
  end_date: ''
})
const exporting = ref(false)

const handleExportPDF = async () => {
  exporting.value = true
  try {
    const response = await axiosInstance.get('/report/export', {
      params: exportDates.value,
      responseType: 'blob'
    })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `laporan_tiket_${DateTime.now().toFormat('yyyyMMdd')}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    showExportModal.value = false
    sweetAlert("Berhasil!", "Laporan PDF berhasil diunduh.", "success")
  } catch (err) {
    console.error("Export PDF failed:", err)
    sweetAlert("Gagal!", "Gagal mengekspor laporan PDF.", "error")
  } finally {
    exporting.value = false
  }
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
          backgroundColor: ['#60a5fa', '#fbbf24', '#34d399', '#f87171'] // Soft colors
        }],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#475569',
              font: {
                family: 'system-ui'
              }
            }
          }
        },
        cutout: '72%',
      }
    })
  }

  feather.replace();
});
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">
          Halo, {{ authStore.user?.name }}!
        </h1>
        <p class="text-sm text-slate-500 mt-1">Selamat datang di Panel Admin QuTick</p>
      </div>
      <button 
        @click="showExportModal = true" 
        class="inline-flex items-center px-4 py-2.5 border border-slate-200 text-sm font-semibold rounded-xl text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition-all"
      >
        <i data-feather="download" class="w-4 h-4 mr-2 text-slate-500"></i>
        Ekspor Laporan PDF
      </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl shadow-sm shadow-slate-100/50 p-6 border border-slate-100/80 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500">Total Tiket</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ statistic?.totalTickets }}</h3>
          </div>
          <div class="p-3 bg-blue-50/50 text-blue-500 rounded-2xl border border-blue-100/50">
            <i data-feather="tag" class="w-6 h-6"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-slate-500 font-medium">
          <span class="text-emerald-500 flex items-center mr-1.5 font-bold">
            <i data-feather="trending-up" class="w-3.5 h-3.5 mr-0.5"></i>
            12%
          </span>
          vs bulan lalu
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm shadow-slate-100/50 p-6 border border-slate-100/80 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500">Tiket Aktif</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ statistic?.activeTickets }}</h3>
          </div>
          <div class="p-3 bg-amber-50/50 text-amber-500 rounded-2xl border border-amber-100/50">
            <i data-feather="clock" class="w-6 h-6"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-slate-500 font-medium">
          <span class="text-rose-500 flex items-center mr-1.5 font-bold">
            <i data-feather="trending-down" class="w-3.5 h-3.5 mr-0.5"></i>
            8%
          </span>
          vs bulan lalu
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm shadow-slate-100/50 p-6 border border-slate-100/80 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500">Tiket Selesai</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ statistic?.completedTickets }}</h3>
          </div>
          <div class="p-3 bg-emerald-50/50 text-emerald-500 rounded-2xl border border-emerald-100/50">
            <i data-feather="check-circle" class="w-6 h-6"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-slate-500 font-medium">
          <span class="text-emerald-500 flex items-center mr-1.5 font-bold">
            <i data-feather="trending-up" class="w-3.5 h-3.5 mr-0.5"></i>
            12%
          </span>
          vs bulan lalu
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm shadow-slate-100/50 p-6 border border-slate-100/80 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500">Rata-rata Resolusi</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ statistic?.avgCompletionTime }} jam</h3>
          </div>
          <div class="p-3 bg-indigo-50/50 text-indigo-500 rounded-2xl border border-indigo-100/50">
            <i data-feather="activity" class="w-6 h-6"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-slate-500 font-medium">
          <span class="text-emerald-500 flex items-center mr-1.5 font-bold">
            <i data-feather="trending-up" class="w-3.5 h-3.5 mr-0.5"></i>
            10%
          </span>
          vs bulan lalu
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Ticket List Section -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-slate-800">Tiket Terbaru</h2>
            <RouterLink :to="{ name: 'admin.ticket' }" class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold flex items-center transition-colors">
              Lihat Semua
              <i data-feather="chevron-right" class="w-4 h-4 ml-1"></i>
            </RouterLink>
          </div>

          <div class="divide-y divide-slate-50">
            <!-- Ticket Item -->
            <div v-for="ticket in tickets" :key="ticket.id" class="p-6 relative hover:bg-slate-50/50 transition-colors">
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <h3 class="font-semibold text-slate-800 text-lg mb-2">
                    {{ ticket.title }}
                  </h3>
                  <div class="flex items-center space-x-2 mb-3">
                    <span class="text-sm font-mono text-slate-400">#{{ ticket.code }}</span>
                  </div>
                  <span class="px-3 py-1 text-xs font-semibold rounded-full" :class="{
                    'text-blue-600 bg-blue-50 border border-blue-100': ticket.status.number == 0,
                    'text-amber-600 bg-amber-50 border border-amber-100': ticket.status.number == 1,
                    'text-emerald-600 bg-emerald-50 border border-emerald-100': ticket.status.number == 2,
                    'text-rose-600 bg-rose-50 border border-rose-100': ticket.status.number == 3,
                  }">
                    {{ ticket.status.name }}
                  </span>
                </div>

                <div class="text-right">
                  <p class="text-xs text-slate-400 mb-4">{{ ticket.time }}</p>
                  <button @click="toggleTicketMenu(ticket)" class="text-slate-400 hover:text-slate-600 transition-colors p-1">
                    <i data-feather="more-vertical" class="w-5 h-5"></i>
                  </button>

                  <!-- Dropdown Menu -->
                  <div v-if="ticket.showMenu"
                    class="absolute right-6 mt-1 w-44 bg-white rounded-xl shadow-lg py-1.5 z-10 border border-slate-100">
                    <RouterLink :to="{ name: 'admin.ticket.detail', params: { code: ticket.code } }" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Detail Tiket</RouterLink>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Status Distribution Section -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Status Tiket</h2>

        <div class="mb-6 h-56 flex items-center justify-center">
          <canvas id="statusChart"></canvas>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-blue-400 rounded-full mr-2.5"></span>
              <span class="text-sm font-medium text-slate-600">Open</span>
            </div>
            <span class="text-sm font-bold text-slate-800">{{ statistic?.dataPerStatus?.open }}</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-amber-400 rounded-full mr-2.5"></span>
              <span class="text-sm font-medium text-slate-600">On Progress</span>
            </div>
            <span class="text-sm font-bold text-slate-800">{{ statistic?.dataPerStatus?.inProgress }}</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-emerald-400 rounded-full mr-2.5"></span>
              <span class="text-sm font-medium text-slate-600">Resolved</span>
            </div>
            <span class="text-sm font-bold text-slate-800">{{ statistic?.dataPerStatus?.completed }}</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-rose-400 rounded-full mr-2.5"></span>
              <span class="text-sm font-medium text-slate-600">Rejected</span>
            </div>
            <span class="text-sm font-bold text-slate-800">{{ statistic?.dataPerStatus?.rejected }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Modal (Soft light aesthetic) -->
    <div v-if="showExportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/30 backdrop-blur-sm">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-xl w-full max-w-md p-6 relative">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Ekspor Laporan PDF</h3>
        <p class="text-sm text-slate-500 mb-6">Pilih rentang tanggal tiket untuk diekspor ke file PDF.</p>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Tanggal Mulai</label>
            <input 
              type="date" 
              v-model="exportDates.start_date" 
              class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Tanggal Akhir</label>
            <input 
              type="date" 
              v-model="exportDates.end_date" 
              class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
            />
          </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
          <button 
            @click="showExportModal = false" 
            class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 rounded-xl border border-slate-200 transition-colors"
          >
            Batal
          </button>
          <button 
            @click="handleExportPDF" 
            :disabled="exporting"
            class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 rounded-xl transition-colors"
          >
            {{ exporting ? 'Mengekspor...' : 'Ekspor PDF' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
