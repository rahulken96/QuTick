<!-- eslint-disable no-unused-vars -->
<!-- eslint-disable vue/valid-template-root -->
<script setup>
import { sweetAlert } from "@/helpers/swalHelper";
import { ref, onMounted, watch } from "vue";
import { useTicketStore } from "@/stores/ticket";
import { storeToRefs } from "pinia";
import { capitalize, debounce } from "lodash";
import { DateTime } from "luxon";
import { Chart } from "chart.js/auto";
import feather from "feather-icons";

const ticketStore = useTicketStore();
const { tickets } = storeToRefs(ticketStore);
const { fetchTickets } = ticketStore;

const filters = ref({
  keyword: '',
  status: '',
  priority: '',
  date: '',
});

watch(filters, debounce(async () => {
  await fetchTickets(filters.value);
}, 300), { deep: true });

onMounted(async () => {
  await fetchTickets(filters.value);

  feather.replace()
});
</script>

<template>
  <div class="p-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

          <div class="relative">
            <input type="text" v-model="filters.keyword" placeholder="Cari tiket..."
              class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500" />
            <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
          </div>

          <!-- Status Filter -->
          <select v-model="filters.status"
            class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua Status</option>
            <option value="0">Open</option>
            <option value="1">In Progress</option>
            <option value="2">Resolved</option>
            <option value="3">Rejected</option>
          </select>

          <!-- Priority Filter -->
          <select v-model="filters.priority"
            class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua Prioritas</option>
            <option value="2">High</option>
            <option value="1">Medium</option>
            <option value="0">Low</option>
          </select>

          <!-- Date Filter -->
          <!-- <select class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua Tanggal</option>
            <option value="today">Hari Ini</option>
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
          </select> -->

        </div>
      </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                ID Tiket
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Judul
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Pelapor
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Prioritas
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tanggal
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Aksi
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="ticket in tickets" :key="ticket.code" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                #{{ ticket.code }}
              </td>

              <td class="px-6 py-4">
                <div class="text-sm text-gray-800">
                  {{ ticket.title }}
                </div>
              </td>

              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <img :src="`https://ui-avatars.com/api/?name=${ticket.user.name}&background=0D8ABC&color=fff`"
                    :alt="ticket.user.name" class="w-6 h-6 rounded-full" />
                  <span class="ml-2 text-sm text-gray-800">{{ ticket.user.name }}</span>
                </div>
              </td>

              <!-- kolom status, prioritas, tanggal, aksi bisa kamu lengkapi -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 text-xs font-medium rounded-full" :class="{
                  'text-blue-700 bg-blue-100': ticket.status.number == 0,
                  'text-yellow-700 bg-yellow-100': ticket.status.number == 1,
                  'text-green-700 bg-green-100': ticket.status.number == 2,
                  'text-red-700 bg-red-100': ticket.status.number == 3,
                }">
                  {{ capitalize(ticket.status.name) }}
                </span>
              </td>

              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 text-xs font-medium rounded-full" :class="{
                  'text-red-700 bg-red-100': ticket.priority.number == 2,
                  'text-yellow-700 bg-yellow-100': ticket.priority.number == 1,
                  'text-green-700 bg-green-100': ticket.priority.number == 0,
                }">
                  {{ capitalize(ticket.priority.name) }}
                </span>
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ ticket.created_at }}
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <RouterLink :to="{ name: 'admin.ticket.detail', params: { code: ticket.code } }"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-600 hover:text-blue-800 hover:bg-blue-50">
                  <i data-feather="message-square" class="w-4 h-4 mr-2"></i>
                  Jawab
                </RouterLink>
              </td>



            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
