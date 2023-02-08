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
            <span class="text-subtitle-1">CJ</span>
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
      overflow: 'auto',
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
  },
};
</script>
<style>
  html, body {
    overflow: hidden !important;
  }
</style>
