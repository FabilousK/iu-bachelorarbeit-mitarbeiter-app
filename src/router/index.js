import { createRouter, createWebHistory } from 'vue-router';
import ScanView from '../views/ScanView.vue';
import HistorieView from '../views/HistorieView.vue';
import BedienungshilfeView from '../views/BedienungshilfeView.vue';

const routes = [
  {
    path: '/',
    name: 'Scan',
    component: ScanView,
  },
  {
    path: '/Historie',
    name: 'Historie',
    component: HistorieView,
  },
  {
    path: '/Hilfe',
    name: 'Bedienungshilfe',
    component: BedienungshilfeView,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
