<template>
  <div>
    <v-expansion-panels flat popout multiple v-if="tasks.length > 0">
      <v-expansion-panel v-for="task in tasks" :key="task.name">
        <TaskItem :selectedDate="selectedDate" :loadTasks="loadTasks" :task="task" :TASK_STATUSES="TASK_STATUSES"></TaskItem>
      </v-expansion-panel>
    </v-expansion-panels>
  </div>
</template>

<script>
import TaskItem from './TaskItem.vue';

export default {
  components: {
    TaskItem
  },
  props: {
    tasks: {
      type: Array,
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
  methods: {
    openEditTaskModal(task) {
      this.editingTask = {...task}; // Клонировать задачу, чтобы предотвратить неожиданное поведение
      this.showEditTaskModal = true;
    },
    isAllSubTasksCompleted(task) {
      return task.sub_tasks.every(sub_task => sub_task.status === this.TASK_STATUSES.TASK_COMPLETED);
    },
    calculateProgress(subTasks) {
      const completedTasks = subTasks.filter(task => task.status === this.TASK_STATUSES.TASK_COMPLETED).length;
      return (completedTasks / subTasks.length) * 100;  // Процент выполнения
    },
    completedSubTasks(subTasks) {
      return subTasks.filter(task => task.status === this.TASK_STATUSES.TASK_COMPLETED).length;
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
    confirmDeleteTask(task) {
      if (confirm("Вы уверены, что хотите удалить эту задачу?")) {
        this.deleteTask(task);
      }
    }
  }
}
</script>

<style scoped>

.subtask-item.completed .subtask-name,
.subtask-item.completed .subtask-description {
  color: green;
}

.task-rounded {
  border-radius: 0;
  overflow: hidden;
}

.box-shadow {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.task-header .v-icon {
  transform: none !important;
}

</style>
