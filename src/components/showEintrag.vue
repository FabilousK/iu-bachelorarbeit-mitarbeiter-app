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
          @click="$router.push(backUrl)"
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
      <v-tab value="html">Hilfe</v-tab>
      <v-tab value="html_short" v-if="eintrag.html_short !== ''">Kurzhilfe</v-tab>
      <v-tab value="info">Info</v-tab>
    </v-tabs>
    <v-row>
      <v-col v-if="
        ['html', 'html_short'].includes(tabsEintragMetadaten)
        || $vuetify.display.lgAndUp
      ">
        <v-tabs
          v-if="$vuetify.display.lgAndUp && eintrag.html_short !== ''"
          v-model="tabsHtmlVariant"
        >
          <v-tab value="html">Hilfe</v-tab>
          <v-tab value="html_short" v-if="eintrag.html_short !== ''">Kurzhilfe</v-tab>
        </v-tabs>
        <div class="pa-4">
          <div
            v-if="
              (tabsHtmlVariant === 'html' && $vuetify.display.lgAndUp)
              || (tabsEintragMetadaten === 'html' && !$vuetify.display.lgAndUp)
              || eintrag.html_short === ''
            "
            class="text-body-2"
            v-html="eintrag.html"
          ></div>
          <div
            v-else
            class="text-body-2"
            v-html="eintrag.html_short"
          ></div>
        </div>
      </v-col>
      <v-col v-if="tabsEintragMetadaten === 'info' || $vuetify.display.lgAndUp" class="pa-4">
        <v-table style="background:transparent;">
          <tbody>
            <tr>
              <td>Titel:</td>
              <td v-if="!edit"><b>{{ eintrag.title }}</b></td>
              <td v-else>
                <v-text-field
                  label="Titel"
                  v-model="eintrag.title"
                  class="mt-5"
                />
              </td>
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
    prop_eintrag: {
      type: Object,
      default() { return {}; },
    },
  },
  data: () => ({
    tabsEintragMetadaten: null,
    tabsHtmlVariant: null,
    eintrag: {},
  }),
  watch: {
    prop_eintrag: {
      handler() {
        this.setEintrag();
      },
      deep: true,
    },
  },
  computed: {
    backUrl() {
      if (this.$router.currentRoute.value.query.back) {
        return `/${this.$router.currentRoute.value.query.back}`;
      }
      return '/';
    },
    edit() {
      if (
        this.$vuetify.display.lgAndUp
        && this.$store.state.login.user.idRole === 2
      ) {
        return true;
      }
      return false;
    },
  },
  methods: {
    setEintrag() {
      this.eintrag = JSON.parse(JSON.stringify(this.prop_eintrag));
    },
  },
  created() {
    this.setEintrag();
  },
};
</script>
