<template>
  <v-app>
    <div id="app" data-app>
      <v-toolbar dark>
        <v-toolbar-title>
          <strong>daylywins</strong><small style="font-size: 0.6em;"> Ваш ключ к успешному будущему!</small>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn v-if="!isUserAuthenticated" text @click="navigateToLogin">Войти</v-btn>
        <v-menu v-else offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn text v-bind="attrs" v-on="on">
              <span>{{ userName }}</span>
            </v-btn>
          </template>
          <v-list-item @click="logout">
            <v-list-item-title>Выход</v-list-item-title>
          </v-list-item>
        </v-menu>
      </v-toolbar>
      <router-view @loginSuccess="loginSuccess"></router-view>
    </div>
  </v-app>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      user: {
        name: '',
        authToken: false
      }
    };
  },
  computed: {
    isUserAuthenticated() {
      return this.user.authToken;
    },
    userName() {
      return this.user.name || 'ГОСТЬ';
    }
  },
  created() {
    this.user.name = localStorage.getItem('user') || { name: '' };
    this.user.authToken = localStorage.getItem('authToken') === 'true';
  },
  methods: {
    navigateToLogin() {
      if (!this.isUserAuthenticated && this.$route.path !== '/login') {
        this.$router.push('/login');
      }
    },
    loginSuccess(user) {
      this.user.name = user;
      this.user.authToken = true;
    },
    logout() {
      localStorage.removeItem('authToken');
      localStorage.removeItem('user');
      this.user.name = '';
      this.user.authToken = false;
      this.$router.push('/login');
    }
  }
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  color: #2c3e50;
  margin-top: 0;
}
</style>