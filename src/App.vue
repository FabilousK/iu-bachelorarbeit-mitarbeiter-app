<template>
  <v-app>
    <v-navigation-drawer
      v-model="drawerMainmenu"
      location="left"
      class="pa-2"
      color="background"
      temporary
    >
      <v-row align="center">
        <v-col cols="3" align="center">
          <v-avatar color="surface-variant" size="small">
            MM
          </v-avatar>
        </v-col>
        <v-col>
          <p class="text-subtitle-1">Mitarbeiter-App</p>
          <p class="text-subtitle-2">Max Mustermann</p>
        </v-col>
      </v-row>
      <v-divider class="my-4" />
      <v-list>
        <v-list-item to="/Hilfe" prepend-icon="mdi-help-circle">
          Bedienungshilfe
        </v-list-item>
        <v-list-item @click="logout()" prepend-icon="mdi-logout-variant">
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
          prepend-icon="mdi-menu"
          @click="drawerMainmenu = true;"
          :size="isDesktop ? 'large' : 'small'"
          :stacked="!isDesktop"
          :class="{ 'flex-grow-1': !isDesktop, 'me-4': isDesktop}"
        >
          Menü
        </v-btn>
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
      <!-- Quickmenu -->
    </v-container>
    </v-toolbar>
  </v-app>
</template>

<script>

export default {
  name: 'App',

  data: () => ({
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
    logout() {
      //
    },
  },
  created() {
    this.checkMediaQueries();
    if (localStorage.getItem('history')) {
      this.$store.commit('getHilfestellung/setHistory', JSON.parse(localStorage.getItem('history')));
    }
  },
};
</script>
<style>
  html, body {
    overflow: hidden !important;
  }
</style>
