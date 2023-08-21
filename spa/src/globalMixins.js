import {TASK_STATUSES} from './constants';
import {TASK_TYPE} from './constants';

export const globalMixins = {
    data: function () {
        return {
            TASK_STATUSES,
            TASK_TYPE
        };
    }
};

