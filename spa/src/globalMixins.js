import { TASK_STATUSES } from './constants';

export const globalMixins = {
    data: function() {
        return {
            TASK_STATUSES
        };
    },
    computed: {
        currentUser() {
            return JSON.parse(localStorage.getItem('user'));
        }
    }
};

