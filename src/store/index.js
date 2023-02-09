import { createStore } from 'vuex';

import main from './modules/main';

export default createStore({
  state: {
    inputValidationRules: {
      required: (value) => !!value || 'Bitte angeben!',
      counter: (value) => value.length <= 20 || 'Max 20 characters',
      email: (value) => {
        const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return pattern.test(value) || 'ungültige E-Mail-Adresse!';
      },
    },
    alert: {
      snackbarShow: false,
      snackbarText: 'Lorem ipsum dolor sit amet...',
    },
  },
  getters: {
  },
  mutations: {
    showAlert(state, { text }) {
      state.alert.snackbarShow = false;
      state.alert.snackbarText = text;
      state.alert.snackbarShow = true;
    },
    hideAlert(state) {
      state.alert.snackbarShow = false;
    },
  },
  actions: {
  },
  modules: {
    main,
  },
});
