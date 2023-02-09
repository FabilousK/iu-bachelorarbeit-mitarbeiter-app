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
  },
  actions: {
    async tryGetHilfestellung({ state, rootState }, { code }) {
      state.loading = true;
      const body = new FormData();
      body.append('code', code);
      await fetch(`${rootState.main.urlApi}api/`, { method: 'post', body })
        .then((response) => response.json())
        .then((res) => {
          state.lastFetch = res;
          state.loading = false;
          if (state.history.indexOf(code) >= 0) {
            state.history.splice(state.history.indexOf(code), 1);
          }
          state.history.push(code);
          localStorage.setItem('history', JSON.stringify(state.history));
        })
        .catch((error) => {
          console.error(error);
          state.loading = false;
        });
    },
  },
  modules: {
  },
};
