<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
import Sidebar from '@/components/admin/Sidebar.vue'
</script>

<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <main class="flex-1 overflow-x-hidden overflow-y-auto">
      <!-- Topbar -->
      <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="flex items-center justify-between px-6 py-4">
          <h2 class="text-xl font-semibold text-gray-800">
            {{ $route.meta.title }}
          </h2>
          <div class="flex items-center space-x-4">
            <button class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full">
              <i data-feather="bell" class="w-6 h-6"></i>
              <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            <div class="flex items-center bg-gray-50 px-4 py-2 rounded-full">
              <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" alt="Profile"
                class="w-8 h-8 rounded-full" />
              <span class="ml-2 text-sm font-medium text-gray-700">Admin</span>
              <i data-feather="chevron-down" class="w-4 h-4 ml-2 text-gray-500"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6">
        <router-view />
      </div>
    </main>
  </div>
</template>
