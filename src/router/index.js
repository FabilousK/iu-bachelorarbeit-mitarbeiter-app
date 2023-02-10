import { createRouter, createWebHistory } from 'vue-router';
import ScanView from '../views/ScanView.vue';
import HistorieView from '../views/HistorieView.vue';
import RaumplanView from '../views/RaumplanView.vue';
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
    path: '/Raumplan',
    name: 'Raumplan',
    component: RaumplanView,
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
