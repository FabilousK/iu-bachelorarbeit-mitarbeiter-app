<template>
  <div>
    <v-app-bar
      color="primary-lighten-3"
      density="compact"
      :style="{
        'transform': $vuetify.display.lgAndUp ? 'translateY(64px)' : '',
      }"
    >
      <template v-slot:prepend>
        <v-btn size="small" icon="mdi-undo"
          @click="$router.push('/')"
        />
      </template>
      <v-app-bar-title>{{ eintrag.title }}</v-app-bar-title>
      <!-- <template v-slot:append>
        <v-btn icon="mdi-dots-vertical"></v-btn>
      </template> -->
    </v-app-bar>
    <v-tabs
      v-if="!$vuetify.display.lgAndUp"
      v-model="tabsEintragMetadaten"
    >
      <v-tab value="1">Eintrag</v-tab>
      <v-tab value="2">Info</v-tab>
    </v-tabs>
    <v-row>
      <v-col v-if="tabsEintragMetadaten === '1' || $vuetify.display.lgAndUp" class="pa-6">
        <!-- <div class="text-subtitle-1 mb-2"><b>{{ eintrag.title }}</b></div> -->
        <div
          class="text-body-2"
          v-html="eintrag.html"
        />
      </v-col>
      <v-col v-if="tabsEintragMetadaten === '2' || $vuetify.display.lgAndUp" class="pa-4">
        <v-table style="background:transparent;">
          <tbody>
            <tr>
              <td>Titel:</td>
              <td><b>{{ eintrag.title }}</b></td>
            </tr>
            <tr>
              <td>Code:</td>
              <td>{{ eintrag.code }}</td>
            </tr>
            <tr>
              <td>Assets:</td>
              <td>
                {{ eintrag.assets }}
              </td>
            </tr>
            <tr>
              <td>Letzte Änderung:</td>
              <td>
                {{ eintrag.lastEdit_de }}
              </td>
            </tr>
            <tr>
              <td>Erstellt:</td>
              <td>{{ eintrag.created_de }}</td>
            </tr>
          </tbody>
        </v-table>
      </v-col>
    </v-row>
  </div>
</template>

<script>

export default {
  name: 'showEintrag',
  components: {
  },
  props: {
    eintrag: {
      type: Object,
      default() { return {}; },
    },
  },
  data: () => ({
    tabsEintragMetadaten: null,
  }),
  computed: {
  },
  methods: {
  },
  created() {
  },
};
</script>
