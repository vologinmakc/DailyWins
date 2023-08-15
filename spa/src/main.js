import Vue from 'vue';
import App from './App.vue';
import '@mdi/font/css/materialdesignicons.min.css';

import {globalMixins} from '@/globalMixins';

Vue.mixin(globalMixins);

import './globalStyles.css';
import './plugins/axios';
import router from './router';
import vuetify from './vuetify';

new Vue({
    render: h => h(App),
    router,
    vuetify,
}).$mount('#app');
