<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
import Sidebar from '@/components/admin/Sidebar.vue'
import { ref, onMounted, onUnmounted } from 'vue'
import { axiosInstance } from '@/plugins/axios'
import { DateTime } from 'luxon'
import feather from 'feather-icons'

const notifications = ref([])
const showNotifications = ref(false)
const hasUnread = ref(false)
const lastOpened = ref(localStorage.getItem('last_notif_opened') || '')
const sidebarOpen = ref(false)

const fetchNotifications = async () => {
  try {
    const response = await axiosInstance.get('/logs', {
      params: { limit: 5 }
    })
    if (response.data.status) {
      notifications.value = response.data.data.data
      
      if (notifications.value.length > 0) {
        const latestTime = notifications.value[0].created_at
        if (!lastOpened.value || DateTime.fromISO(latestTime) > DateTime.fromISO(lastOpened.value)) {
          hasUnread.value = true
        } else {
          hasUnread.value = false
        }
      }
    }
  } catch (err) {
    console.error("Failed to fetch notifications:", err)
  }
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value) {
    hasUnread.value = false
    const nowStr = DateTime.now().toISO()
    localStorage.setItem('last_notif_opened', nowStr)
    lastOpened.value = nowStr
    
    setTimeout(() => { feather.replace() }, 50)
  }
}

let intervalId = null

onMounted(() => {
  fetchNotifications()
  intervalId = setInterval(fetchNotifications, 10000)
  setTimeout(() => { feather.replace() }, 100)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>

<template>
  <div class="flex h-screen bg-slate-50/50 overflow-hidden relative">
    <!-- Backdrop for Mobile Sidebar -->
    <div 
      v-if="sidebarOpen" 
      @click="sidebarOpen = false" 
      class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 md:hidden"
    ></div>

    <!-- Sidebar (Responsive Drawer) -->
    <div 
      class="fixed inset-y-0 left-0 z-50 transform md:relative md:translate-x-0 transition-transform duration-300 ease-in-out shrink-0 bg-white"
      :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    >
      <Sidebar />
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-x-hidden overflow-y-auto flex flex-col h-full">
      <!-- Topbar -->
      <div class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="flex items-center">
            <!-- Sidebar toggle button for mobile -->
            <button 
              @click="sidebarOpen = !sidebarOpen" 
              class="p-2 -ml-2 mr-3 text-slate-600 md:hidden hover:bg-slate-50 rounded-xl transition-colors"
            >
              <i data-feather="menu" class="w-5 h-5"></i>
            </button>
            <h2 class="text-base sm:text-lg font-bold text-slate-800 truncate">
              {{ $route.meta.title }}
            </h2>
          </div>

          <div class="flex items-center space-x-2 sm:space-x-4 relative">
            <!-- Bell icon -->
            <button 
              @click="toggleNotifications" 
              class="relative p-2 text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-xl transition-colors"
            >
              <i data-feather="bell" class="w-5 h-5"></i>
              <span v-if="hasUnread" class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
            </button>

            <!-- Notifications Dropdown (Soft light aesthetic) -->
            <div 
              v-if="showNotifications" 
              class="absolute right-0 sm:right-36 top-12 w-72 sm:w-80 bg-white rounded-2xl border border-slate-100 shadow-xl py-2 z-50 overflow-hidden"
            >
              <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800">Pemberitahuan</span>
                <span v-if="hasUnread" class="w-2 h-2 bg-rose-500 rounded-full"></span>
              </div>
              <div class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                <div v-if="notifications.length === 0" class="px-4 py-6 text-center text-xs text-slate-400">
                  Tidak ada pemberitahuan baru
                </div>
                <div 
                  v-else 
                  v-for="notif in notifications" 
                  :key="notif.id" 
                  class="px-4 py-3 hover:bg-slate-50/50 transition-colors flex items-start space-x-3"
                >
                  <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg shrink-0">
                    <i data-feather="info" class="w-3.5 h-3.5"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-700 leading-normal">
                      {{ notif.activity }}
                    </p>
                    <p class="text-[10px] text-slate-400 mt-1">
                      {{ DateTime.fromISO(notif.created_at).toFormat('dd LLL, HH:mm') }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Profile Info -->
            <div class="flex items-center bg-slate-50 px-3 sm:px-4 py-1.5 rounded-xl border border-slate-100/50">
              <img src="https://ui-avatars.com/api/?name=Admin&background=10B981&color=fff" alt="Profile"
                class="w-7 h-7 rounded-full" />
              <span class="ml-2.5 text-xs font-semibold text-slate-700 hidden sm:inline">Admin</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-4 sm:p-6 space-y-6 flex-1">
        <router-view />
      </div>
    </main>
  </div>
</template>
