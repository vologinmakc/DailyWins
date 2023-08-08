
// Initializing test user in localStorage
const testUserKey = "test_user";
const testUser = {
  email: "admin@admin.com",
  password: "123456"
};
if (!localStorage.getItem(testUserKey)) {
  localStorage.setItem(testUserKey, JSON.stringify(testUser));
}
import './globalStyles.css';
import Vue from 'vue'
import App from './App.vue'
// eslint-disable-next-line no-unused-vars
import router from './router'


import vuetify from './vuetify';

new Vue({
  render: h => h(App),
  router,
  vuetify,
}).$mount('#app');

