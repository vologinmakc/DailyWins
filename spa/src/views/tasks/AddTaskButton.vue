<template>
  <div>
    <div class="center-container">
      <v-btn @click="showModal = true" class="btn-add" fab depressed small color="success">
        <v-icon>mdi-pen-plus</v-icon>
      </v-btn>
    </div>
    <v-dialog v-model="showModal" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Добавить задачу</span>
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="newTaskName" label="Имя задачи" outlined></v-text-field>
          <v-select
              v-model="selectedTaskType"
              :items="taskTypes"
              label="Тип задачи"
              outlined
              disabled
          ></v-select>
          <div v-if="showSubTasks">
            <div v-for="(subTask, index) in subTasks" :key="index">
              <v-text-field v-model="subTask.name" label="Имя подзадачи" outlined></v-text-field>
              <v-text-field v-model="subTask.description" label="Описание подзадачи" outlined></v-text-field>
            </div>

            <v-btn @click="addSubTask" color="blue" dark small class="mt-2 mb-2">Добавить еще подзадачу</v-btn>
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
  </div>
</template>

<script>
export default {
  name: 'AddTaskButton',
  props: {
    loadTasks: {
      type: Function,
      required: true
    },
    selectedDate: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      showModal: false,
      newTaskName: '',
      taskTypes: [
        {text: 'На сегодня', value: 1},
        {text: 'Повторяющееся', value: 2}
      ],
      selectedTaskType: 1, // пока так
      showSubTasks: false,
      subTasks: [{name: '', description: ''}]
    };
  },
  methods: {
    toggleSubTasks() {
      this.showSubTasks = !this.showSubTasks;
      if (this.showSubTasks && this.subTasks.length === 0) {
        this.subTasks = [{name: '', description: ''}];
      }
    },
    async addTask() {
      if (this.newTaskName.trim() !== '') {
        const taskData = {
          name: this.newTaskName,
          type: this.selectedTaskType,  // Добавляем тип задачи
          subtasks: this.subTasks.filter(st => st.name.trim() !== ''),
          start_date: this.selectedDate, // Добавляем выбранную дату
        };

        try {
          const response = await this.$axios.post('/api/tasks', taskData);
          if (response.data.result_code === 'COMPLETE') {
            this.showModal = false;
            this.newTaskName = '';          // Очищаем имя новой задачи
            this.subTasks = [{name: '', description: ''}];  // Сбрасываем подзадачи
            this.loadTasks();
          } else {
            console.error('Error adding the task');
          }
        } catch (error) {
          console.error('Failed to add the task');
        }
      }
    },
    addSubTask() {
      this.subTasks.push({name: '', description: ''});
    }
  }
}
</script>

<style scoped>

.center-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.btn-add {
  font-size: 26px !important;
}
</style>
