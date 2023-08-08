<template>
  <v-container class="home">
    <v-row>
      <v-col cols="4" class="left-column">
      </v-col>
      <v-col cols="12" class="center-column" style="max-width: 800px; margin: 0 auto;">
        <div class="date" :style="{ color: dateColor }">
          <span class="font-weight-light" style="font-size: 0.8em;">Сегодня</span> - <span
            style="font-size: 1.6em;">{{ currentDate }}</span><br>{{ currentDayOfWeek }}
        </div>
        <v-col>
          <hr>
        </v-col>
        <div v-if="tasks.length === 0" style="font-size: 1.6em;">На сегодня у вас нет задач</div>
        <v-btn @click="showModal = true" color="green" dark small>Добавить задачу</v-btn>
      </v-col>
      <v-col cols="4" class="right-column">

      </v-col>
    </v-row>
    <!-- Modal Start -->
    <v-dialog v-model="showModal" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Добавить задачу</span>
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="newTaskName" label="Имя задачи" outlined></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn color="blue darken-1" text @click="addTask">Добавить</v-btn>
          <v-btn color="blue darken-1" text @click="showModal = false">Отмена</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Modal End -->
  </v-container>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      tasks: [],
      newTaskName: '',
      showModal: false,
      daysMapping: {
        'Monday': 'Понедельник',
        'Tuesday': 'Вторник',
        'Wednesday': 'Среда',
        'Thursday': 'Четверг',
        'Friday': 'Пятница',
        'Saturday': 'Суббота',
        'Sunday': 'Воскресенье'
      }
    };
  },
  computed: {
    isUserAuthenticated() {
      return !!localStorage.getItem('authToken');
    },
    userName() {
      return localStorage.getItem('user') || 'Войти';
    },
    currentDate() {
      return new Date().toLocaleDateString();
    },
    dateColor() {
      if (this.tasks.length === 0) {
        return 'gray';
      }
      const uncompletedTasks = this.tasks.filter(task => task.status === 1);
      if (uncompletedTasks.length > 0) {
        return 'red';
      }
      return 'green';
    },
    currentDayOfWeek() {
      const dayNameEnglish = new Date().toLocaleString('en-US', { weekday: 'long' });
      return this.daysMapping[dayNameEnglish];
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('authToken');
      this.$router.push('/login');
    },
    async addTask() {
      if (this.newTaskName.trim() !== '') {
        try {
          const response = await this.$axios.post('http://localhost:8000/api/tasks', { name: this.newTaskName });
          if (response.data.success) {
            this.tasks.push(response.data.task);
            this.showModal = false;
          } else {
            console.error('Error adding the task:', response.data.message);
          }
        } catch (error) {
          console.error('Failed to add the task:', error);
        }
      }
    }
  }
}
</script>