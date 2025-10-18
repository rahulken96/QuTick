<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable no-unused-vars -->
<script setup>
import { sweetAlert } from "@/helpers/swalHelper";
import { ref, onMounted, watch } from "vue";
import { useTicketStore } from "@/stores/ticket";
import { storeToRefs } from "pinia";
import { capitalize, debounce } from "lodash";
import feather from "feather-icons";
import { DateTime } from "luxon";

const ticketStore = useTicketStore();
const { tickets, success, error, loading } = storeToRefs(ticketStore);
const { fetchMyTickets } = ticketStore;

const filters = ref({
  keyword: '',
  status: '',
  priority: '',
  date: '',
})

watch(filters, debounce(async () => {
  await fetchMyTickets(filters.value)
}, 300), { deep: true });

onMounted(async () => {
  await fetchMyTickets(filters.value);

  feather.replace()
})

</script>

<template>
  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Tiket Saya</h1>
      <p class="text-sm text-gray-500 mt-1">Kelola dan pantau status tiket Anda</p>
    </div>

    <router-link
      :to="{ name: 'app.ticket.create' }"
      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
    >
      <i data-feather="plus" class="w-4 h-4 mr-2"></i>
      Buat Tiket Baru
    </router-link>
  </div>

  <!-- Alert sukses -->
  <div
    v-if="success"
    class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative flex items-center justify-between"
    role="alert"
  >
    <div class="flex items-center">
      <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
      <span>{{ success }}</span>
    </div>

    <button class="flex items-center justify-center" @click="success = null">
      <i data-feather="x" class="w-4 h-4 text-green-600"></i>
    </button>
  </div>

  <!-- Filter -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <!-- Search -->
        <div class="relative">
          <input
            type="text"
            placeholder="Cari tiket..."
            v-model="filters.keyword"
            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
          />
          <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
        </div>

        <!-- Status -->
        <select
          v-model="filters.status"
          class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
        >
          <option value="">Semua Status</option>
          <option value="0">Open</option>
          <option value="1">In Progress</option>
          <option value="2">Resolved</option>
          <option value="3">Rejected</option>
        </select>

        <!-- Priority -->
        <select
          v-model="filters.priority"
          class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
        >
          <option value="">Semua Prioritas</option>
          <option value="2">High</option>
          <option value="1">Medium</option>
          <option value="0">Low</option>
        </select>

        <!-- Date -->
        <!-- <select
          v-model="filters.date"
          class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
        >
          <option value="">Semua Tanggal</option>
          <option value="today">Hari Ini</option>
          <option value="week">Minggu Ini</option>
          <option value="month">Bulan Ini</option>
        </select> -->
      </div>
    </div>
  </div>

  <!-- Skeleton Loading -->
  <div v-if="loading" class="space-y-4">
    <div
      v-for="n in 4"
      :key="n"
      class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-pulse"
    >
      <div class="h-5 bg-gray-200 rounded w-1/3 mb-4"></div>
      <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
      <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
      <div class="flex space-x-3 mt-4">
        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
      </div>
    </div>
  </div>

  <!-- Data Tiket -->
  <div v-else class="space-y-4">
    <div
      class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow"
      v-for="ticket in tickets"
      :key="ticket.code"
    >
      <router-link
        :to="{ name: 'app.ticket.detail', params: { code: ticket.code } }"
        class="block p-6"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center space-x-3">
              <h3 class="text-lg font-semibold text-gray-800">
                {{ ticket.title }}
              </h3>
              <span
                class="px-3 py-1 text-xs font-medium rounded-full"
                :class="{
                  'text-blue-700 bg-blue-100': ticket.status.number == 0,
                  'text-yellow-700 bg-yellow-100': ticket.status.number == 1,
                  'text-green-700 bg-green-100': ticket.status.number == 2,
                  'text-red-700 bg-red-100': ticket.status.number == 3,
                }"
              >
                {{ capitalize(ticket.status.name) }}
              </span>

              <span
                class="px-3 py-1 text-xs font-medium rounded-full"
                :class="{
                  'text-red-700 bg-red-100': ticket.priority.number == 2,
                  'text-yellow-700 bg-yellow-100': ticket.priority.number == 1,
                  'text-green-700 bg-green-100': ticket.priority.number == 0,
                }"
              >
                {{ capitalize(ticket.priority.name) }}
              </span>
            </div>

            <p class="text-sm text-gray-500 mt-1">
              <span class="font-mono font-semibold">#{{ ticket.code ?? 'n/a' }}</span>
              - Dibuat pada
              <span class="font-bold">{{ ticket.created_at ?? 'n/a' }}</span>
            </p>

            <p class="text-xs font-sans text-gray-500 mt-2">
              "{{ ticket.description ?? 'n/a' }}"
            </p>

            <div class="mt-4 flex items-center space-x-4">
              <div class="flex items-center text-sm text-gray-500">
                <i data-feather="message-square" class="w-4 h-4 mr-1"></i>
                <span>{{ ticket?.count_reply }} balasan</span>
              </div>

              <div class="flex items-center text-sm text-gray-500">
                <i data-feather="clock" class="w-4 h-4 mr-1"></i>
                <span class="font-semibold">
                  Terakhir diupdate {{ ticket.updated_at ?? 'n/a' }}
                </span>
              </div>
            </div>
          </div>

          <div class="ml-4 flex items-center text-gray-600 hover:text-gray-800 cursor-pointer">
            <span>Lihat Detail</span>
            <i data-feather="chevron-right" class="w-5 h-5 text-gray-400 ml-1"></i>
          </div>
        </div>
      </router-link>
    </div>

    <p v-if="!tickets.length" class="text-center text-gray-500 py-6">
      Tidak ada tiket ditemukan.
    </p>
  </div>
</template>

