<template>
  <aside class="w-64 bg-white shadow-lg">
    <div class="p-6 border-b border-gray-100">
      <h1 class="text-2xl font-bold text-green-600 flex items-center">
        <i data-feather="radio" class="w-8 h-8 mr-2"></i>
        QuTick
      </h1>
    </div>
    <nav class="mt-6">
      <RouterLink :to="{ name: 'admin.dashboard' }"
        class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:border-l-4 hover:border-gray-200"
        :class="{ 'bg-blue-50 border-l-4 border-blue-600': $route.name === 'admin.dashboard' }">
        <i data-feather="home" class="w-5 h-5 mr-3"></i>
        Dashboard
      </RouterLink>

      <RouterLink :to="{ name: 'admin.ticket' }"
        class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:border-l-4 hover:border-gray-200"
        :class="{ 'bg-blue-50 border-l-4 border-blue-600': $route.name === 'admin.ticket' }">
        <i data-feather="tag" class="w-5 h-5 mr-3"></i>
        Tiket
      </RouterLink>

      <a href="#"
        class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:border-l-4 hover:border-gray-200 mt-6">
        <i data-feather="log-out" class="w-5 h-5 mr-3"></i>
        Logout
      </a>
    </nav>
  </aside>
</template>
