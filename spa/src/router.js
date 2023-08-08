import Vue from 'vue';

Vue.use(VueRouter);


import VueRouter from 'vue-router'
import HomePage from './views/HomePage.vue'
import LoginPage from './views/LoginPage.vue'
import RegistrationPage from './views/RegistrationPage.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: LoginPage
  },
  {
    path: '/registration',
    name: 'register',
    component: RegistrationPage
  }
]

const router = new VueRouter({
  mode: 'history', base: process.env.BASE_URL,
  routes
})

// Simple mock function to check if user is authenticated
// TODO: Replace this with actual authentication check
function isAuthenticated() {
  const user = localStorage.getItem("test_user");
  return user !== null;
}

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated()) {
    next('/login');
  } else {
    next();
  }
})

export default router
