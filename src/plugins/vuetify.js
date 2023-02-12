/* eslint-disable */
// Styles
import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/styles';

// Vuetify
import { createVuetify } from 'vuetify';

export default createVuetify({
  theme: {
    defaultTheme: 'light2',
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
      light2: {
        dark: false,
        colors: {
          primary: '#113B49', // #009091
          'primary-lighten-1': '#185467',
          'primary-lighten-2': '#227591',
          'primary-lighten-3': '#2786a5',
          'primary-lighten-4': '#30a7cf',
          'primary-lighten-5': '#eaf6fa',
          background: '#eaf6fa',
          info: '#2196F3',
          success: '#4CAF50',
          warning: '#FB8C00',
          error: '#B00020',
        },
      },
    },
  },
});
