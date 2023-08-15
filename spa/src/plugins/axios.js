// src/plugins/axios.js

import Vue from 'vue';
import axios from 'axios';

// Установите здесь любые глобальные настройки, например:
axios.defaults.baseURL = 'http://127.0.0.1:8000';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Добавление перехватчика запросов
axios.interceptors.request.use(
    config => {
        const token = localStorage.getItem('authToken');
        if (token) {
            config.headers['Authorization'] = 'Bearer ' + token;
        }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

Vue.prototype.$axios = axios;
