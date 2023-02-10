export default {
  namespaced: true,
  state: {
    loading: {
      login: false,
    },
    user: {
      id: 0,
      token: '',
      idRole: 0,
    },
  },
  getters: {
  },
  mutations: {
  },
  actions: {
    async tryLogin({ rootState, state }, { username, password }) {
      state.loading.login = true;
      const body = new FormData();
      body.append('nn', username);
      body.append('pw', password);
      await fetch(`${rootState.main.urlApi}api/login/`, { method: 'post', body })
        .then((response) => response.json())
        .then((res) => {
          state.loading.login = false;
          state.user = res.user;
        })
        .catch((error) => {
          console.error(error);
          state.loading = false;
        });
    },
    logout({ state }) {
      state.user = {
        id: 0,
        token: '',
        idRole: 0,
      };
      localStorage.clear();
      this.commit('getHilfestellung/setHistory', []);
    },
  },
  modules: {
  },
};
