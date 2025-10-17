<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable no-unused-vars -->
<!-- eslint-disable vue/valid-template-root -->
<script setup>
import { sweetAlert } from "@/helpers/swalHelper";
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { ref } from "vue";

const authStore = useAuthStore();
const { loading, success, error } = storeToRefs(authStore);
const { register } = authStore;

const form = ref({
  name: null,
  email: null,
  password: null
})

const handleSubmit = async () => {
  await register(form.value)
}

const togglePassword = () => {

}

const toggleConfirmPassword = () => {

}

</script>

<template>
  <form class="space-y-6" @submit.prevent="handleSubmit()">
    <!-- Name -->
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
      <div class="mt-1 relative">
        <input v-model="form.name" type="text" id="name" name="name"
          class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-50"
          :class="{'border-red-500 ring-red-500': error?.name}"
          placeholder="Jane Doe"
          required
          >

        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
          <i data-feather="user" class="w-4 h-4 text-gray-400"></i>
        </div>

        <p class="mt-1 text-xs text-red-500" v-if="error?.name">
          {{ error?.name?.join(', ') }}
        </p>
      </div>
    </div>

    <!-- Email -->
    <div>
      <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
      <div class="mt-1 relative">
        <input v-model="form.email" type="email" id="email" name="email"
          class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
          :class="{ 'border-red-500 ring-red-500': error?.email }"
          placeholder="nama@perusahaan.com"
          required
        />

        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
          <i data-feather="mail" class="w-4 h-4 text-gray-400"></i>
        </div>

        <p class="mt-1 text-xs text-red-500" v-if="error?.email">
          {{ error?.email?.join(', ') }}
        </p>
      </div>
    </div>

    <!-- Password -->
    <div>
      <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
      <div class="mt-1 relative">
        <input v-model="form.password" type="password" id="password" name="password"
          class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
          :class="{ 'border-red-500 ring-red-500': error?.password }"
          placeholder="••••••••"
          required
        />

        <p class="mt-1 text-xs text-red-500" v-if="error?.password">
          {{ error?.password?.join(', ') }}
        </p>

        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
          <button
            type="button"
            class="text-gray-400 hover:text-gray-600 focus:outline-none"
          >
            <i data-feather="eye" class="w-4 h-4" id="password-toggle"></i>
          </button>
        </div>
      </div>
    </div>

    <div>
      <button
        type="submit"
        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
      >
        <span v-if="!loading">
          Daftar
        </span>
        <span v-else>
          <i>Tunggu...</i>
        </span>
      </button>
    </div>

    <!-- Divider -->
    <div class="mt-6">
      <div class="relative">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-2 bg-white text-gray-500">Atau</span>
        </div>
      </div>
    </div>

    <!-- Login -->
    <div class="mt-6 text-center">
      <p class="text-sm text-gray-600">
        Sudah punya akun?
        <a href="/login" class="font-medium text-blue-600 hover:text-blue-800">
          Masuk
        </a>
      </p>
    </div>
  </form>
</template>
