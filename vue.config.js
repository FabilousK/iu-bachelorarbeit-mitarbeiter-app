const { defineConfig } = require('@vue/cli-service');

module.exports = defineConfig({
  transpileDependencies: true,

  pluginOptions: {
    vuetify: {
      // https://github.com/vuetifyjs/vuetify-loader/tree/next/packages/vuetify-loader
    },
  },
  pwa: {
    workboxOptions: {
      navigateFallback: 'index.html',
      skipWaiting: true,
      clientsClaim: true,
    },
    themeColor: '#ffffff',
    name: 'IU Bachelor',
  },
});
