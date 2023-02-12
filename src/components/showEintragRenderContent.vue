<template>
  <div>
    <div
      v-for="(cont, idx) in prop_content"
      :key="idx"
    >
      <div
        v-if="cont.type === 'div'"
        v-html="cont.html"
        class="px-2"
        align="justify"
      ></div>
      <v-img
        :src="cont.html"
        v-else-if="cont.type === 'img'"
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
      <div align="center" v-else-if="cont.type === 'mp4'">
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
      <div
        v-else
        v-html="cont.html"
      ></div>
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
