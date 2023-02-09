export default {
  namespaced: true,
  state: {
    urlApi: 'https://iu-bachelorarbeit.fkaltenbach.de/',
    inputValidationRules: {
      required: (value) => !!value || 'Bitte angeben!',
      counter: (value) => value.length <= 20 || 'Max 20 Zeichen',
      email: (value) => {
        const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return pattern.test(value) || 'ungültige E-Mail-Adresse!';
      },
      hilfestellungCode: (value) => {
        const pattern = /^[a-zA-Z0-9]{4,4}-[a-zA-Z0-9]{4,4}-[a-zA-Z0-9]{4,4}$/;
        return pattern.test(value) || 'Format: xxxx-xxxx-xxxx';
      },
    },
    alert: {
      snackbarShow: false,
      snackbarText: '',
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
  },
};
