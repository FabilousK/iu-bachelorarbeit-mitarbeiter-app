<template>
  <v-app>
    <v-dialog
      v-model="dialogLogin"
      width="500px"
    >
      <v-card>
        <v-card-title>Login</v-card-title>
        <v-card-text>
          <v-text-field
            label="Benutzername"
            v-model="loginUsername"
          />
          <v-text-field
            label="Passwort"
            type="password"
            v-model="loginPassword"
            @keyup.enter="login('');"
          />
        </v-card-text>
        <v-card-actions>
          <v-btn size="small" @click="dialogLogin = false;">abbrechen</v-btn>
          <v-spacer />
          <v-btn
            size="small"
            color="primary"
            @click="login('');"
            :loading="$store.state.login.loading.login"
          >anmelden</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-navigation-drawer
      v-model="drawerMainmenu"
      location="left"
      class="pa-2"
      color="background"
      temporary
    >
      <v-row align="center" v-if="$store.state.login.user.id > 0">
        <v-col cols="3" align="center">
          <v-avatar color="surface-variant" size="small">
            {{ $store.state.login.user.initialien }}
          </v-avatar>
        </v-col>
        <v-col>
          <p class="text-subtitle-1">Mitarbeiter-App</p>
          <p class="text-subtitle-2">{{ $store.state.login.user.name }}</p>
        </v-col>
      </v-row>
      <v-divider v-if="$store.state.login.user.id > 0" class="my-4" />
      <v-list>
        <v-list-item
          v-if="$vuetify.display.lgAndUp && $store.state.login.user.idRole === 2"
          to="/?h=0" prepend-icon="mdi-plus-box">
          Neuer Eintrag
        </v-list-item>
        <v-list-item
          v-if="!$vuetify.display.mdAndUp && $store.state.login.user.id > 0"
          to="/Raumplan" prepend-icon="mdi-floor-plan">
          Raumplan
        </v-list-item>
        <v-list-item to="/Hilfe" prepend-icon="mdi-help-circle">
          Bedienungshilfe
        </v-list-item>
        <v-list-item
          v-if="$store.state.login.user.id <= 0"
          @click="dialogLogin = true; drawerMainmenu = false;"
          prepend-icon="mdi-login-variant"
        >
          Einloggen
        </v-list-item>
        <v-list-item
          v-if="$store.state.login.user.id > 0"
          @click="logout()"
          prepend-icon="mdi-logout-variant"
        >
          Ausloggen
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-snackbar
      v-model="$store.state.main.alert.snackbarShow"
      multi-line
    >
      {{ $store.state.main.alert.snackbarText }}

      <template v-slot:actions>
        <v-btn
          color="red"
          variant="text"
          @click="$store.commit('main/hideAlert');"
        >
          X
        </v-btn>
      </template>
    </v-snackbar>
    <v-main
      :style="mainwrapperStyle">
      <router-view/>
    </v-main>
    <v-toolbar
      elevation="4"
      color="primary"
      :style="toolbarStyle"
    >
    <v-container fluid :class="{
      'd-flex': true,
      'flex-row': true,
      'justify-start': isDesktop,
      'justify-space-around': $vuetify.display.mdAndDown && !$vuetify.display.smAndDown,
      'justify-space-between': $vuetify.display.smAndDown,
      'align-center': true,
      'px-0': !isDesktop,
    }">
      <!-- Quickmenu -->
      <v-btn
          v-if="!$vuetify.display.lgAndUp"
          prepend-icon="mdi-menu"
          @click="drawerMainmenu = true;"
          :size="isDesktop ? 'large' : 'small'"
          :stacked="!isDesktop"
          :class="{ 'flex-grow-1': !isDesktop, 'me-4': isDesktop}"
        >
          Menü
        </v-btn>
        <v-btn
        v-if="$vuetify.display.lgAndUp"
          icon="mdi-menu"
          @click="drawerMainmenu = true;"
          :size="isDesktop ? 'large' : 'small'"
          :stacked="!isDesktop"
          :class="{ 'flex-grow-1': !isDesktop, 'me-4': isDesktop}"
        />
        <v-divider
          v-if="$vuetify.display.lgAndUp"
          class="mx-4 me-8"
          vertical
        ></v-divider>
        <v-btn
          prepend-icon="mdi-scan-helper"
          to="/"
          :size="isDesktop ? 'large' : 'small'"
          :stacked="!isDesktop"
          :class="{ 'flex-grow-1': !isDesktop, 'mx-4': isDesktop }"
        >
          Scan
        </v-btn>
        <v-btn
          prepend-icon="mdi-history"
          to="/Historie"
          :size="isDesktop ? 'large' : 'small'"
          :stacked="!isDesktop"
          :class="{ 'flex-grow-1': !isDesktop, 'mx-4': isDesktop }"
        >
          Historie
        </v-btn>
        <v-btn
          v-if="$vuetify.display.mdAndUp && $store.state.login.user.id > 0"
          prepend-icon="mdi-floor-plan"
          to="/Raumplan"
          :size="isDesktop ? 'large' : 'small'"
          :stacked="!isDesktop"
          :class="{ 'flex-grow-1': !isDesktop, 'mx-4': isDesktop }"
        >
          Raumplan
        </v-btn>
      <!-- Quickmenu -->
    </v-container>
    </v-toolbar>
  </v-app>
</template>

<script>

export default {
  name: 'App',

  data: () => ({
    dialogLogin: false,
    drawerMainmenu: false,
    mainwrapperStyle: {
      maxHeight: 'calc(100vh - 65px)',
      overflowY: 'auto',
      overflowX: 'hidden',
    },
    toolbarStyle: {
      position: 'fixed',
      bottom: '0px',
      left: '0px',
      width: '100%',
    },
    loginUsername: '',
    loginPassword: '',
  }),
  computed: {
    vuetifyBreakpoints() {
      return this.$vuetify.display;
    },
    isDesktop() {
      return this.$vuetify.display.lgAndUp;
    },
  },
  watch: {
    vuetifyBreakpoints: {
      handler() {
        this.checkMediaQueries();
      },
      deep: true,
    },
  },
  methods: {
    checkMediaQueries() {
      if (this.isDesktop) {
        // Desktop-PC Auflösung
        this.mainwrapperStyle.marginTop = '65px';
        delete this.toolbarStyle.bottom;
        this.toolbarStyle.top = '0px';
      } else {
        // Mobile Auflösung
        delete this.mainwrapperStyle.marginTop;
        delete this.toolbarStyle.top;
        this.toolbarStyle.bottom = '0px';
      }
    },
    login(token) {
      this.$store.dispatch('login/tryLogin', {
        username: this.loginUsername,
        password: this.loginPassword,
        token,
      }).then(() => {
        if (this.$store.state.login.user.id > 0) {
          this.loginUsername = '';
          this.loginPassword = '';
          this.dialogLogin = false;
        }
      });
    },
    logout() {
      this.$store.dispatch('login/logout');
    },
  },
  created() {
    this.checkMediaQueries();
    if (localStorage.getItem('history')) {
      this.$store.commit('getHilfestellung/setHistory', JSON.parse(localStorage.getItem('history')));
    }
    // Alle lokal erzwungenen Hilfestellungen laden:
    this.$store.dispatch('getHilfestellung/tryGetLokaleHilfestellungen');
    // Wenn Loginsitzung vorhanden, lade Nutzerdaten
    if (localStorage.getItem('login')) {
      const userdata = JSON.parse(localStorage.getItem('login'));
      this.login(userdata.token);
    }
  },
};
</script>
<style>
  html, body {
    overflow: hidden !important;
  }
</style>
