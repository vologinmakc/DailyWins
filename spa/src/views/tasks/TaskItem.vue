<template>
  <div>
    <v-expansion-panel-header :class="taskCompletedClass"
                              class="task-header d-flex align-center">
      <div class="task-text-wrapper flex-grow-1">
        {{ task.name }}
        <v-progress-linear
            v-if="task.sub_tasks && task.sub_tasks.length"
            :value="calculateProgress(task.sub_tasks)"
            color="green"
            height="20"
            class="v-progress-linear__bar mt-2"
        >
          {{ completedSubTasks(task.sub_tasks) }} / {{ task.sub_tasks.length }}
        </v-progress-linear>
      </div>
      <template v-slot:actions>
        <v-menu offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-icon
                v-bind="attrs"
                v-on="on"
                class="custom-icon-size"
            >
              mdi-menu-down
            </v-icon>
          </template>
          <v-list>
            <v-list-item @click="openEditTaskModal(task)">
              <v-list-item-icon>
                <v-icon>mdi-pencil</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Редактировать</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item @click.stop="confirmDeleteTask(task)">
              <v-list-item-icon>
                <v-icon>mdi-trash-can-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Удалить</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>

      </template>
    </v-expansion-panel-header>

    <v-expansion-panel-content class="custom-expansion-panel-content">
      <ul class="subtask-list text-left">
        <li v-for="subTask in task.sub_tasks" :key="subTask.name" class="subtask-item"
            :class="{ 'completed': subTask.status === TASK_STATUSES.TASK_COMPLETED }">
          <v-icon :color="getSubTaskIconColor(subTask.status)" small class="mr-2">
            {{ getSubTaskIcon(subTask.status) }}
          </v-icon>
          <div class="subtask-details">
            <div class="subtask-name">{{ subTask.name }}</div>
            <div class="subtask-description">{{ subTask.description }}</div>
            <v-divider></v-divider>
          </div>
          <v-menu offset-y>
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                  v-bind="attrs"
                  v-on="on"
                  class="custom-icon-size"
              >
                mdi-menu
              </v-icon>
            </template>
            <v-list>
              <v-list-item @click="toggleSubTaskStatus(task, subTask)">
                <v-list-item-icon>
                  <v-icon>
                    {{
                      subTask.status === TASK_STATUSES.TASK_COMPLETED ? 'mdi-checkbox-marked' : 'mdi-checkbox-blank-outline'
                    }}
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Выполнено</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item @click="openEditSubTaskModal(task, subTask)">
                <v-list-item-icon>
                  <v-icon>mdi-pencil</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Редактировать</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item @click="deleteSubTask(task, subTask)">
                <v-list-item-icon>
                  <v-icon>mdi-trash-can-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Удалить</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-menu>

        </li>
      </ul>
      <!--  Окно добавления подзадачи    -->
      <v-btn @click="showAddSubTaskModal = true" color="success" fab depressed small class="btn-add-sub-task mt-2 mb-2">
        <v-icon>mdi-note-plus</v-icon>
      </v-btn>

    </v-expansion-panel-content>
    <!-- Modal Start -->

    <!-- Создание подзадачи   -->
    <v-dialog v-model="showAddSubTaskModal" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Добавить подзадачу</span>
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="newSubTask.name" label="Имя подзадачи" outlined></v-text-field>
          <v-text-field v-model="newSubTask.description" label="Описание подзадачи" outlined></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn color="green darken-1" text @click="addNewSubTask">Добавить</v-btn>
          <v-btn color="red darken-1" text @click="showAddSubTaskModal = false">Отмена</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Редактирование задачи   -->
    <v-dialog v-model="showEditTaskModal" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Редактировать задачу</span>
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="editingTask.name" label="Имя задачи" outlined></v-text-field>
          <v-menu ref="menu" v-model="menuStartDate" :close-on-content-click="false" :nudge-right="40">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                  v-model="editingTask.start_date"
                  label="Дата начала"
                  outlined
                  type="date"
                  style="max-width: 300px"
                  v-bind="attrs"
                  v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker v-model="editingTask.start_date" dark @input="menuStartDate = false" width="300px"></v-date-picker>
          </v-menu>
          <v-menu ref="menu" v-model="menuEndDate" :close-on-content-click="false" :nudge-right="40">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                  v-model="editingTask.end_date"
                  label="Укажите дату окончания задачи"
                  outlined
                  type="date"
                  style="max-width: 300px"
                  v-bind="attrs"
                  v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker v-model="editingTask.end_date" dark @input="menuEndDate = false" width="300px"></v-date-picker>
          </v-menu>

        </v-card-text>
        <v-card-actions>
          <v-btn color="green darken-1" text @click="updateTask">Обновить</v-btn>
          <v-btn color="red darken-1" text @click="showEditTaskModal = false">Отмена</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Редактирование задачи   -->

    <!-- Редактировать под задачи   -->
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
    <!-- Редактировать под задачи   -->

    <!-- Подтверждение действия удаления задачи   -->
    <v-dialog v-model="showConfirmDeleteModal" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Подтвердите действие</span>
        </v-card-title>
        <v-card-text>
          Вы уверены, что хотите удалить эту задачу?
        </v-card-text>
        <v-card-actions>
          <v-btn color="green darken-1" text @click="confirmDelete">Подтвердить</v-btn>
          <v-btn color="red darken-1" text @click="showConfirmDeleteModal = false">Отмена</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Modal End -->
  </div>
