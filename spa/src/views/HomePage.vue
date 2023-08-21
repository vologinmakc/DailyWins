<template>
  <v-container class="home">
    <v-row>
      <v-col cols="4" class="left-column">
      </v-col>
      <v-col cols="12" class="center-column" style="max-width: 800px; margin: 0 auto;">
        <div class="date">
          <v-icon v-if="areAllTasksCompleted" large color="green" class="success-icon">mdi-check-circle-outline
          </v-icon>
          <div class="date-labels">
            <div class="day-name">
              <v-icon color="green">mdi-clipboard-text-clock-outline
              </v-icon>
              {{ displayedDateLabel }}
            </div>
            <v-menu v-model="menu" :close-on-content-click="false" :nudge-right="40">
              <template v-slot:activator="{ on, attrs }">
              <span v-bind="attrs" v-on="on" style="font-size: 2.6em; cursor: pointer;">{{
                  formattedSelectedDate
                }}</span>
              </template>
              <v-date-picker width="300px" v-model="selectedDate" dark @input="onDateSelected"></v-date-picker>
            </v-menu>
          </div>

          <!--   Сегодняшний день  пока уберем -->
          <!--          <div class="current-day-label">
                      <div>
                        Сегодня | <span class="current-day-label__current-day">{{ currentDayOfWeek }}</span>
                      </div>
                    </div>-->
        </div>
        <v-col>
          <v-divider></v-divider>
        </v-col>
        <v-overlay :value="isLoading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <div v-if="tasks.length === 0" class="empty-tasks-container">
          <v-icon class="mb-2" size="50">mdi-note-alert</v-icon>
          <div style="font-size: 1.6em;">Тут пока ничего нет</div>
        </div>

        <!--   Tasks      -->
        <!--   Выводим ежедневные задачи      -->
        <div v-if="dailyTasks.length > 0">
          <div class="tasks-header__type-task">
            <v-icon color="orange">mdi-tag</v-icon>
            Ежедневные задачи
            <br>
            <small style="font-weight: normal">Задачи которые будут повторяться в указанные дни недели</small>
            <v-divider class="mb-5"></v-divider>
          </div>
          <TasksList :selectedDate="selectedDate" :loadTasks="loadTasksForSelectedDate" :tasks="dailyTasks"
                     :TASK_STATUSES="TASK_STATUSES"/>
        </div>

        <!--   Остальные типы задач     -->
        <div v-if="nonDailyTasks.length > 0" class="mt-10">
          <v-divider class="mb-5"></v-divider>
          <div class="tasks-header__type-task">
            <v-icon color="orange">mdi-repeat-off</v-icon>
            Задачи только на сегодня
            <br>
            <small style="font-weight: normal">Задачи которые назначены на сегодняшний день</small>
            <v-divider class="mb-5"></v-divider>
          </div>
          <TasksList :selectedDate="selectedDate" :loadTasks="loadTasksForSelectedDate" :tasks="nonDailyTasks"
                     :TASK_STATUSES="TASK_STATUSES"/>
        </div>
        <!--   Остальные типы задач     -->

        <!-- Добавление задачи   -->
        <div class="text-right mt-4">
          <AddTaskButton :selectedDate="selectedDate" :loadTasks="loadTasksForSelectedDate"/>
        </div>
      </v-col>
      <v-col cols="4" class="right-column">
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import TasksList from './tasks/TasksList.vue';
import AddTaskButton from './tasks/AddTaskButton.vue';
import {TASK_TYPE} from "@/constants";

