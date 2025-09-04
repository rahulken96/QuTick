/* eslint-disable no-unused-vars */
import { defineStore } from "pinia";
import { axiosInstance } from "@/plugins/axios";
import { handleError } from "@/helpers/errorHelper";

export const useDashboardStore = defineStore("dashboard", {
  state: () => ({
    statistic: null,
    loading: false,
    error: null,
    success: null,
  }),

  actions: {
    async fetchStatistics() {
      this.loading = true;
      this.error   = null;
      this.success = null;

      try {
        const response = await axiosInstance.get("/dashboard");
        const isValid = response.data?.status || false;

        if (!isValid) {
          this.error = handleError(response.data?.message);
        }

        this.statistic = response.data?.data;
      } catch (error) {
        this.error = handleError(error);
      } finally {
        this.loading = false;
      }
    }
  }
});
