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
        'transform': $vuetify.display.lgAndUp ? 'translateY(49px)' : '',
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
          >
            <showEintragRenderContent :prop_content="insertAssetsToHTML(eintrag.html)" />
          </div>
          <div
            v-else
            class="text-body-2"
          ><showEintragRenderContent :prop_content="insertAssetsToHTML(eintrag.html_short)" /></div>
          <showEintragFeedback :eintrag="eintrag" v-if="!$vuetify.display.lgAndUp" />
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
            <tr v-if="$store.state.login.user.idRole === 2">
              <td>Aufrufe:</td>
              <td>
                {{ eintrag.aufrufe }}
              </td>
            </tr>
            <tr v-if="edit">
              <td>Assets:</td>
              <td>
                <div>
                  <v-chip
                    v-for="asset in eintrag.assets"
                    :key="asset.id"
                    class="ma-1 pa-5"
                  >
                    {{ asset.name }}.{{ asset.type }}
                    <v-btn
                      size="x-small" icon="mdi-content-copy" class="ms-4"
                      @click="copyToClipboard(`[[Asset::${asset.id}]]`);"
                      title="In die Zwischenablage"
                    ></v-btn>
                    <v-btn
                      size="x-small" icon="mdi-pencil" class="ms-2"
                      @click="dialogsEditAsset[asset.id] = true;"
                      title="Asset bearbeiten"
                    ></v-btn>
                  </v-chip>
                  <v-dialog
                    v-for="asset in eintrag.assets"
                    :key="asset.id"
                    v-model="dialogsEditAsset[asset.id]"
                    width="500px"
                  >
                    <v-card>
                      <v-card-title>
                        <v-btn
                          icon="mdi-delete" size="x-small" class="me-2"
                          @click="delAsset(asset.id)"
                        />
                        {{ asset.name }}
                      </v-card-title>
                      <v-card-text>
                        <v-text-field label="Name" v-model="asset.name"/>
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer />
                        <v-btn @click="dialogsEditAsset[asset.id] = false;">ok</v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </div>
                <v-row align="center" v-if="eintrag.id > 0">
                  <v-col>
                    <v-file-input
                      multiple
                      label="Datei/en anhängen"
                      class="mt-5"
                      v-model="neuAssets"
                      accept=".jpg,.jpeg,.png,.pdf,.mp4"
                    ></v-file-input>
                  </v-col>
                  <v-col cols="2" align="center" v-if="neuAssets.length > 0">
                    <v-btn
                      size="small"
                      @click="addAssets();"
                      :loading="loadingAddAssets"
                      icon="mdi-upload"
                    ></v-btn>
                  </v-col>
                </v-row>
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
              <td>
                <v-btn
                  size="small"
                  prepend-icon="mdi-download"
                  class="me-2"
                  @click="$store.dispatch('main/tryDownload', {
                    path: `data/assets/${eintrag.id}/qr_${eintrag.code}.pdf`,
                    name: `QR-${eintrag.code}`,
                    newTab: true,
                  })"
                >{{ eintrag.code }}</v-btn>
              </td>
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
        <showEintragFeedback :eintrag="eintrag" v-if="$vuetify.display.lgAndUp" />
      </v-col>
    </v-row>
  </div>
</template>

<script>
import showEintragRenderContent from '@/components/showEintragRenderContent.vue';
import showEintragFeedback from '@/components/showEintragFeedback.vue';

