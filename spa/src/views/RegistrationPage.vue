
<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12">
          <v-toolbar color="primary" dark flat>
            <v-toolbar-title>Регистрация</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form @submit.prevent="register">
              <v-text-field
                label="Имя"
                v-model="name"
                required
              ></v-text-field>
              <v-text-field
                label="Email"
                v-model="email"
                type="email"
                required
              ></v-text-field>
              <v-text-field
                label="Пароль"
                v-model="password"
                type="password"
                required
              ></v-text-field>
              <v-row>
                <v-col>
                  <v-btn type="submit" block>Регистрация</v-btn>
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
      name: '',
      email: '',
      password: ''
    };
  },
  methods: {
    async register() {
      // Placeholder for registration logic
      console.log("Registration triggered");
      this.$emit("register");
    }
  }
};
</script>
