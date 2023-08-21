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
          ></v-select>
          <v-combobox
              v-if="selectedTaskType === 2"
              v-model="selectedDays"
              :items="daysOfWeek"
              item-text="label"
              item-value="value"
              label="Выберите дни недели"
              outlined
              multiple
              chips
              clearable
              deletable-chips
          ></v-combobox>
          <!--  Поле для указания завершения повторяющейся задачи -->
          <v-menu
              v-if="selectedTaskType === 2"
              ref="menuEndDate"
              v-model="menuEndDate"
              :close-on-content-click="false"
              :nudge-right="40"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                  v-model="endRepeatDate"
                  label="Дата окончания повторения"
                  type="date"
                  style="max-width: 300px"
                  outlined
                  v-bind="attrs"
                  v-on="on"
              ></v-text-field>
              <v-btn v-if="selectedTaskType === 2" small icon @click="clearEndDate">
                <small style="color: crimson">Сбросить дату</small>
              </v-btn>
            </template>
            <v-date-picker style="min-width: 300px" v-model="endRepeatDate" dark @input="menuEndDate = false" width="300px"></v-date-picker>
          </v-menu>



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
      selectedTaskType: '', // пока так
      showSubTasks: false,
      subTasks: [{name: '', description: ''}],
      daysOfWeek: [
        {label: 'Понедельник', value: 1},
        {label: 'Вторник', value: 2},
        {label: 'Среда', value: 3},
        {label: 'Четверг', value: 4},
        {label: 'Пятница', value: 5},
        {label: 'Суббота', value: 6},
        {label: 'Воскресенье', value: 7}
      ],
      selectedDays: [],
      showRepeatDaysModal: false,
      endRepeatDate: null,
      menuEndDate: false,
    };
  },
  computed: {
    selectedDaysValues() {
      return this.selectedDays.map(day => day.value);
    }
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
          type: this.selectedTaskType,
          subtasks: this.subTasks.filter(st => st.name.trim() !== ''),
          start_date: this.selectedDate,
          recurrence: this.selectedDaysValues, // Добавляем выбранные дни недели
          end_date: this.endRepeatDate
        };

        try {
          const response = await this.$axios.post('/api/tasks', taskData);
          if (response.data.result_code === 'COMPLETE') {
            this.showModal = false;
            this.resetFields();
            this.loadTasks();
          } else {
            console.error('Error adding the task');
          }
        } catch (error) {
          console.error('Failed to add the task');
        }
      }
    },
    confirmRepeatDays() {
      this.showRepeatDaysModal = false;
      this.addTask();
    },
    addSubTask() {
      this.subTasks.push({name: '', description: ''});
    },
    clearEndDate() {
      this.endRepeatDate = null;
    },
    resetFields() {
      this.newTaskName = '';
      this.selectedTaskType = '';
      this.showSubTasks = false;
      this.subTasks = [{name: '', description: ''}];
      this.selectedDays = [];
      this.endRepeatDate = null;
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
