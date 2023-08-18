import Vue from 'vue';
import VueRouter from 'vue-router'
import HomePage from './views/HomePage.vue'
import LoginPage from './views/LoginPage.vue'
import RegistrationPage from './views/RegistrationPage.vue'

Vue.use(VueRouter);

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

function isAuthenticated() {
  const user = localStorage.getItem("user");
  return user !== null;
}

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated() && to.path !== '/login') {
    next('/login');
  } else {
    next();
  }
})

export default router
