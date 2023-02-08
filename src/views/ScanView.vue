<template>
  <ScanQR />
</template>

<script>
import { defineComponent } from 'vue';

// Components
import ScanQR from '../components/ScanQR.vue';

export default defineComponent({
  name: 'HomeView',

  components: {
    ScanQR,
  },
});
</script>