export default {
  name: 'HomePage',
  components: {
    TasksList,
    AddTaskButton
  },
  data() {
    return {
      isLoading: false,
      menu: false,
      selectedDate: new Date().toISOString().substr(0, 10),
      startDate: new Date().toISOString().substr(0, 10),
      tasks: [],
      subTasks: [{name: '', description: ''}],
      showModal: false,
      showSubTasks: false,
      daysMapping: {
        'Monday': 'Понедельник',
        'Tuesday': 'Вторник',
        'Wednesday': 'Среда',
        'Thursday': 'Четверг',
        'Friday': 'Пятница',
        'Saturday': 'Суббота',
        'Sunday': 'Воскресенье'
      },
      weekDays: [
        {name: 'Понедельник', isCurrent: false, isNeighbor: false},
        {name: 'Вторник', isCurrent: false, isNeighbor: false},
        {name: 'Среда', isCurrent: false, isNeighbor: false},
        {name: 'Четверг', isCurrent: false, isNeighbor: false},
        {name: 'Пятница', isCurrent: false, isNeighbor: false},
        {name: 'Суббота', isCurrent: false, isNeighbor: false},
        {name: 'Воскресенье', isCurrent: false, isNeighbor: false},
      ],
      showEditModal: false,
      editingSubTask: {name: '', description: ''},
      taskTypes: [
        {text: 'На сегодня', value: 1},
        {text: 'Повторяющееся', value: 2}
      ],
      selectedTaskType: 1 // пока так
    };
  },
  mounted() {
    this.loadTasksForSelectedDate();
    this.weekDays[this.currentDayOfWeekIndex()].isCurrent = true;
    if (this.currentDayOfWeekIndex() > 0) {
      this.weekDays[this.currentDayOfWeekIndex() - 1].isNeighbor = true;
    }
    if (this.currentDayOfWeekIndex() < this.weekDays.length - 1) {
      this.weekDays[this.currentDayOfWeekIndex() + 1].isNeighbor = true;
    }
  },
  computed: {
    formattedSelectedDate() {
      const dateObj = new Date(this.selectedDate);
      return dateObj.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
      });
    },
    areAllTasksCompleted() {
      if (this.tasks.length === 0) {
        return false;
      }

      return this.tasks.every(task => task.sub_tasks.every(subTask => subTask.status === this.TASK_STATUSES.TASK_COMPLETED));
    },
    isUserAuthenticated() {
      return !!localStorage.getItem('authToken');
    },
    displayedDateLabel() {
      const today = new Date().toISOString().substr(0, 10);
      if (this.selectedDate === today) {
        return "Сегодня";
      }
      const dayNameEnglish = new Date(this.selectedDate).toLocaleString('en-US', {weekday: 'long'});
      return this.daysMapping[dayNameEnglish];
    },
    dailyTasks() {
      return this.tasks.filter(task => task.type === TASK_TYPE.TYPE_RECURRING);
    },
    nonDailyTasks() {
      return this.tasks.filter(task => task.type === TASK_TYPE.TYPE_ONE_OFF);
    }
  },
  methods: {
    // Загрузим задачи пользователя (пока все потом сделаем по дате)
    async loadTasksForSelectedDate() {
      try {
        this.isLoading = true;
        const response = await this.$axios.get('/api/tasks?search[start_date_or_day]=' + this.selectedDate + '&&expand=sub_tasks&&sort=id');
        this.tasks = response.data.data;
      } catch (error) {
        console.error('Ошибка при получении задач');
      } finally {
        this.isLoading = false;
      }
    },
    openEditTaskModal(task) {
      this.editingTask = {...task}; // Клонировать задачу, чтобы предотвратить неожиданное поведение
      this.showEditTaskModal = true;
    },
    getSubTaskIcon(status) {
      switch (status) {
        case this.TASK_STATUSES.TASK_COMPLETED:
          return 'mdi-check';
        case this.TASK_STATUSES.TASK_NOT_STARTED:
        case this.TASK_STATUSES.TASK_IN_PROGRESS:
          return 'mdi-clock-outline';
        default:
          return '';
      }
    },
    isAllSubTasksCompleted(task) {
      return task.sub_tasks.every(sub_task => sub_task.status === this.TASK_STATUSES.TASK_COMPLETED);
    },
    getSubTaskIconColor(status) {
      switch (status) {
        case this.TASK_STATUSES.TASK_COMPLETED:
          return 'green';
        case this.TASK_STATUSES.TASK_NOT_STARTED:
        case this.TASK_STATUSES.TASK_IN_PROGRESS:
          return 'gray';
        default:
          return '';
      }
    },
    logout() {
      localStorage.removeItem('authToken');
      this.$router.push('/login');
    },
    currentDayOfWeekIndex() {
      const dayNameEnglish = new Date().toLocaleString('en-US', {weekday: 'long'});
      const currentDay = this.daysMapping[dayNameEnglish];
      return this.weekDays.findIndex(day => day.name === currentDay);
    },
    onDateSelected() {
      this.menu = false;
      this.loadTasksForSelectedDate();
    }
  }
}
</script>
<style scoped>

.home {
  padding: 20px;
}

.date {
  margin-bottom: 1px;
}

.tasks-header__type-task {
  text-align: left;
  font-weight: bold;
  font-size: 1.2em;
  margin: 10px 10px;
}

.subtask-item.completed .subtask-name,
.subtask-item.completed .subtask-description {
  color: green;
}

.success-icon {
  font-size: 4em;
  vertical-align: middle;
  margin-top: 35px;
  margin-right: 10px;
}

.task-header .v-icon {
  transform: none !important;
}

.empty-tasks-container {
  font-family: 'Roboto', serif;
  text-align: center;
  color: #555;
}

.empty-tasks-container > div {
  font-weight: 300;
  letter-spacing: 0.5px;
  margin-top: 10px;
}

.date-labels {
  display: inline-flex;
  flex-direction: column;
  align-items: flex-start;
  vertical-align: middle;
}

.day-name {
  font-size: 20px;
  font-weight: 300;
  margin-bottom: 0;
  padding-left: 0;
}

</style>
