<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable no-unused-vars -->
<script setup>
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { ref, onMounted } from "vue";

// Optional: bikin pesan yang muncul setelah masuk dashboard
const message = ref("");

const authStore = useAuthStore();
const { user } = storeToRefs(authStore);

// Simulasi pesan setelah pindah route
onMounted(() => {
  message.value = "Selamat datang di Admin Dashboard!";
});
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800">
      Admin Dashboard
    </h1>
    <p class="mt-2 text-gray-600">
      Halo, {{ authStore.user?.name }}! Selamat datang di Admin Dashboard.
    </p>
  </div>
</template>
