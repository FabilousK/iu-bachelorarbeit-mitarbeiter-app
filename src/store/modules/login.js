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
    async tryLogin({ rootState, state }, { username, password, token }) {
      state.loading.login = true;
      const body = new FormData();
      body.append('nn', username);
      body.append('pw', password);
      body.append('token', token);
      await fetch(`${rootState.main.urlApi}api/login/`, { method: 'post', body })
        .then((response) => response.json())
        .then((res) => {
          state.loading.login = false;
          if (res.status !== 'success') {
            if (token !== '') {
              this.commit('main/showAlert', {
                text: 'Die Sitzung ist abgelaufen. Sie wurden automatisch ausgeloggt.',
              });
            } else {
              this.commit('main/showAlert', {
                text: 'Logindaten ungültig',
              });
            }
            this.dispatch('login/logout');
          } else {
            state.user = res.user;
            localStorage.setItem('login', JSON.stringify(res.user));
          }
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
