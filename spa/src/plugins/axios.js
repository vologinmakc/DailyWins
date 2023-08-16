// src/plugins/axios.js

import Vue from 'vue';
import axios from 'axios';
import { EventBus } from '@/event-bus.js';

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

// Добавление перехватчика ответов
axios.interceptors.response.use(
    response => {
        if (response.data.result_code === 'COMPLETE') {
            return response;
        }
    },
    error => {
        if (error.response) {
            if (error.response.status === 500 && error.response.data.result_code === 'ERROR') {
                EventBus.$emit('displaySnackbar', error.response.data.data || 'Ошибка сервера. Пожалуйста, попробуйте позже.', 'error');
            } else if (error.response.status === 500) {
                EventBus.$emit('displaySnackbar', 'Ошибка сервера. Пожалуйста, попробуйте позже.', 'error');
            } else {
                EventBus.$emit('displaySnackbar', error.message, 'error');
            }
        } else {
            EventBus.$emit('displaySnackbar', 'Ошибка сети или другая неизвестная ошибка', 'error');
        }
        return Promise.reject(error);
    }
);

Vue.prototype.$axios = axios;
