<!-- eslint-disable no-unused-vars -->
<script setup>
import { sweetAlert } from "@/helpers/swalHelper";
import { ref, onMounted, watch } from "vue";
import { useTicketStore } from "@/stores/ticket";
import { storeToRefs } from "pinia";
import { capitalize } from "lodash";
import { DateTime } from "luxon";
import { useRoute } from "vue-router";
import feather from "feather-icons";

const route = useRoute();

const ticketStore = useTicketStore();
const { landing, error, success } = storeToRefs(ticketStore);
const { fetchTicket, createTicketReply } = ticketStore;

const ticket = ref({});
const replies = ref({});
const form = ref({
  status: 0,
  message: '',
})

const fetchTicketDetail = async () => {
  const ticketCode = route.params.code;
  const response = await fetchTicket(ticketCode);

  ticket.value = response.ticket;
  replies.value = response.replies;
  form.value.status = response.status;
}

const handleSubmit = async () => {
  if (!form.value.message || form.value.message.trim() === "") {
    sweetAlert('Warning!', 'Jawaban tidak boleh kosong', 'warning');
    return;
  }

  await createTicketReply(route.params.code, form.value);
  await fetchTicketDetail();
  resetForm();
}

const resetForm = () => {
  form.value.message = "";
};

onMounted(async () => {
  await fetchTicketDetail();
  feather.replace();
});

</script>

<template>
  <!-- Back Button -->
  <div class="mb-6">
    <router-link :to="{ name: 'app.dashboard' }"
      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800"
    >
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
    Kembali Ke List Ticket
    </router-link>
  </div>

  <!-- Ticket Info -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="p-6">
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-lg font-semibold text-gray-800">{{ ticket?.title }}</h1>
          <h1 class="font-mono font-semibold text-gray-400">#{{ ticket?.code }}</h1>

          <div class="mt-2 flex items-center space-x-4">
            <span
              class="px-3 py-1 text-sm font-semibold text-blue-700 bg-blue-100 rounded-full"
              :class="{
                'text-blue-700 bg-blue-100': ticket.status?.number == 0,
                'text-yellow-700 bg-yellow-100': ticket.status?.number == 1,
                'text-green-700 bg-green-100': ticket.status?.number == 2,
                'text-red-700 bg-red-100': ticket.status?.number == 3,
              }"
            >
              {{ capitalize(ticket.status?.name) }}
            </span>

            <span
              class="px-3 py-1 text-sm font-semibold text-red-700 bg-red-100 rounded-full"
              :class="{
                'text-red-700 bg-red-100': ticket.priority?.number == 2,
                'text-yellow-700 bg-yellow-100': ticket.priority?.number == 1,
                'text-green-700 bg-green-100': ticket.priority?.number == 0,
              }"
            >
              {{ capitalize(ticket.priority?.name) }}
            </span>
          </div>

          <div class="mt-2 flex items-center space-x-4">
            <!-- Reporter -->
            <span class="text-sm text-gray-500">
              Dilaporkan oleh <strong>{{ ticket.user?.name }}</strong>
            </span>

            <span class="text-sm">|</span>

            <!-- Reporter -->
            <span class="text-sm text-gray-500">
              Dilaporkan pada <strong>{{ ticket.created_at }}</strong>
            </span>
          </div>
        </div>

        <!-- <button
          class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50"
        >
          <i data-feather="download" class="w-4 h-4 inline-block mr-2"></i>
          Lampiran
        </button> -->
      </div>
    </div>
  </div>

  <!-- Deskripsi Ticket -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="p-6">
      <h3 class="text-lg font-semibold text-gray-800">
        Deskripsi Ticket
      </h3>
      <span class="text-xs font-sans font-semibold text-gray-400">
        "{{ ticket.description ?? 'n/a' }}"
      </span>
    </div>
  </div>

  <!-- Discussion Thread -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <!-- Thread Header -->
    <div class="p-6 border-b border-gray-100" v-if="replies.length > 0">
      <div class="mb-4" v-for="reply in replies" :key="reply.id">
        <div class="flex items-start space-x-4">
          <img
            :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(reply.user.name)}&background=0D8ABC&color=fff`"
            :alt="reply.user.name ?? 'User'"
            class="w-10 h-10 rounded-full"
          />
          <div class="flex-1">
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-medium text-gray-800">{{ reply.user.name }}</h4>
                <p class="text-xs text-gray-500">{{ reply.created_at }}</p>
              </div>
            </div>

            <div class="mt-3 text-sm text-gray-800">
              <p>{{ reply.message }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="p-6">
        <p class="text-sm text-gray-500">Belum ada tanggapan</p>
      </div>
    </div>

    <!-- Reply Form -->
    <div class="p-6 border border-gray-100">
      <h4 class="text-sm font-medium text-gray-900 mb-4">Tambah Balasan</h4>

      <form @submit.prevent="handleSubmit()" class="space-y-4">
        <div class="group">
          <textarea
            v-model="form.message"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            :class="{ 'border-red-500 ring-red-500': error?.message }"
            rows="4"
            placeholder="Tulis balasan Anda di sini..."
            maxlength="1000"
          ></textarea>

          <p v-if="error?.message" class="mt-1 text-xs text-red-500">
            {{ error?.message?.join(', ') }}
          </p>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <button
              type="button"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
              <i data-feather="paperclip" class="w-4 h-4 inline-block mr-2"></i>
              Lampiran
            </button>
          </div>
          <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50 focus:outline-none focus:ring-1 focus:ring-blue-500"
          >
            <i data-feather="send" class="w-4 h-4 inline-block mr-2"></i>

            <span v-if="!loading">
              Kirim Balesan
            </span>
            <span v-else>
              Tunggu...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
