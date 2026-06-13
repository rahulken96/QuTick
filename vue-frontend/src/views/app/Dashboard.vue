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
  setTimeout(() => { feather.replace() }, 50)
}, 300), { deep: true });

onMounted(async () => {
  await fetchMyTickets(filters.value);
  feather.replace()
})
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Tiket Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola dan pantau status tiket Anda</p>
      </div>

      <router-link
        :to="{ name: 'app.ticket.create' }"
        class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm"
      >
        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
        Buat Tiket Baru
      </router-link>
    </div>

    <!-- Alert sukses -->
    <div
      v-if="success"
      class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative flex items-center justify-between"
      role="alert"
    >
      <div class="flex items-center text-sm font-medium">
        <i data-feather="check-circle" class="w-5 h-5 mr-2 text-emerald-500"></i>
        <span>{{ success }}</span>
      </div>

      <button class="flex items-center justify-center p-1 rounded-lg hover:bg-emerald-100/50" @click="success = null">
        <i data-feather="x" class="w-4 h-4 text-emerald-600"></i>
      </button>
    </div>

    <!-- Filter -->
    <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 p-4 shadow-sm">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search -->
        <div class="relative">
          <input
            type="text"
            placeholder="Cari tiket..."
            v-model="filters.keyword"
            class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
          />
          <i data-feather="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
        </div>

        <!-- Status -->
        <select
          v-model="filters.status"
          class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-slate-700"
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
          class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-slate-700"
        >
          <option value="">Semua Prioritas</option>
          <option value="2">High</option>
          <option value="1">Medium</option>
          <option value="0">Low</option>
        </select>
      </div>
    </div>

    <!-- Skeleton Loading -->
    <div v-if="loading" class="space-y-4">
      <div
        v-for="n in 3"
        :key="n"
        class="bg-white rounded-2xl border border-slate-100 p-6 animate-pulse"
      >
        <div class="h-5 bg-slate-100 rounded w-1/3 mb-4"></div>
        <div class="h-4 bg-slate-100 rounded w-2/3 mb-2"></div>
        <div class="h-4 bg-slate-100 rounded w-1/2 mb-2"></div>
        <div class="flex space-x-3 mt-4">
          <div class="h-4 bg-slate-100 rounded w-1/4"></div>
          <div class="h-4 bg-slate-100 rounded w-1/3"></div>
        </div>
      </div>
    </div>

    <!-- Data Tiket -->
    <div v-else class="space-y-4">
      <div
        class="bg-white rounded-2xl border border-slate-100 hover:shadow-md hover:border-slate-200/80 transition-all duration-300 overflow-hidden"
        v-for="ticket in tickets"
        :key="ticket.code"
      >
        <router-link
          :to="{ name: 'app.ticket.detail', params: { code: ticket.code } }"
          class="block p-6"
        >
          <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex-1 min-w-0 space-y-2.5">
              <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-bold text-slate-800 truncate">
                  {{ ticket.title }}
                </h3>
                
                <span
                  class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                  :class="{
                    'text-blue-600 bg-blue-50 border border-blue-100': ticket.status.number == 0,
                    'text-amber-600 bg-amber-50 border border-amber-100': ticket.status.number == 1,
                    'text-emerald-600 bg-emerald-50 border border-emerald-100': ticket.status.number == 2,
                    'text-rose-600 bg-rose-50 border border-rose-100': ticket.status.number == 3,
                  }"
                >
                  {{ capitalize(ticket.status.name) }}
                </span>

                <span
                  class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                  :class="{
                    'text-rose-600 bg-rose-50 border border-rose-100': ticket.priority.number == 2,
                    'text-amber-600 bg-amber-50 border border-amber-100': ticket.priority.number == 1,
                    'text-emerald-600 bg-emerald-50 border border-emerald-100': ticket.priority.number == 0,
                  }"
                >
                  {{ capitalize(ticket.priority.name) }}
                </span>
              </div>

              <p class="text-xs text-slate-400">
                <span class="font-mono font-semibold">#{{ ticket.code ?? 'n/a' }}</span>
                <span class="mx-1.5">•</span>
                Dibuat pada <span class="font-medium text-slate-500">{{ ticket.created_at ?? 'n/a' }}</span>
              </p>

              <p class="text-sm text-slate-600 line-clamp-2">
                {{ ticket.description ?? 'n/a' }}
              </p>

              <div class="pt-2 flex items-center space-x-4">
                <div class="flex items-center text-xs text-slate-500">
                  <i data-feather="message-square" class="w-4 h-4 mr-1 text-slate-400"></i>
                  <span>{{ ticket?.count_reply }} balasan</span>
                </div>

                <div class="flex items-center text-xs text-slate-500">
                  <i data-feather="clock" class="w-4 h-4 mr-1 text-slate-400"></i>
                  <span>Terakhir diupdate {{ ticket.updated_at ?? 'n/a' }}</span>
                </div>
              </div>
            </div>

            <div class="self-end sm:self-center flex items-center text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
              <span>Detail</span>
              <i data-feather="chevron-right" class="w-4 h-4 ml-0.5"></i>
            </div>
          </div>
        </router-link>
      </div>

      <div v-if="!tickets.length" class="text-center text-slate-400 py-12 bg-white rounded-2xl border border-slate-100">
        <i data-feather="info" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
        Tidak ada tiket ditemukan.
      </div>
    </div>
  </div>
</template>
