<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
})
</script>

<template>
  <div class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <div class="flex justify-center">
          <i data-feather="activity" class="w-12 h-12 text-blue-600"></i>
        </div>
        <h2 class="mt-6 text-3xl font-bold text-gray-900">TickTrack</h2>
        <p class="mt-2 text-sm text-gray-600">{{ title }}</p>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <router-view />
      </div>
    </div>
  </div>
</template>
