/* eslint-disable no-unused-vars */
import { defineStore } from "pinia";
import { axiosInstance } from "@/plugins/axios";
import { handleError } from "@/helpers/errorHelper";
import router from "@/router";
import Cookies from "js-cookie";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    loading: false,
    error: null,
    success: null,
  }),

  getters: {
    token: () => Cookies.get("token"),
    isAuthenticated: (state) => !!state.user,
  },

  actions: {
    async register(credentials) {},

    async login(credentials) {
      this.loading = true;

      try {
        const response = await axiosInstance.post("/login", credentials);
        const isValid = response.data?.status || false;

        if (!isValid) {
          this.error = handleError(response.data?.message);
        }

        const token = response.data?.data?.token;
        Cookies.set("token", token);
        this.success = response.data.message;

        /* simpan user ke store guard */
        this.user = response.data?.data?.user;

        const isAdmin = response.data?.data?.user?.role == "admin";
        if (isAdmin) {
          router.push({ name: "admin.dashboard" });
        } else {
          router.push({ name: "app.dashboard" });
        }
      } catch (error) {
        this.error = handleError(error);
      } finally {
        this.loading = false;
      }
    },

    async logout() {},
  },
});
