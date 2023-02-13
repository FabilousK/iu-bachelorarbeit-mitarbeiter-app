export default {
  namespaced: true,
  state: {
    loading: false,
    loadingSave: false,
    lastFetch: null,
    history: [],
    emptyTemplate: {
      id: 0,
      code: '0',
      assets: [],
      title: 'Neuer Eintrag',
      html: '',
      html_short: '',
      created_de: '',
      lastEdit_de: '',
      offline: 1,
      fetched_de: '',
    },
  },
  getters: {
  },
  mutations: {
    setHistory(state, value) {
      state.history = value;
      localStorage.setItem('history', JSON.stringify(state.history));
    },
    addToHistory(state, code) {
      if (state.history.indexOf(code) >= 0) {
        state.history.splice(state.history.indexOf(code), 1);
      }
      state.history.push(code);
      localStorage.setItem('history', JSON.stringify(state.history));
    },
  },
  actions: {
    async trySaveHilfestellung({ rootState, state }, { eintrag }) {
      state.lastFetch = null;
      state.loadingSave = true;
      const body = new FormData();
      body.append('token', rootState.login.user.token);
      body.append('eintrag', JSON.stringify(eintrag));
      await fetch(`${rootState.main.urlApi}api/?save`, { method: 'post', body })
        .then((response) => response.json())
        .then((res) => {
          state.lastFetch = res;
          state.loadingSave = false;
        })
        .catch((error) => {
          console.error(error);
          state.loadingSave = false;
        });
    },
    async tryGetHilfestellung({ commit, state, rootState }, { code }) {
      state.lastFetch = null;
      state.loading = true;
      const body = new FormData();
      let idLogin = rootState.login.user.id;
      if (localStorage.getItem('login') && idLogin === 0) {
        const lokalLogin = JSON.parse(localStorage.getItem('login'));
        idLogin = lokalLogin.id;
      }
      body.append('id_login', idLogin);
      body.append('code', code);
      await fetch(`${rootState.main.urlApi}api/?getFromId`, { method: 'post', body })
        .then((response) => response.json())
        .then((res) => {
          state.lastFetch = res;
          state.loading = false;
          if (res.status === 'success') {
            commit('addToHistory', code);
          }
        })
        .catch((error) => {
          console.error(error);
          state.loading = false;
        });
    },
    async tryGetLokaleHilfestellungen({ rootState }) {
      await fetch(`${rootState.main.urlApi}api/?getAllLocals`, { method: 'get' })
        .then((response) => response.json())
        .then((res) => {
          if (res.eintraege) {
            res.eintraege.forEach((e) => {
              localStorage.setItem(`h-${e.code}`, JSON.stringify(e));
            });
          }
        })
        .catch((error) => {
          console.error(error);
        });
    },
  },
  modules: {
  },
};
