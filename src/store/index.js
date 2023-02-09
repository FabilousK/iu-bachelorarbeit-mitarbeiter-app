import { createStore } from 'vuex';

import main from './modules/main';
import getHilfestellung from './modules/getHilfestellung';

export default createStore({
  modules: {
    main,
    getHilfestellung,
  },
});
