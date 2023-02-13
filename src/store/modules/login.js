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
            this.commit('getHilfestellung/setHistory', res.user.historie);
            res.user.historie_lokal.forEach((h) => {
              localStorage.setItem(`h-${h.code}`, JSON.stringify(h));
            });
            localStorage.setItem('login', JSON.stringify(res.user));
          }
        })
        .catch((error) => {
          setTimeout(() => {
            if (localStorage.getItem('login')) {
              const login = JSON.parse(localStorage.getItem('login'));
              state.user = login;
            } else {
              this.commit('main/showAlert', {
                text: `Es konnte keine Verbindung mit dem Server hergestellt werden.
                Bitte die Internetverbindung prüfen.`,
              });
            }
            console.error(error);
            state.loading = false;
          }, 1000);
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
    async trySaveHistory({ rootState, state }) {
      const body = new FormData();
      body.append('historie', JSON.stringify(rootState.getHilfestellung.history));
      body.append('token', state.user.token);
      await fetch(`${rootState.main.urlApi}api/login/?saveHistory`, { method: 'post', body })
        .then((response) => response.json())
        .then((/* res */) => {
          //
        })
        .catch((error) => {
          console.error(error);
        });
    },
  },
  modules: {
  },
};
