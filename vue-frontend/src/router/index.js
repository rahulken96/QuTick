import Admin from "@/layouts/Admin.vue";
import Auth from "@/layouts/Auth.vue";
import { useAuthStore } from "@/stores/auth";
import Dashboard from "@/views/admin/Dashboard.vue";
import TicketList from "@/views/admin/ticket/TicketList.vue";
import TicketDetail from "@/views/admin/ticket/TicketDetail.vue";
import Login from "@/views/auth/Login.vue";
import Register from "@/views/auth/Register.vue";
import { createRouter, createWebHistory } from "vue-router";

import App from "@/layouts/App.vue";
import AppDashboard from "@/views/app/Dashboard.vue";
import AppTicketDetail from "@/views/app/TicketDetail.vue";
import AppTicketCreate from "@/views/app/TicketCreate.vue";
import Cookies from "js-cookie";
import ActivityLogs from "@/views/admin/logs/ActivityLogs.vue";
import MyActivity from "@/views/app/logs/MyActivity.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      component: App,
      children: [
        {
          path: "",
          name: "app.dashboard",
          component: AppDashboard,
          meta: {
            requiresAuth: true,
            title: "My Dashboard",
          },
        },
        {
          path: "ticket/:code",
          name: "app.ticket.detail",
          component: AppTicketDetail,
          meta: {
            requiresAuth: true,
            title: "Ticket Detail",
          },
        },
        {
          path: "ticket/create",
          name: "app.ticket.create",
          component: AppTicketCreate,
        },
        {
          path: "activity",
          name: "app.activity",
          component: MyActivity,
          meta: {
            requiresAuth: true,
            title: "Aktivitas Saya",
          },
        },
      ],
    },
    {
      path: "/admin",
      component: Admin,
      children: [
        {
          path: "dashboard",
          name: "admin.dashboard",
          component: Dashboard,
          meta: {
            requiresAuth: true,
            role: "admin",
            title: "Dashboard",
          },
        },
        {
          path: "ticket",
          name: "admin.ticket",
          component: TicketList,
          meta: {
            requiresAuth: true,
            role: "admin",
            title: "Ticket",
          },
        },
        {
          path: "ticket/:code",
          name: "admin.ticket.detail",
          component: TicketDetail,
          meta: {
            requiresAuth: true,
            role: "admin",
            title: "Ticket Detail",
          },
        },
        {
          path: "logs",
          name: "admin.logs",
          component: ActivityLogs,
          meta: {
            requiresAuth: true,
            role: "admin",
            title: "Aktivitas Sistem",
          },
        },
      ],
    },
    {
      path: "/login",
      component: Auth,
      children: [
        {
          path: "",
          name: "login",
          component: Login,
          meta: { requiresUnauth: true },
        },
      ],
    },
    {
      path: "/register",
      component: Auth,
      children: [
        {
          path: "",
          name: "register",
          component: Register,
          meta: { requiresUnauth: true },
        },
      ],
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Debug log untuk tracking
  // console.log('=== GUARD DEBUG ===');
  // console.log('To route:', to.path);
  // console.log('Token from store:', authStore.token);
  // console.log('User from store:', authStore.user);
  // console.log('Just logged in:', authStore.justLoggedIn);
  // console.log('==================');

  // Halaman yang butuh login
  if (to.meta.requiresAuth) {
    const token = authStore.token || Cookies.get("token"); // Fallback ke cookies kalau store token null

    if (!token) {
      return next({ name: "login" }); // Redirect ke login kalau token gak ada
    }

    // Kalau user belum ada, validasi token
    if (!authStore.user) {
      const success = await authStore.checkAuth();
      if (!success) {
        return next({ name: "login" }); // Token invalid, ke login
      }
    }

    // Cek role untuk route yang punya meta role
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
      // Redirect sesuai role user
      return next({
        name: authStore.user?.role === "admin" ? "admin.dashboard" : "app.dashboard",
      });
    }

    // Semua aman, lanjut ke route tujuan
    return next();
  }

  // Halaman khusus tamu (login/register)
  if (to.meta.requiresUnauth && authStore.token) {
    // Pastikan user sudah siap
    if (!authStore.user) await authStore.checkAuth();

    // Redirect sesuai role
    return next({
      name: authStore.user?.role === "admin" ? "admin.dashboard" : "app.dashboard",
    });
  }

  // Default: lanjut ke route
  next();
});

export default router;
