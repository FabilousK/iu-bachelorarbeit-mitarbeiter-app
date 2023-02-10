export default {
  namespaced: true,
  state: {
    loading: false,
    lastFetch: null,
    history: [],
  },
  getters: {
  },
  mutations: {
    setHistory(state, value) {
      state.history = value;
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
    async tryGetHilfestellung({ commit, state, rootState }, { code }) {
      state.loading = true;
      const body = new FormData();
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
