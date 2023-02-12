<template>
  <v-row style="min-height:100%;">
    <v-col v-if="!query.h">
      <v-container
        style="min-height:100%;"
        class="d-flex flex-row flex-wrap justify-space-around align-center"
      >
        <div v-if="dialogScan">
          <qrcode-stream
            @decode="pruefeCode"
            :style="{
              'opacity': $store.state.getHilfestellung.loading ? '0.5' : '1',
            }"
          />
          <v-progress-linear
            v-if="$store.state.getHilfestellung.loading"
            indeterminate
            color="primary"
          ></v-progress-linear>
          <v-row no-gutters>
            <v-col class="d-flex flex-row">
              <v-text-field
                v-model="manualCode"
                label="Manuelle Eingabe"
                placeholder="XXXX-XXXX-XXXX"
                @keyup.enter="pruefeCode(`${$store.state.main.urlApi}?h=${manualCode}`);"
                :disabled="$store.state.getHilfestellung.loading"
                :rules="[
                  // $store.state.main.inputValidationRules.hilfestellungCode,
                ]"
              />
              <v-btn variant="tonal" class="ma-0 pt-6 pb-8"
                color="grey"
                @click="pruefeCode(`${$store.state.main.urlApi}?h=${manualCode}`);"
                :loading="$store.state.getHilfestellung.loading"
              >OK</v-btn>
            </v-col>
          </v-row>
        </div>
      </v-container>
    </v-col>
    <v-col v-if="query.h">
      <v-progress-linear
        v-if="$store.state.getHilfestellung.loading"
        indeterminate
        color="primary"
      ></v-progress-linear>
      <showEintrag :prop_eintrag="showEintrag" />
    </v-col>
  </v-row>
</template>

<script>
import { QrcodeStream } from 'vue3-qrcode-reader';
import showEintrag from '@/components/showEintrag.vue';

export default {
  name: 'ScanQR',
  components: {
    QrcodeStream,
    showEintrag,
  },
  data: () => ({
    dialogScan: true,
    manualCode: '3146967',
    showEintrag: {},
  }),
  watch: {
    manualCode(neu) {
      const neuCode = neu.toUpperCase();
      this.manualCode = neuCode;
    },
  },
  computed: {
    query() {
      return this.$router.currentRoute.value.query;
    },
  },
  methods: {
    pruefeCode(qrcode) {
      if (qrcode !== '' && !this.$store.state.getHilfestellung.loading) {
        if (qrcode.includes(`${this.$store.state.main.urlApi}?h=`)) {
          // Gültiger Code
          const code = qrcode.split(`${this.$store.state.main.urlApi}?h=`).join('');
          this.manualCode = code;
          this.ladeHilfestellung(code);
        } else {
          // Ungültiger Code
          this.$store.commit('main/showAlert', { text: 'Der Code ist ungültig.' });
        }
      }
    },
    ladeHilfestellung(code) {
      // Lade aus dem lokale Speicher
      let local = false;
      let back = '';
      if (localStorage.getItem(`h-${code}`)) {
        if (this.query.back) {
          back = `&back=${this.query.back}`;
        }
        this.$router.push(`/?h=${code}${back}`);
        this.showEintrag = JSON.parse(localStorage.getItem(`h-${code}`));
        local = true;
      }
      // Lade aus dem Backend
      this.$store.dispatch('getHilfestellung/tryGetHilfestellung', { code, router: this.$router })
        .then(() => {
          const res = this.$store.state.getHilfestellung.lastFetch;
          if (res?.status !== 'success' && code !== '0') {
            if (!res) {
              if (local) {
                this.$store.commit('getHilfestellung/addToHistory', code);
              } else {
                this.$store.commit('main/showAlert', {
                  text: 'Es konnte keine Verbindung zum Server aufgebaut werden.',
                });
              }
            } else if (local) {
              localStorage.removeItem(`h-${code}`);
              this.$store.commit('main/showAlert', {
                text: `Der Code "${code}" ist nicht mehr vorhanden.`,
              });
              if (this.query.back) {
                back = this.query.back;
              }
              this.$router.push(`${decodeURIComponent(back)}`);
            } else {
              this.$store.commit('main/showAlert', {
                text: `Zu dem Code "${code}" konnte kein Eintrag gefunden werden.`,
              });
              if (this.query.back) {
                back = this.query.back;
              }
              this.$router.push(`${decodeURIComponent(back)}`);
            }
          } else {
            let { eintrag } = res;
            if (code === '0') {
              eintrag = JSON.parse(JSON.stringify(
                this.$store.state.getHilfestellung.emptyTemplate,
              ));
            }
            if (!local) {
              // Wenn noch lokal geladen wurde, leite weiter
              this.$router.push(`/?h=${code}`);
            }
            // Speichere die Daten
            localStorage.setItem(`h-${code}`, JSON.stringify(eintrag));
            this.showEintrag = eintrag;
          }
          this.$store.dispatch('login/trySaveHistory');
        });
    },
  },
  created() {
    if (this.query.h) {
      this.ladeHilfestellung(this.query.h);
    }
  },
};
</script>
