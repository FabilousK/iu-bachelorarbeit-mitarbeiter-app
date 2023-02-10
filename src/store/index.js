import { createStore } from 'vuex';

import main from './modules/main';
import login from './modules/login';
import getHilfestellung from './modules/getHilfestellung';

export default createStore({
  modules: {
    main,
    login,
    getHilfestellung,
  },
});
