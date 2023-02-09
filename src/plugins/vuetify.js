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
          'primary-lighten-1': '#48bf40',
          'primary-lighten-2': '#6dcc66',
          'primary-lighten-3': '#91d98c',
          'primary-lighten-4': '#b6e6b3',
          'primary-lighten-5': '#daf2d9',
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
