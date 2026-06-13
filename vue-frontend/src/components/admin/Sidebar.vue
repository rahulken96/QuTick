<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable no-unused-vars -->
<script setup>
import { sweetAlert } from "@/helpers/swalHelper";
import { useAuthStore } from '@/stores/auth';
import { storeToRefs } from 'pinia';

const authStore = useAuthStore();
const {logout} = authStore;

const handleLogout = async () => {
  sweetAlert(
    "Ingin Logout ?",
    "Yakin ingin keluar ?",
    "warning",
    async () => {
      await logout();
      sweetAlert('Berhasil', 'Anda berhasil logout!', 'success');
    },
    'Ya, Yakin'
  );
}
</script>

<template>
  <aside class="w-64 bg-slate-50/50 border-r border-slate-100 shadow-sm">
    <div class="p-6 border-b border-slate-100/80">
      <h1 class="text-2xl font-bold text-emerald-600 flex items-center">
        <i data-feather="radio" class="w-8 h-8 mr-2 text-emerald-500"></i>
        QuTick
      </h1>
    </div>
    <nav class="mt-6">
      <RouterLink :to="{ name: 'admin.dashboard' }"
        class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-100/50 hover:border-l-4 hover:border-slate-200 transition-all"
        :class="{ 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500': $route.name === 'admin.dashboard' }">
        <i data-feather="home" class="w-5 h-5 mr-3"></i>
        Dashboard
      </RouterLink>

      <RouterLink :to="{ name: 'admin.ticket' }"
        class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-50 hover:border-l-4 hover:border-slate-200 transition-all"
        :class="{ 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500': $route.name === 'admin.ticket' }">
        <i data-feather="tag" class="w-5 h-5 mr-3"></i>
        Tiket
      </RouterLink>

      <RouterLink :to="{ name: 'admin.logs' }"
        class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-50 hover:border-l-4 hover:border-slate-200 transition-all"
        :class="{ 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500': $route.name === 'admin.logs' }">
        <i data-feather="activity" class="w-5 h-5 mr-3"></i>
        Log Aktivitas
      </RouterLink>

      <a @click="handleLogout()"
        class="flex items-center px-6 py-3 text-slate-600 hover:bg-slate-50 hover:border-l-4 hover:border-slate-200 mt-6 transition-all" style="cursor: pointer;">
        <i data-feather="log-out" class="w-5 h-5 mr-3"></i>
        Logout
      </a>
    </nav>
  </aside>
</template>
