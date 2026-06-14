<script setup>
import {defineProps, defineEmits} from 'vue';
import {useI18n} from 'vue-i18n';

const {t} = useI18n();

defineProps({
    sortParam: String
});

const emits = defineEmits(['update:sortParam']);

const updateSort = (event) => {
    emits('update:sortParam', event.target.value);
};
</script>

<template>
    <div class="flex justify-center items-center h-fit my-2">
        <label for="sortParam"
               class="hidden lg:block sm:mr-2 tracking-wider
                      text-sm font-semibold text-slate-600 dark:text-slate-100">
            {{ t('sort') }}
        </label>
        <select id="sortParam" :value="sortParam" @change="updateSort"
                class="w-44 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                       text-gray-600 dark:text-gray-900
                       border border-slate-400 dark:border-slate-600
                       rounded-sm shadow-sm">

            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option disabled>─────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>─────────────</option>

            <option value="textAsc">{{ t('answer') }} A→Z</option>
            <option value="textDesc">{{ t('answer') }} Z→A</option>

            <option value="quizTitleAsc">{{ t('quiz') }} A→Z</option>
            <option value="quizTitleDesc">{{ t('quiz') }} Z→A</option>

            <option value="questionTextAsc">{{ t('question') }} A→Z</option>
            <option value="questionTextDesc">{{ t('question') }} Z→A</option>
            <option disabled>─────────────</option>

            <option value="weightDesc">{{ t('weight') }} 9→0</option>
            <option value="weightAsc">{{ t('weight') }} 0→9</option>

            <option value="attemptItemsDesc">{{ t('attempts') }} 9→0</option>
            <option value="attemptItemsAsc">{{ t('attempts') }} 0→9</option>
            <option disabled>─────────────</option>

            <option value="correct">{{ t('correct') }}</option>
            <option value="incorrect">{{ t('incorrect') }}</option>
            <option value="correctDesc">{{ t('correct') }} ON→OFF</option>
            <option value="correctAsc">{{ t('correct') }} OFF→ON</option>
            <option disabled>─────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>─────────────</option>
        </select>
    </div>
</template>
