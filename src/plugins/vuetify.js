/* eslint-disable */
// Styles
import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/styles';

// Vuetify
import { createVuetify } from 'vuetify';

export default createVuetify({
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        dark: false,
        colors: {
          primary: '#3EA436',
          background: '#ecf8ec',
          info: '#2196F3',
          success: '#4CAF50',
          warning: '#FB8C00',
          error: '#B00020',
        },
      },
    },
  },
});
