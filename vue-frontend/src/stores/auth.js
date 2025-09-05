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
    justLoggedIn: false,
  }),

  getters: {
    token: () => Cookies.get("token"),
    isAuthenticated: (state) => !!state.user,
  },

  actions: {
    async register(credentials) {},

    async login(credentials) {
      this.loading = true;
      this.error   = null;
      this.success = null;

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
        this.justLoggedIn = true;

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

    async checkAuth() {
      try {
        const token = Cookies.get("token");
        if (!token) {
          this.user = null;
          return false;
        }

        const response = await axiosInstance.get("/my-profile", {
          headers: { Authorization: `Bearer ${token}` },
        });

        if (!(response.data?.status || false)) return false;

        this.user = response.data?.data;
        return true;
      } catch (error) {
        this.user = null;
        Cookies.remove("token");
        this.error = handleError(error);
        return false;
      }
    },

    async logout() {
      this.loading = true;
      this.error   = null;
      this.success = null;

      try {
        const response = await axiosInstance.post("/logout");
        const isValid = response.data?.status || false;

        if (!isValid) {
          this.error = handleError(response.data?.message);
        }

        Cookies.remove('token');

        this.user    = null;
        this.success = response.data?.message;

        router.push({name: 'login'});
      } catch (error) {
        this.error = handleError(error);
      } finally {
        this.loading = false;
      }
    },
  },
});
