<template>
  <div>
    <v-dialog
      v-model="dialogCancelEdit"
      width="500px"
    >
      <v-card>
        <v-card-title>Sicher?</v-card-title>
        <v-card-text>
          Nicht gespeicherte Änderungen gehen verloren.
        </v-card-text>
        <v-card-actions>
          <v-btn
            size="small"
            @click="dialogCancelEdit = false"
          >
            abbrechen
          </v-btn>
          <v-spacer />
          <v-btn
            size="small"
            color="error"
            @click="
              this.eintrag = JSON.parse(this.eintragSnapshot);
              edit = false;
              dialogCancelEdit = false;
            "
          >
            Ohne Speichern beenden
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
      <template v-slot:append v-if="allowEdit">
        <v-btn
          v-if="!edit"
          icon="mdi-pencil"
          @click="edit = true;"
        ></v-btn>
        <v-btn
          v-if="edit"
          icon="mdi-cancel"
          @click="cancelEdit();"
        ></v-btn>
        <v-btn
          v-if="edit"
          icon="mdi-content-save"
          :disabled="
            JSON.stringify(eintrag) === eintragSnapshot
            || eintrag.title.split(' ').join('') === ''
          "
          :loading="$store.state.getHilfestellung.loadingSave"
          @click="saveEintrag();"
        ></v-btn>
      </template>
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
          v-if="
            $vuetify.display.lgAndUp
            && (
              eintrag.html_short !== ''
              || edit
            )
          "
          v-model="tabsHtmlVariant"
        >
          <v-tab value="html">Hilfe</v-tab>
          <v-tab value="html_short" v-if="eintrag.html_short !== '' || edit">Kurzhilfe</v-tab>
        </v-tabs>
        <div class="pa-4" v-if="!edit">
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
        <div class="pa-0" v-else>
          <v-textarea
            v-if="
              (tabsHtmlVariant === 'html' && $vuetify.display.lgAndUp)
              || (tabsEintragMetadaten === 'html' && !$vuetify.display.lgAndUp)
              || (eintrag.html_short === '' && !edit)
            "
            v-model="eintrag.html"
            auto-grow
          />
          <v-textarea
            v-else
            v-model="eintrag.html_short"
            auto-grow
          />
        </div>
      </v-col>
      <v-col v-if="tabsEintragMetadaten === 'info' || $vuetify.display.lgAndUp" class="pa-4">
        <v-table style="background:transparent;">
          <tbody>
            <tr>
              <td width="100px">Titel:</td>
              <td v-if="!edit"><b>{{ eintrag.title }}</b></td>
              <td v-else>
                <v-text-field
                  label="Titel"
                  v-model="eintrag.title"
                  class="mt-5"
                />
              </td>
            </tr>
            <tr v-if="edit">
              <td>Assets:</td>
              <td>
                {{ eintrag.assets }}
              </td>
            </tr>
            <tr v-if="edit">
              <td>Offline:</td>
              <td>
                <v-select
                  label="Offlinemodus"
                  v-model="eintrag.offline"
                  :items="offlineModi"
                  class="mt-5"
                />
              </td>
            </tr>
            <tr v-if="eintrag.code !== '0'">
              <td>Code:</td>
              <td>{{ eintrag.code }}</td>
            </tr>
            <tr v-if="eintrag.lastEdit_de !== ''">
              <td>Letzte Änderung:</td>
              <td>
                {{ eintrag.lastEdit_de }}
              </td>
            </tr>
            <tr v-if="eintrag.created_de !== ''">
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
    dialogCancelEdit: false,
    tabsEintragMetadaten: null,
    tabsHtmlVariant: null,
    edit: false,
    eintrag: {},
    eintragSnapshot: '',
    offlineModi: [
      { title: 'Nicht offline verfügbar', value: 0 },
      { title: 'Nach Abruf Offline verfügbar', value: 1 },
      { title: 'Immer Offline verfügbar', value: 2 },
    ],
  }),
  computed: {
    query() {
      return this.$router.currentRoute.value.query;
    },
    backUrl() {
      if (this.$router.currentRoute.value.query.back) {
        return `/${this.$router.currentRoute.value.query.back}`;
      }
      return '/';
    },
    allowEdit() {
      if (
        this.$vuetify.display.lgAndUp
        && this.$store.state.login.user.idRole === 2
      ) {
        return true;
      }
      return false;
    },
  },
  watch: {
    query(neu) {
      if (neu.h === '0') {
        this.eintrag = JSON.parse(JSON.stringify(
          this.$store.state.getHilfestellung.emptyTemplate,
        ));
        this.edit = true;
      }
    },
    prop_eintrag: {
      handler() {
        this.setEintrag();
      },
      deep: true,
    },
    allowEdit(neu) {
      if (!neu) {
        this.eintrag = JSON.parse(this.eintragSnapshot);
        this.edit = false;
      }
    },
    edit(neu) {
      if (neu) {
        this.eintrag.html = this.eintrag.html.split('<br />').join('');
        this.eintrag.html_short = this.eintrag.html_short.split('<br />').join('');
        this.eintragSnapshot = JSON.stringify(this.eintrag);
      } else {
        this.eintrag.html = this.eintrag.html.split('\n').join('\n<br />');
        this.eintrag.html_short = this.eintrag.html_short.split('\n').join('\n<br />');
        if (this.eintrag.id === 0) {
          this.$router.push('/');
        }
      }
    },
  },
  methods: {
    saveEintrag() {
      this.$store.dispatch('getHilfestellung/trySaveHilfestellung', {
        eintrag: this.eintrag,
      }).then(() => {
        if (!this.$store.state.getHilfestellung.lastFetch) {
          // Netzwerkfehler
          this.$store.commit('main/showAlert', {
            text: 'Es konnte keine Verbindung zum Server aufgebaut werden.',
          });
        } else if (this.$store.state.getHilfestellung.lastFetch.status !== 'success') {
          // Login ungültig
          this.$store.dispatch('login/logout');
        } else {
          this.eintrag.id = this.$store.state.getHilfestellung.lastFetch.eintrag.id;
          if (this.$store.state.getHilfestellung.lastFetch.eintrag.code) {
            this.eintrag.code = this.$store.state.getHilfestellung.lastFetch.eintrag.code;
            this.$router.push(`/?h=${this.eintrag.code}`);
          }
          localStorage.setItem(`h-${this.eintrag.code}`, JSON.stringify(this.eintrag));
          this.edit = false;
        }
      });
    },
    setEintrag() {
      this.eintrag = JSON.parse(JSON.stringify(this.prop_eintrag));
      this.eintragSnapshot = JSON.stringify(this.prop_eintrag);
      if (this.$router.currentRoute.value.query.h === '0') {
        this.edit = true;
      }
    },
    cancelEdit() {
      if (JSON.stringify(this.eintrag) === this.eintragSnapshot) {
        this.edit = false;
      } else {
        this.dialogCancelEdit = true;
      }
    },
  },
  created() {
    this.setEintrag();
  },
};
</script>
