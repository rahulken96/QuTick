<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable no-unused-vars -->
<script setup>
import { sweetAlert } from "@/helpers/swalHelper";
import { ref, onMounted, watch } from "vue";
import { useTicketStore } from "@/stores/ticket";
import { storeToRefs } from "pinia";
import feather from "feather-icons";

const ticketStore = useTicketStore();
const { success, error, loading } = storeToRefs(ticketStore);
const { createTicket } = ticketStore;

const form = ref({
  title: '',
  description: '',
  priority: '',
})

const handleSubmit = async () => {
  await createTicket(form.value)

  if (success.value) {
    sweetAlert('Berhasil', 'Ticket Berhasil Dibuat!', 'success');
    resetForm();
  }
}

const resetForm = () => {
  form.value = {
    title: '',
    description: '',
    priority: ''
  }

  error.value = {}
}

onMounted(async () => {
  feather.replace()
})

</script>

<template>
  <div class="mb-6">
    <router-link :to="{ name: 'app.dashboard' }"
      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">
      <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
      Kembali Ke List Ticket
    </router-link>
  </div>

  <!-- Create Ticket Form -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
      <h1 class="text-2xl font-bold text-gray-800">Buat Tiket Baru</h1>
      <p class="text-sm text-gray-500 mt-1">Isi form di bawah ini untuk membuat tiket baru</p>
    </div>

    <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
      <!-- Judul Tiket -->
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Tiket</label>
        <input type="text" id="title" v-model="form.title" placeholder="Contoh: Gangguan Jaringan WiFi"
          class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
        <div v-if="error?.title" class="flex items-center mt-2">
          <p class="text-xs text-red-500">{{ error.title[0] }}</p>
        </div>
      </div>

      <!-- Deskripsi -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Masalah</label>
        <textarea id="description" v-model="form.description" rows="6"
          placeholder="Jelaskan masalah Anda secara detail. Sertakan informasi seperti:&#10;- Kapan masalah mulai terjadi&#10;- Apa yang sudah Anda coba&#10;- Pesan error jika ada"
          class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
        <div v-if="error?.description" class="flex items-center mt-2">
          <p class="text-xs text-red-500">{{ error.description[0] }}</p>
        </div>
      </div>

      <!-- Prioritas -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
        <div class="grid grid-cols-3 gap-4">

          <!-- Low -->
          <label class="relative flex cursor-pointer rounded-lg border"
            :class="[form.priority === '0' ? 'border-green-600 bg-green-50' : 'border-gray-200']">
            <input type="radio" v-model="form.priority" value="0" class="sr-only" />
            <div class="flex w-full items-center justify-between p-4">
              <div class="flex items-center">
                <div class="text-sm">
                  <p class="font-medium text-gray-900">Rendah</p>
                  <p class="text-gray-500">Tidak mendesak</p>
                </div>
              </div>
              <div class="shrink-0 text-green-600" v-show="form.priority === '0'">
                <i data-feather="check-circle" class="w-6 h-6"></i>
              </div>
            </div>
          </label>

          <!-- Medium -->
          <label class="relative flex cursor-pointer rounded-lg border"
            :class="[form.priority === '1' ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200']">
            <input type="radio" v-model="form.priority" value="1" class="sr-only" />
            <div class="flex w-full items-center justify-between p-4">
              <div class="flex items-center">
                <div class="text-sm">
                  <p class="font-medium text-gray-900">Sedang</p>
                  <p class="text-gray-500">Normal</p>
                </div>
              </div>
              <div class="shrink-0 text-yellow-500" v-show="form.priority === '1'">
                <i data-feather="check-circle" class="w-6 h-6"></i>
              </div>
            </div>
          </label>

          <!-- High -->
          <label class="relative flex cursor-pointer rounded-lg border"
            :class="[form.priority === '2' ? 'border-red-500 bg-red-50' : 'border-gray-200']">
            <input type="radio" v-model="form.priority" value="2" class="sr-only" />
            <div class="flex w-full items-center justify-between p-4">
              <div class="flex items-center">
                <div class="text-sm">
                  <p class="font-medium text-gray-900">Tinggi</p>
                  <p class="text-gray-500">Sangat mendesak</p>
                </div>
              </div>
              <div class="shrink-0 text-red-600" v-show="form.priority === '2'">
                <i data-feather="check-circle" class="w-6 h-6"></i>
              </div>
            </div>
          </label>
        </div>

        <div v-if="error?.priority" class="mt-2 flex items-center">
          <p class="text-xs text-red-500">{{ error.priority[0] }}</p>
        </div>
      </div>

      <!-- Submit -->
      <div class="flex items-center justify-end gap-3">
        <button
          type="button"
          @click="resetForm"
          class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300"
        >
          Batal
        </button>

        <button
          type="submit"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700"
          >
          <i data-feather="send" class="w-4 h-4 mr-2"></i>
          Buat Tiket
        </button>
      </div>
    </form>
  </div>
</template>
