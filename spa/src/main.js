import Vue from 'vue';
import App from './App.vue';
import {TASK_STATUSES} from './constants';
import '@mdi/font/css/materialdesignicons.min.css';

// Initializing test user in localStorage
const testUserKey = "test_user";
const testUser = {
    email: "admin@admin.com",
    password: "123456"
};

import {globalMixins} from '@/globalMixins';

Vue.mixin(globalMixins);

// Тестовая задача
const testTask = [
    {
        name: 'Разработка веб-приложения',
        subTasks: [
            {
                id: 1,
                name: 'Анализ требований',
                description: 'Изучение требований к веб-приложению от заказчика',
                status: TASK_STATUSES.TASK_COMPLETED
            },
            {
                id: 2,
                name: 'Проектирование базы данных',
                description: 'Создание структуры базы данных для веб-приложения',
                status: TASK_STATUSES.TASK_COMPLETED
            },
            {
                id: 3,
                name: 'Разработка пользовательского интерфейса',
                description: 'Создание интерактивного пользовательского интерфейса с использованием HTML, CSS и JavaScript',
                status: TASK_STATUSES.TASK_IN_PROGRESS
            },
            {
                id: 4,
                name: 'Разработка серверной логики',
                description: 'Написание серверного кода для обработки запросов и взаимодействия с базой данных',
                status: TASK_STATUSES.TASK_NOT_STARTED
            }
        ]
    },
    {
        name: 'Тестирование мобильного приложения',
        subTasks: [
            {
                id: 1,
                name: 'Планирование тестирования',
                description: 'Составление плана тестирования мобильного приложения',
                status: TASK_STATUSES.TASK_COMPLETED
            },
            {
                id: 2,
                name: 'Написание тестовых сценариев',
                description: 'Создание детальных сценариев для тестирования различных функциональных возможностей приложения',
                status: TASK_STATUSES.TASK_COMPLETED
            },
            {
                id: 3,
                name: 'Выполнение функционального тестирования',
                description: 'Проведение тестирования функциональности мобильного приложения',
                status: TASK_STATUSES.TASK_IN_PROGRESS
            },
            {
                id: 4,
                name: 'Выполнение нагрузочного тестирования',
                description: 'Проверка производительности мобильного приложения при большой нагрузке',
                status: TASK_STATUSES.TASK_NOT_STARTED
            }
        ]
    }
];

/* Тестовые данные по бэк в ремонте */
const tasksKey = 'userTasks';
localStorage.setItem(tasksKey, JSON.stringify(testTask));
if (!localStorage.getItem(testUserKey)) {
    localStorage.setItem(testUserKey, JSON.stringify(testUser));
}

import './globalStyles.css';
import './plugins/axios';
import router from './router';
import vuetify from './vuetify';

new Vue({
    render: h => h(App),
    router,
    vuetify,
}).$mount('#app');
