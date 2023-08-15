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
              <v-alert v-if="errorMessage" type="error">{{ errorMessage }}</v-alert>
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
      password: '',
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      try {
        const response = await this.$axios.post("/api/login", {
          username: this.email,
          password: this.password
        });
        if (response.data && response.data.access_token) {
          localStorage.setItem('authToken', response.data.access_token);
          const userResponse = await this.$axios.get("/api/user/me");
          if (userResponse.data) {
            localStorage.setItem('user', JSON.stringify(userResponse.data.data));
            this.$emit('loginSuccess', userResponse.data.data.name);
          }
          this.$router.push('/');
        } else {
          this.errorMessage = "Ошибка в данных пользователя";
        }
      } catch (error) {
        this.errorMessage = "Ошибка в данных пользователя";
        console.error("Error during authentication:", error);
      }
    },
    redirectToRegistration() {
      this.$router.push('/registration');
    }
  }
};
</script>
