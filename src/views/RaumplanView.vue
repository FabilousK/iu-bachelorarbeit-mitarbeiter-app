<template>
  <v-container>
    <v-expansion-panels class="mt-2" v-model="expansionGebaeude">
      <v-expansion-panel
        v-for="geb in gebaeude" :key="geb.id"
        :value="geb.id"
      >
        <v-expansion-panel-title>
          {{ geb.name }}
          <span v-if="geb.titel">&nbsp;- {{ geb.titel }}</span>
        </v-expansion-panel-title>
        <v-expansion-panel-text>
          <v-btn
            v-for="code in geb.codes" :key="code.code"
            :to="`/?h=${code.code}&back=${
              encodeURIComponent(`/Raumplan?e=${geb.id}-0-0`)
            }`"
            prepend-icon="mdi-information"
          >{{ code.titel }}</v-btn>
          <v-expansion-panels class="mt-2" v-model="expansionEbene">
            <v-expansion-panel
              v-for="ebene in geb.ebenen" :key="ebene.id"
              :value="ebene.id"
            >
              <v-expansion-panel-title>
                {{ ebene.name }}
                <span v-if="ebene.titel">&nbsp;- {{ ebene.titel }}</span>
              </v-expansion-panel-title>
              <v-expansion-panel-text>
                <v-btn
                  v-for="code in ebene.codes" :key="code.code"
                  :to="`/?h=${code.code}&back=${
                    encodeURIComponent(`/Raumplan?e=${geb.id}-${ebene.id}-0`)
                  }`"
                  prepend-icon="mdi-information"
                >{{ code.titel }}</v-btn>
                <v-expansion-panels class="mt-2" v-model="expansionRaum">
                  <v-expansion-panel
                    v-for="raum in ebene.raeume" :key="raum.id"
                    :value="raum.id"
                  >
                    <v-expansion-panel-title>
                      {{ raum.name }}
                      <span v-if="raum.titel">&nbsp;- {{ raum.titel }}</span>
                    </v-expansion-panel-title>
                    <v-expansion-panel-text>
                      <v-btn
                        size="small"
                        v-for="code in raum.codes" :key="code.code"
                        :to="`/?h=${code.code}&back=${
                          encodeURIComponent(`/Raumplan?e=${geb.id}-${ebene.id}-${raum.id}`)
                        }`"
                        prepend-icon="mdi-information"
                      >{{ code.titel }}</v-btn>
                    </v-expansion-panel-text>
                  </v-expansion-panel>
                </v-expansion-panels>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-expansion-panel-text>
      </v-expansion-panel>
    </v-expansion-panels>
  </v-container>
</template>

<script>
import { defineComponent } from 'vue';

// Components

export default defineComponent({
  name: 'RaumplanView',
  components: {
  },
  data: () => ({
    expansionGebaeude: null,
    expansionEbene: null,
    expansionRaum: null,
    gebaeude: [
      {
        id: 1,
        name: 'Hauptverwaltung',
        titel: '',
        codes: [],
        ebenen: [
          {
            id: 1,
            name: 'EG',
            titel: '',
            codes: [],
            raeume: [
              {
                id: 1, name: 'Küche', titel: '', codes: [{ titel: 'Saeco Kaffeemaschine reinigen', code: '4711' }],
              },
              {
                id: 2, name: '008', titel: 'IT Service', codes: [],
              },
            ],
          },
          {
            id: 2,
            name: '1. OG',
            titel: '',
            codes: [],
            raeume: [
              {
                id: 3, name: 'Küche', titel: '', codes: [{ titel: 'Saeco Kaffeemaschine reinigen', code: '4711' }],
              },
              {
                id: 4, name: '018', titel: 'Finanzen 1', codes: [],
              },
            ],
          },
        ],
      },
      {
        id: 2,
        name: 'Verwaltung Köln',
        titel: '',
        codes: [],
        ebenen: [
          {
            id: 3,
            name: 'EG',
            titel: '',
            codes: [],
            raeume: [
              {
                id: 5, name: 'Küche', titel: '', codes: [{ titel: 'Saeco Kaffeemaschine reinigen', code: '4711' }],
              },
              {
                id: 6, name: '008', titel: 'IT Service', codes: [],
              },
            ],
          },
        ],
      },
    ],
  }),
  computed: {
    query() {
      return this.$router.currentRoute.value.query;
    },
  },
  watch: {
    query() {
      this.openFromQuery();
    },
  },
  methods: {
    openFromQuery() {
      if (this.query.e) {
        const e = this.query.e.split('-');
        if (e.length === 3) {
          if (parseInt(e[0], 10) > 0) {
            this.expansionGebaeude = parseInt(e[0], 10);
          }
          if (parseInt(e[1], 10) > 0) {
            this.expansionEbene = parseInt(e[1], 10);
          }
          if (parseInt(e[2], 10) > 0) {
            this.expansionRaum = parseInt(e[2], 10);
          }
          // const neuUrl = this.$router.currentRoute.value.fullPath
          //  .split(`e=${this.query.e}`).join('e=0');
          // this.$router.push(neuUrl);
        }
      }
    },
  },
  created() {
    this.openFromQuery();
  },
});
</script>
