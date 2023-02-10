<template>
  <v-container>
    <div
      v-if="history.length > 0"
    >
      <v-card
        v-for="eintrag in history"
        :key="eintrag.code"
        :to="`/?h=${eintrag.code}&back=Historie`"
        class="my-2"
      >
        <v-card-title>{{ eintrag.val.title }}</v-card-title>
        <v-card-subtitle>{{ eintrag.val.fetched_de }} - {{eintrag.code }}</v-card-subtitle>
      </v-card>
    </div>
    <div class="text-grey" v-else>
      Es wurden noch keine Einträge abgerufen
    </div>
  </v-container>
</template>

<script>
import { defineComponent } from 'vue';

// Components

export default defineComponent({
  name: 'HistorieView',

  components: {
  },
  computed: {
    history() {
      const history = [];
      let i = 0;
      this.$store.state.getHilfestellung.history.forEach((h) => {
        let eintrag = {};
        if (localStorage.getItem(`h-${h}`)) {
          eintrag = JSON.parse(localStorage.getItem(`h-${h}`));
        }
        if (Object.keys(eintrag).length > 0) {
          history.push({
            sort: i,
            code: h,
            val: eintrag,
          });
          i += 1;
        }
      });
      history.sort((a, b) => {
        if (a.sort > b.sort) {
          return -1;
        }
        if (b.sort > a.sort) {
          return 1;
        }
        return 0;
      });
      return history;
    },
  },
});
</script>