</template>

<script>
export default {
  props: {
    task: {
      type: Object,
      required: true
    },
    loadTasks: {
      type: Function,
      required: true
    },
    selectedDate: {
      type: String,
      required: true,
      default: new Date().toISOString().substr(0, 10)
    }
  },
  data() {
    return {
      editingSubTask: {name: '', description: ''},
      showEditModal: false,
      showEditTaskModal: false,
      editingTask: {
        id: null,
        name: '',
        start_date: '',
        end_date: ''
      },
      menuStartDate: false,
      menuEndDate: false,
      showAddSubTaskModal: false,
      newSubTask: {name: '', description: ''},
      showConfirmDeleteModal: false,
      taskToDelete: null,
    }
  },
  computed: {
    taskCompletedClass() {
      return this.isAllSubTasksCompleted(this.task) ? 'task-completed' : '';
    }
  },
  methods: {
    isAllSubTasksCompleted() {
      return this.task.sub_tasks.every(sub_task => sub_task.status === this.TASK_STATUSES.TASK_COMPLETED);
    },
    async deleteTask(taskToDelete) {
      try {
        const response = await this.$axios.delete(`/api/tasks/${taskToDelete.id}`);

        if (response.data.result_code === 'COMPLETE') {
          console.log('Задача успешно удалена');
          this.loadTasks();
        } else {
          console.error('Ошибка при удалении задачи');
        }
      } catch (error) {
        console.error('Ошибка при удалении задачи');
      }
    },
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

      // Обновляем статус подзадачи на сервере
      this.updateSubTaskStatus(subTask);

      if (this.isAllSubTasksCompleted(mainTask)) {
        mainTask.status = this.TASK_STATUSES.TASK_COMPLETED;
      } else {
        mainTask.status = this.TASK_STATUSES.TASK_NOT_STARTED;
      }
    },

    // Обновляем подзадачи
    async updateSubTaskStatus(subTask) {
      try {
        const response = await this.$axios.put(`/api/subtasks/${subTask.id}/status`, {
          status: subTask.status,
          date: this.selectedDate
        });

        if (response.data.result_code === 'COMPLETE') {
          console.log('Статус подзадачи успешно обновлен');
        } else {
          console.error('Ошибка при обновлении статуса');
        }
      } catch (error) {
        console.error('Ошибка при обновлении статуса');
      }
    },
    openEditSubTaskModal(task, subTask) {
      this.editingTask = task;
      this.editingSubTask = {...subTask}; // Делаем копию объекта
      this.showEditModal = true;
    },
    async deleteSubTask(mainTask, subTaskToDelete) {
      try {
        this.isLoading = true;

        const response = await this.$axios.delete(`/api/subtasks/${subTaskToDelete.id}`);

        if (response.data.result_code === 'COMPLETE') {
          const index = mainTask.sub_tasks.indexOf(subTaskToDelete);
          if (index > -1) {
            mainTask.sub_tasks.splice(index, 1);
          }
        } else {
          console.error('Ошибка при удалении подзадачи');
        }
      } catch (error) {
        console.error('Ошибка при удалении подзадачи');
      } finally {
        this.isLoading = false;
      }
    },
    async updateTask() {
      try {
        this.isLoading = true;
        const response = await this.$axios.put(`/api/tasks/${this.editingTask.id}`, this.editingTask);
        if (response.data.result_code === 'COMPLETE') {
          this.showEditTaskModal = false;
          this.loadTasks();
        } else {
          console.error('Ошибка при редактировании задачи');
        }
      } catch (error) {
        console.error('Ошибка при редактировании задачи');
      } finally {
        this.isLoading = false;
      }
    },
    openEditTaskModal(task) {
      this.editingTask = {...task}; // Клонировать задачу, чтобы предотвратить неожиданное поведение
      this.showEditTaskModal = true;
    },
    async updateSubTask() {
      try {
        this.isLoading = true;
        const response = await this.$axios.put(`/api/subtasks/${this.editingSubTask.id}`, this.editingSubTask);
        if (response.data.result_code === 'COMPLETE') {
          this.showEditModal = false;
        } else {
          console.error('Error');
        }
      } catch (error) {
        console.error('Failed');
      } finally {
        this.loadTasks();
        this.isLoading = false;
      }
    },
    confirmDeleteTask(task) {
      this.taskToDelete = task;
      this.showConfirmDeleteModal = true;
    },
    confirmDelete() {
      this.deleteTask(this.taskToDelete);
      this.showConfirmDeleteModal = false;
    },
    async addNewSubTask() {
      if (this.newSubTask.name.trim() !== '') {
        const subTaskData = {
          name: this.newSubTask.name,
          description: this.newSubTask.description,
          task_id: this.task.id
        };

        try {
          const response = await this.$axios.post(`/api/subtasks/`, subTaskData);
          if (response.data.result_code === 'COMPLETE') {
            this.showAddSubTaskModal = false;
            this.loadTasks();
          } else {
            console.error('Error adding the subtask');
          }
        } catch (error) {
          console.error('Failed to add the subtask');
        }
      }
    }
  }
}
</script>

