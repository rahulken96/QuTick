/* eslint-disable no-unused-vars */
import { defineStore } from "pinia";
import { axiosInstance } from "@/plugins/axios";
import { handleError } from "@/helpers/errorHelper";
import router from "@/router";

export const useTicketStore = defineStore("ticket", {
  state: () => ({
    tickets: [],
    loading: false,
    error: null,
    success: null,
  }),

  actions: {
    async fetchTickets(params) {
      this.loading = true;
      this.error   = null;
      this.success = null;

      try {
        const response = await axiosInstance.get("/ticket/all", {params});
        const isValid  = response.data?.status || false;

        if (!isValid) {
          this.error = handleError(response.data?.message);
        }

        this.tickets = response.data?.data;
      } catch (error) {
        this.error = handleError(error);
      } finally {
        this.loading = false;
      }
    },

    async fetchMyTickets(params) {

    },

    async fetchTicket(code) {

    },

    async createTicket(payload) {

    },

    async updateTicket(payload) {

    },

    async createTicketReply(code, payload) {

    },
  }
});
