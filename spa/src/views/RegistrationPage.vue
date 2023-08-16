<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12">
          <v-toolbar dark flat>
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
              <v-text-field
                  label="Подтвердите пароль"
                  v-model="passwordConfirmation"
                  type="password"
                  required
              ></v-text-field>
              <!-- Блок с капчей -->
              <div>
                <img :src="captchaSrc" @click="refreshCaptcha" style="cursor:pointer;">
                <v-text-field
                    label="Введите капчу"
                    v-model="captcha"
                    required
                ></v-text-field>
              </div>
              <input type="hidden" v-model="captchaToken">
              <!-- Конец блока с капчей -->
              <v-row>
                <v-col>
                  <v-btn type="submit" dark block>Регистрация</v-btn>
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
      password: '',
      passwordConfirmation: '',
      captchaSrc: '',
      captchaToken: '',
      captcha: ''
    };
  },
  mounted() {
    this.refreshCaptcha();
  },
  methods: {
    async refreshCaptcha() {
      try {
        const response = await this.$axios.get('/api/captcha/generate');
        this.captchaSrc = response.data.data.image;
        this.captchaToken = response.data.data.token;
      } catch (error) {
        console.error('Failed to refresh the captcha:', error);
      }
    },
    async registerUser() {
      if (this.password !== this.passwordConfirmation) {
        alert("Пароль и подтверждение пароля не совпадают!");
        return;
      }

      const userData = {
        name: this.name,
        email: this.email,
        password: this.password,
        captcha: this.captcha,
        captcha_token: this.captchaToken
      };

      try {
        const response = await this.$axios.post('/api/user/register', userData);

        if (response.data.result_code === 'COMPLETE') {
          console.log('User registered successfully');
          localStorage.setItem('authToken', response.data.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.data.user))
          this.$emit('loginSuccess', response.data.data.user.name);
          this.$router.push('/');
        } else {
          console.error('Error during registration:', response.data.data);
        }
      } catch (error) {
        console.error('Failed to register the user:', error);
      }
    },
    async register() {
      this.registerUser();
    }
  }
};
</script>
