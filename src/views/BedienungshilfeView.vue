<template>
  <v-container>
    <p>
      Hier finden Sie antworten auf häufig gestellte Fragen
      zur Bedienung der App.
    </p>
    <v-expansion-panels class="mt-2">
      <v-expansion-panel
        title="Qr-Code kann nicht gescannt werden"
      >
        <v-expansion-panel-text>
          Wenn das Scannen des QR-Codes nicht funktioniert,
          kann der unter dem Code angegebene Code auch
          manuell angegeben werden.
        </v-expansion-panel-text>
      </v-expansion-panel>
      <v-expansion-panel
        title="Warum kann ich mich nicht einloggen?"
      >
        <v-expansion-panel-text>
          Wenn Sie noch über keine Zugangsdaten verfügen,
          fragen Sie bei Ihrem Arbeitgeber nach.
        </v-expansion-panel-text>
      </v-expansion-panel>
    </v-expansion-panels>
  </v-container>
</template>

<script>
import { defineComponent } from 'vue';

// Components

export default defineComponent({
  name: 'BedienungshilfeView',

  components: {
  },
});
</script>