<style scoped>

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
  color: #3b3b3b;
  font-size: 0.9em;
}

.custom-expansion-panel-content /deep/ .v-expansion-panel-content__wrap {
  padding: 0 !important;
}

.v-expansion-panel-content {
  border-bottom: 1px solid #ccc;
  border-right: 1px solid #ccc;
  border-left: 1px solid #ccc;
}

.task-header {
  justify-content: space-between;
  font-size: 16px;
  font-weight: bold;
  text-align: left;
  color: #273354;
  border: 1px solid #ccc;
}

.custom-expansion-panel-content {
  padding: 16px;

  /*background: linear-gradient(
      rgba(255,255,255, 0) .9em, rgba(0, 0, 0, .15) 1em)
  0 0,
  linear-gradient(90deg,
      rgba(255,255,255, 0) .9em, rgba(0, 0, 0, .15) 1em)
  0 0;
  background-size: 1em 1em;
  background-color: white;*/
}

.subtask-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.subtask-name, .subtask-description {
  flex: 1;
}

.subtask-item.completed .subtask-name,
.subtask-item.completed .subtask-description {
  color: green;
}

.v-progress-linear__bar {
  border-radius: 0;
}

.subtask-details {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.btn-add-sub-task {
  float: right; /* Чтобы кнопка была справа */
}

.task-completed {
  background-color: #4caf50 !important;
  background-image: none !important;
  color: white;
}

.task-header .v-icon {
  transform: none !important;
}

.task-text-wrapper {
  font-family: Nunito, Poppins, Helvetica, Arial, sans-serif;
  font-size: 1.1em;
  font-weight: normal;
}

</style>
