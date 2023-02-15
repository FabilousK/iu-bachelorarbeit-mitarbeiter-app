<template>
    <div
      v-for="(cont, idx) in prop_content.filter((o) => {
        if (
          o.type !== ''
          && o.html.split('\n<br />').join('') !== ''
          && o.html.split('<br />\n').join('') !== ''
          && o.html.split('>').join('') !== ''
          && (
            o.inAbschnitt.menu < 0
            || menueAuswahlWerte[o.inAbschnitt.menu] === o.inAbschnitt.value
          )
        ) {
          return o;
        }
        return null;
      })"
      :key="idx"
    >
      <!-- DIV -->
      <div
        v-if="cont.type === 'div'"
        v-html="cont.html"
        class="px-2"
        align="justify"
      ></div>
      <!-- Nummerierte Liste -->
      <v-list
        v-if="cont.type === 'nummerierung'"
        style="background:transparent;"
      >
        <v-list-item
          v-for="(wert, idx) in cont.werte"
          :key="idx"
        >
          <v-list-item-title></v-list-item-title>
          <v-row>
            <v-col cols="1" align="center">
              <v-avatar color="primary" density="compact" size="small">
                {{ idx + 1 }}
              </v-avatar>
            </v-col>
            <v-col>
              <div v-html="wert"></div>
            </v-col>
          </v-row>
        </v-list-item>
      </v-list>
      <!-- ABSCHNITTSMENÜ TABS -->
      <div
        v-if="cont.type === 'tabs'"
        class="px-2 mt-4 mb-4"
        align="justify"
      >
        <p class="pa-4"><b>{{ cont.html }}</b></p>
        <v-tabs
          density="compact" center-active
          :mandatory="false"
          v-model="menueAuswahlWerte[cont.id]"
        >
          <v-tab
            v-for="wert in cont.werte"
            :key="wert.value"
            :value="wert.value"
          >
            {{ wert.title }}
          </v-tab>
        </v-tabs>
      </div>
      <!-- ABSCHNITTSAUSWAHL -->
      <div
        v-if="cont.type === 'auswahl'"
        class="px-2 mt-4"
        align="justify"
      >
        <v-select
          :label="cont.html"
          v-model="menueAuswahlWerte[cont.id]"
          :items="cont.werte"
          item-title="title"
          item-value="value"
        />
      </div>
      <!-- IMG -->
      <v-img
        :src="cont.html"
        v-if="cont.type === 'img'"
        :width="cont.width"
        :style="{
          'max-height': '70vh',
          'max-width': '100%',
        }"
      >
        <template v-slot:placeholder>
          <v-row
            class="fill-height ma-0"
            align="center"
            justify="center"
          >
            <v-progress-circular
              indeterminate
              color="grey lighten-5"
            ></v-progress-circular>
          </v-row>
        </template>
      </v-img>
      <!-- VIDEO -->
      <div align="center" v-if="cont.type === 'mp4'">
        <v-expansion-panels class="mt-2">
          <v-expansion-panel
            style="background:transparent;"
          >
            <v-expansion-panel-title>
              <b class="me-1">Video:</b> {{ ` ${cont.name}` }}
            </v-expansion-panel-title>
            <v-expansion-panel-text>
              <video
                width="400" controls
                style="max-width:100%;"
              >
                <track kind="captions" />
                <source :src="cont.html" type="video/mp4">
                Das Video kann nicht angezeigt werden.
                Bitte verwenden Sie einen aktuellen Internetbrower.
              </video>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
      </div>
    </div>
</template>

<script>

export default {
  name: 'showEintragRenderContent',
  components: {
  },
  props: {
    prop_content: {
      type: Array,
      default() { return []; },
    },
  },
  data: () => ({
    menueAuswahlWerte: {},
  }),
  computed: {
  },
  watch: {
  },
  methods: {
  },
  created() {
  },
};
</script>
