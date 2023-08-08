<template>
  <div id="app" data-app>
    <v-toolbar color="primary" dark>
      <v-toolbar-title>
        <strong>daylywins </strong><small style="font-size: 0.6em;"> Ваш ключ к успешному будущему!</small>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-menu offset-y>
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-if="isUserAuthenticated" text v-bind="attrs" v-on="on" @click="navigateToLogin">{{ userName }}</v-btn>
        </template>
        <v-list-item @click="logout">
          <v-list-item-title>Выход</v-list-item-title>
        </v-list-item>
      </v-menu>
    </v-toolbar>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
  computed: {
    isUserAuthenticated() {
      return !!localStorage.getItem('authToken');
    },
    userName() {
      return localStorage.getItem('user') || 'Войти';
    },
    loggedInUser() {
      return localStorage.getItem('authToken');
    }
  },
  methods: {
    navigateToLogin() {
      if (!this.isUserAuthenticated) {
        this.$router.push('/login');
      }
    },
    logout() {
      localStorage.removeItem('authToken');
      localStorage.removeItem('user');
      this.$router.push('/login');
    }
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  color: #2c3e50;
  margin-top: 0;
}
</style>