export default {
  name: 'showEintrag',
  components: {
    showEintragRenderContent,
    showEintragFeedback,
  },
  props: {
    prop_eintrag: {
      type: Object,
      default() { return {}; },
    },
  },
  data: () => ({
    dialogCancelEdit: false,
    dialogsEditAsset: {},
    dialogFeedback: false,
    tabsEintragMetadaten: null,
    tabsHtmlVariant: null,
    loadingAddAssets: false,
    edit: false,
    eintrag: {},
    eintragSnapshot: '',
    offlineModi: [
      { title: 'Nicht offline verfügbar', value: 0 },
      { title: 'Nach Abruf Offline verfügbar', value: 1 },
      { title: 'Immer Offline verfügbar', value: 2 },
    ],
    neuAssets: [],
    feedbackLoading: false,
    feedbackValid: false,
    feedbackRating: 0,
    feedbackText: '',
    feedbackDone: false,
  }),
  computed: {
    query() {
      return this.$router.currentRoute.value.query;
    },
    backUrl() {
      if (this.$router.currentRoute.value.query.back) {
        return `${decodeURIComponent(this.$router.currentRoute.value.query.back)}`;
      }
      return '/';
    },
    allowEdit() {
      if (
        /* this.$vuetify.display.lgAndUp
        && */ this.$store.state.login.user.idRole === 2
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
    async trySendFeedback() {
      this.feedbackLoading = true;
      const body = new FormData();
      body.append('token', this.$store.state.login.user.token);
      body.append('idHilfestellung', this.eintrag.id);
      body.append('rating', this.feedbackRating);
      body.append('text', this.feedbackText);
      await fetch(`${this.$store.state.main.urlApi}api/?sendFeedback`, { method: 'POST', body })
        .then((response) => response.json())
        .then((res) => {
          this.feedbackLoading = false;
          if (res.status !== 'success') {
            //
          } else {
            this.dialogFeedback = false;
            this.$store.commit('main/showAlert', {
              text: 'Vielen Dank für Ihr Feedback!',
            });
            this.feedbackDone = true;
          }
        })
        .catch((error) => {
          this.feedbackLoading = false;
          console.error(error);
        });
    },
    insertAssetsToHTML(altHtml) {
      const htmlR = altHtml.split('[[');
      const content = [];
      htmlR.forEach((t) => {
        const t2 = t.split(']]');
        t2.forEach((t3) => {
          content.push({
            type: 'div',
            html: t3,
            inAbschnitt: { menu: -1, value: -1 },
          });
        });
      });
      let inAbschnitt = { menu: -1, value: -1 };
      content.forEach((t, idx) => {
        if (idx % 2) {
          const tR = t.html.split('::');
          if (tR.length > 0) {
            if (tR[0] === 'Asset' && tR.length > 0) {
              const asset = this.eintrag.assets.filter((o) => o.id === parseInt(tR[1], 10));
              if (asset.length > 0) {
                content[idx].type = asset[0].type;
                let neuHtml = '';
                if (['jpg', 'jpeg', 'png'].includes(asset[0].type)) {
                  // Bild- oder Videodatei
                  content[idx].type = 'img';
                  neuHtml = `${this.$store.state.main.urlApi}api/data/`;
                  neuHtml += `assets/${this.eintrag.id}/${asset[0].id}.${asset[0].type}`;
                  content[idx].name = asset[0].name;
                  content[idx].html = neuHtml;
                  content[idx].width = '100%';
                  if (tR[2]) {
                    [, , content[idx].width] = tR;
                  }
                } else if (['mp4'].includes(asset[0].type)) {
                  neuHtml = `${this.$store.state.main.urlApi}api/data/`;
                  neuHtml += `assets/${this.eintrag.id}/${asset[0].id}.${asset[0].type}`;
                  content[idx].name = asset[0].name;
                  content[idx].html = neuHtml;
                } else {
                  // Verlinkung zur Datei
                  neuHtml = `<a target="_blank" href="${this.$store.state.main.urlApi}api/data/`;
                  neuHtml += `assets/${this.eintrag.id}/${asset[0].id}.${asset[0].type}">`;
                  neuHtml += `${asset[0].name}.${asset[0].type}</a>`;
                  content[idx].html = neuHtml;
                }
              }
            } else if (tR[0] === 'Menü' && tR.length > 0) {
              content[idx].type = 'menü';
              const werte = [];
              tR.forEach((tR2, idx2) => {
                if (idx2 >= 3) {
                  const neuWert = tR2.split('|');
                  werte.push({ title: neuWert[1], value: neuWert[0] });
                }
              });
              content[idx].werte = werte;
              [, content[idx].id] = tR;
              [, , content[idx].html] = tR;
            } else if (tR[0] === 'Beginn' && tR.length > 0) {
              content[idx].type = '';
              [, inAbschnitt.menu, inAbschnitt.value] = tR;
            } else if (tR[0] === 'Ende' && tR.length > 0) {
              content[idx].type = '';
              inAbschnitt = { menu: -1, value: -1 };
            }
          }
        }
        content[idx].inAbschnitt = JSON.parse(JSON.stringify(inAbschnitt));
      });
      return content;
    },
    async addAssets() {
      this.loadingAddAssets = true;
      const body = new FormData();
      let i = 1;
      this.neuAssets.forEach((a) => {
        body.append(`upload-${i}`, a);
        i += 1;
      });
      body.append('idHilfestellung', this.eintrag.id);
      body.append('token', this.$store.state.login.user.token);
      await fetch(`${this.$store.state.main.urlApi}api/?addAssets`, { method: 'post', body })
        .then((response) => response.json())
        .then((res) => {
          this.loadingAddAssets = false;
          if (res.status === 'success') {
            res.assets.forEach((a) => {
              this.eintrag.assets.push(a);
            });
          }
        })
        .catch((error) => {
          this.loadingAddAssets = false;
          console.error(error);
        });
    },
    delAsset(id) {
      this.eintrag.assets.forEach((a, idx) => {
        if (a.id === id) {
          this.eintrag.assets.splice(idx, 1);
          this.dialogsEditAsset[id] = false;
        }
      });
    },
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
          // window.location.reload();
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
    copyToClipboard(text) {
      navigator.clipboard.writeText(text);
    },
  },
  created() {
    this.setEintrag();
  },
};
</script>
