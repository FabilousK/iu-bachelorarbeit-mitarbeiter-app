<template>
  <v-row style="min-height:100%;">
    <v-col v-if="!query.h">
      <v-container
        style="min-height:100%;"
        class="d-flex flex-row flex-wrap justify-space-around align-center"
      >
        <div v-if="dialogScan">
          <qrcode-stream
            @decode="onDecode"
          />
          <v-text-field
            v-model="manualCode"
            label="Manuelle Eingabe"
          />
        </div>
      </v-container>
    </v-col>
    <v-col v-if="query.h">
      <v-container>
        {{ query }}
      </v-container>
    </v-col>
  </v-row>
</template>

<script>
import { QrcodeStream } from 'vue3-qrcode-reader';

export default {
  name: 'ScanQR',
  components: {
    QrcodeStream,
  },
  data: () => ({
    dialogScan: true,
    url: 'https://iu-bachelorarbeit.fkaltenbach.de/?h=',
    errors: {
      qrcodeUngueltig: false,
    },
    manualCode: '',
  }),
  computed: {
    query() {
      return this.$router.currentRoute.value.query;
    },
  },
  methods: {
    onDecode(code) {
      if (code !== '') {
        if (code.includes(this.url)) {
          // Gültiger Code
          console.log(code);
          this.errors.qrcodeUngueltig = false;
          const id = code.split(this.url).join('');
          this.$router.push(`/?h=${id}`);
        } else {
          // Ungültiger Code
          this.$store.commit('showAlert', { text: 'Der gescannte QR-Code ist ungültig.' });
        }
      }
    },
  },
  created() {
  },
};
</script>
