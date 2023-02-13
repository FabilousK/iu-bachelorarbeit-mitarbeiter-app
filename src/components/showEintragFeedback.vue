<template>
  <div align="center">
    <v-banner
    v-if="
      !feedbackDone
      && [1, 2].includes($store.state.login.user.idRole)
    "
    lines="four"
    icon="mdi-message-draw"
    color="success"
    class="my-4"
    style="max-width:600px;"
  >
    <v-banner-text align="left">
      <b>Waren diese Informationen hilfreich?</b><br />
      Bitte nehmen Sie sich einen Moment Zeit und bewerten
      diese Seite.<br />
      <div align="right" class="mt-4">
        <v-btn
          color="success" size="small"
          variant="text"
          @click="dialogFeedback = true">Feedback geben</v-btn>
        <v-btn
          color="success" size="small"
          variant="text"
          prepend-icon="mdi-phone-alert"
          href="tel:01234 567 890 12">Support kontaktieren</v-btn>
      </div>
    </v-banner-text>

    <!-- <template v-slot:actions>
      <v-btn @click="dialogFeedback = true">Feedback geben</v-btn>
    </template> -->
  </v-banner>
  <v-dialog
    v-model="dialogFeedback"
    width="500px"
    scrollable
  >
    <v-card>
      <v-card-title>Feedback geben</v-card-title>
      <v-card-text>
        <v-form v-model="feedbackValid">
          <v-rating
            v-model="feedbackRating"
            class="ma-2"
            density="compact"
            label="Ihre Bewertung"
            color="primary"
          ></v-rating>
          <v-textarea
            auto-grow
            v-model="feedbackText"
            label="Ihre Meinung"
            :rules="[
              $store.state.main.inputValidationRules.required,
            ]"
          />
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-btn
          size="small"
          @click="dialogFeedback = false"
        >Abbrechen</v-btn>
        <v-spacer />
        <v-btn
          size="small"
          color="success"
          :disabled="!feedbackValid"
          @click="trySendFeedback();"
          :loading="feedbackLoading"
        >Senden</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
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
    dialogFeedback: false,
    feedbackLoading: false,
    feedbackValid: false,
    feedbackRating: 0,
    feedbackText: '',
    feedbackDone: false,
  }),
  computed: {
  },
  watch: {
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
  },
  created() {
  },
};
</script>
