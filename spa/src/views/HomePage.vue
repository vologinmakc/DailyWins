<template>
  <v-container class="home">
    <v-row>
      <v-col cols="4" class="left-column">
      </v-col>
      <v-col cols="12" class="center-column" style="max-width: 800px; margin: 0 auto;">
        <div class="date">
          <v-icon v-if="areAllTasksCompleted" large color="green" class="success-icon">mdi-check-circle-outline</v-icon>
          <span class="font-weight-light" style="font-size: 1.5em;">Сегодня  </span>
          <v-menu v-model="menu" :close-on-content-click="false" :nudge-right="40">
            <template v-slot:activator="{ on, attrs }">
              <span v-bind="attrs" v-on="on" style="font-size: 2.6em; cursor: pointer;">{{ currentDate }}</span>
            </template>
            <v-date-picker v-model="selectedDate" @input="menu = false"></v-date-picker>
          </v-menu>

          <div class="days-of-week">
            <template v-for="(day, index) in weekDays">
              <span :key="'day-' + day" :class="{ 'current-day': day === currentDayOfWeek }">{{ day }}</span>
              <span v-if="index !== weekDays.length - 1" :key="'separator-' + index"> | </span>
            </template>
          </div>
        </div>
        <v-col>
          <hr>
        </v-col>
        <div v-if="tasks.length === 0" style="font-size: 1.6em;">На сегодня у вас нет задач</div>

        <!--   Tasks      -->
        <v-expansion-panels multiple v-if="tasks.length > 0" class="task-rounded box-shadow">
        <v-expansion-panel v-for="task in tasks" :key="task.name">
            <v-expansion-panel-header :class="{'task-completed': isAllSubTasksCompleted(task)}" class="task-header">

              <div>
                {{ task.name }}
                <v-progress-linear
                    v-if="task.subTasks && task.subTasks.length"
                    :value="calculateProgress(task.subTasks)"
                    color="green"
                    height="20"
                    class="v-progress-linear__bar mt-2"
                >
                  {{ completedSubTasks(task.subTasks) }} / {{ task.subTasks.length }}
                </v-progress-linear>
              </div>
            </v-expansion-panel-header>
            <v-expansion-panel-content class="custom-expansion-panel-content">
              <ul class="subtask-list text-left">
                <li v-for="subTask in task.subTasks" :key="subTask.name" class="subtask-item"
                    :class="{ 'completed': subTask.status === TASK_STATUSES.TASK_COMPLETED }">
                  <v-icon :color="getSubTaskIconColor(subTask.status)" small class="mr-2">
                    {{ getSubTaskIcon(subTask.status) }}
                  </v-icon>
                  <div class="subtask-details">
                    <div class="subtask-name">{{ subTask.name }}</div>
                    <div class="subtask-description">{{ subTask.description }}</div>
                  </div>

                  <!--   Иконки       -->
                  <v-icon @click="toggleSubTaskStatus(task, subTask)" class="custom-icon-size">
                    {{
                      subTask.status === TASK_STATUSES.TASK_COMPLETED ? 'mdi-checkbox-marked' : 'mdi-checkbox-blank-outline'
                    }}
                  </v-icon>
                  <v-icon @click="openEditSubTaskModal(task, subTask)" class="ml-2 edit-icon custom-icon-size">mdi-pencil</v-icon>
                  <v-icon @click="deleteSubTask(task, subTask)" class="ml-2 delete-icon custom-icon-size">mdi-trash-can-outline
                  </v-icon>
                  <!--   Иконки       -->
                </li>
              </ul>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
        <div class="text-right mt-4">
          <v-btn @click="showModal = true" color="green" dark small>Добавить задачу</v-btn>
        </div>
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
          <div v-if="showSubTasks">
            <div v-for="(subTask, index) in subTasks" :key="index">
              <v-text-field v-model="subTask.name" label="Имя подзадачи" outlined></v-text-field>
              <v-text-field v-model="subTask.description" label="Описание подзадачи" outlined></v-text-field>
            </div>
          </div>
          <v-btn @click="toggleSubTasks" color="green" dark small>
            {{ showSubTasks ? 'Скрыть подзадачи' : 'Добавить подзадачу' }}
          </v-btn>
        </v-card-text>
        <v-card-actions>
          <v-btn color="black darken-1" text @click="addTask">Добавить</v-btn>
          <v-btn color="black darken-1" text @click="showModal = false">Отмена</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-if="editingSubTask" v-model="showEditModal" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Редактировать подзадачу</span>
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="editingSubTask.name" label="Имя подзадачи" outlined></v-text-field>
          <v-text-field v-model="editingSubTask.description" label="Описание подзадачи" outlined></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn color="green darken-1" text @click="updateSubTask">Обновить</v-btn>
          <v-btn color="red darken-1" text @click="showEditModal = false">Отмена</v-btn>
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
      menu: false,
      selectedDate: new Date().toISOString().substr(0, 10),
      tasks: JSON.parse(localStorage.getItem('userTasks') || '[]'),
      newTaskName: '',
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
      weekDays: ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'],
      showEditModal: false,
      editingSubTask: {name: '', description: ''},
      editingTask: null
    };
  },
  computed: {
    areAllTasksCompleted() {
      return this.tasks.every(task => task.subTasks.every(subTask => subTask.status === this.TASK_STATUSES.TASK_COMPLETED));
    },
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
      const dayNameEnglish = new Date().toLocaleString('en-US', {weekday: 'long'});
      return this.daysMapping[dayNameEnglish];
    }

  },
  methods: {
    calculateProgress(subTasks) {
      const completedTasks = subTasks.filter(task => task.status === this.TASK_STATUSES.TASK_COMPLETED).length;
      return (completedTasks / subTasks.length) * 100;  // Процент выполнения
    },
    completedSubTasks(subTasks) {
      return subTasks.filter(task => task.status === this.TASK_STATUSES.TASK_COMPLETED).length;
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
      return task.subTasks.every(subTask => subTask.status === this.TASK_STATUSES.TASK_COMPLETED);
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
    toggleSubTaskStatus(mainTask, subTask) {
      if (subTask.status === this.TASK_STATUSES.TASK_COMPLETED) {
        subTask.status = this.TASK_STATUSES.TASK_NOT_STARTED;
      } else {
        subTask.status = this.TASK_STATUSES.TASK_COMPLETED;
      }

      if (this.isAllSubTasksCompleted(mainTask)) {
        mainTask.status = this.TASK_STATUSES.TASK_COMPLETED;
      } else {
        mainTask.status = this.TASK_STATUSES.TASK_NOT_STARTED;
      }
    },
    openEditSubTaskModal(task, subTask) {
      this.editingTask = task;
      this.editingSubTask = {...subTask}; // Делаем копию объекта
      this.showEditModal = true;
    },
    async updateSubTask() {
      try {
        const response = await this.$axios.put(`/v1/sub-task/${this.editingSubTask.id}`, this.editingSubTask);
        if (response.data.success) {
          const index = this.editingTask.subTasks.findIndex(subTask => subTask.id === this.editingSubTask.id);
          this.editingTask.subTasks.splice(index, 1, this.editingSubTask);
          this.showEditModal = false;
        } else {
          console.error('Error updating the subtask:', response.data.message);
        }
      } catch (error) {
        console.error('Failed to update the subtask:', error);
      }
    },
    deleteSubTask(mainTask, subTaskToDelete) {
      const index = mainTask.subTasks.indexOf(subTaskToDelete);
      if (index > -1) {
        mainTask.subTasks.splice(index, 1);
      }
      // Здесь вы можете также обновить данные в localStorage или на сервере
    },
    logout() {
      localStorage.removeItem('authToken');
      this.$router.push('/login');
    },
    toggleSubTasks() {
      this.showSubTasks = !this.showSubTasks;
      if (!this.showSubTasks) {
        this.subTasks = [{name: '', description: ''}];
      }
    },
    addSubTask() {
      this.subTasks.push({name: '', description: ''});
    },
    async addTask() {
      if (this.newTaskName.trim() !== '') {
        const taskData = {
          name: this.newTaskName,
          subTasks: this.subTasks.filter(st => st.name.trim() !== '')
        };

        try {
          const response = await this.$axios.post('/v1/tasks', taskData);
          if (response.data.success) {
            // You can add more actions on successful post
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
<style scoped>
.home {
  padding: 20px;
}

.date {
  margin-bottom: 20px;
}

.subtask-list {
  list-style-type: none;
  padding: 0;
}

.subtask-item {
  margin-bottom: 10px;
}

.subtask-name {
  font-weight: bold;
}

.subtask-description {
  color: gray;
  font-size: 0.9em;
}

.task-header {
  font-size: 16px;
  font-weight: bold;
  text-align: left;
  background-color: #f1f1f1;
}

.custom-expansion-panel-content {
  padding: 16px;
}

.subtask-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.subtask-name, .subtask-description {
  flex: 1;
}

.delete-icon {
  color: red;
}

.subtask-item.completed .subtask-name,
.subtask-item.completed .subtask-description {
  color: green;
}

.v-progress-linear__bar {
  border-radius: 10px;
}

.subtask-details {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.task-completed {
  background-color: #4caf50 !important;
  color: white;
}

.task-rounded {
  border-radius: 20px;
  overflow: hidden;
}

.success-icon {
  font-size: 4em;
  vertical-align: middle;
  margin-top: -20px;
}

.current-day {
  font-weight: bold;
  color: #5492e3;
  margin: 0 2px;
}

.days-of-week {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: space-between;
}

.days-of-week > span {
  margin: 0 5px;
}
.v-icon.custom-icon-size  {
  font-size: 24px;
  color: #5492e3;
}
.box-shadow {
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
