import Vue from 'vue'
import VueRouter from 'vue-router'
 
Vue.use(VueRouter)

import AppHome from '../components/AppHome';
import NewCurrency from '../components/NewCurrency';
import CurrencyComponent from '../components/currency/CurrencyComponent';

const routes = [
    { path: '/', component: AppHome },
    { path: '/new-currency', component: NewCurrency},
    { path: '/currency/:id', component: CurrencyComponent, name: 'Currency'}
];

const router = new VueRouter({
    routes,
    hashbang: false,
    mode: 'history'
});

export default router;