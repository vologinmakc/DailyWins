// src/plugins/axios.js

import Vue from 'vue';
import axios from 'axios';

// Установите здесь любые глобальные настройки, например:
axios.defaults.baseURL = 'http://127.0.0.1:8000';

Vue.prototype.$axios = axios;
