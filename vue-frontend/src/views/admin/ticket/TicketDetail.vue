<script setup>
import { onMounted } from 'vue';

const fetchTicketDetail = async () => {
  const response = await fetchTicket(route.params.code);

  ticket.value = response.data;
  form.value.status = response.status;
};

const handleSubmit = async () => {
  await createTicketReply(route.params.code, form.value);
  await fetchTicketDetail();
};

onMounted(async () => {
  await fetchTicketDetail();
  feather.replace();
});
</script>

<template>

</template>
