<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12">
          <v-toolbar dark flat>
            <v-toolbar-title>Войти</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form @submit.prevent="login">
              <v-text-field
                  label="Email"
                  v-model="email"
                  type="email"
                  required
              ></v-text-field>
              <v-text-field
                  label="Password"
                  v-model="password"
                  type="password"
                  required
              ></v-text-field>
              <v-row>
                <v-col>
                  <v-btn type="submit" dark block>Войти</v-btn>
                </v-col>
                <v-col>
                  <v-btn @click.prevent="redirectToRegistration" dark block>Регистрация</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: ''
    };
  },
  methods: {
    async login() {
      const storedUser = JSON.parse(localStorage.getItem("test_user"));
      if (this.email === storedUser.email && this.password === storedUser.password) {
        console.log("Authenticated successfully");
        localStorage.setItem('user', storedUser.email); // Save user object in localStorage
        localStorage.setItem('authToken', 'true');
        if (this.$router.currentRoute.path !== '/') {
          this.$router.push('/');
        }
        this.$emit("loginSuccess", storedUser.email); // Emit loginSuccess event with the user object
      } else {
        console.log("Authentication failed");
      }
      console.log("Login triggered");
    },
    redirectToRegistration() {
      this.$router.push('/registration');
    }
  }
};
</script>