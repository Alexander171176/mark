<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: String,
})

const emits = defineEmits(['update:sortParam'])

const updateSort = (event) => {
    emits('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit my-2">
        <label for="sortParam"
               class="hidden lg:block sm:mr-2 tracking-wider
                      text-sm font-semibold text-slate-600 dark:text-slate-100">
            {{ t('sort') }}
        </label>
        <select
            id="sortParam"
            :value="sortParam"
            @change="updateSort"
            class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>

            <option value="sortAsc">{{ t('sortNumber') }} ↑</option>
            <option value="sortDesc">{{ t('sortNumber') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────</option>

            <option value="questionTextAsc">{{ t('question') }} ↑</option>
            <option value="questionTextDesc">{{ t('question') }} ↓</option>

            <option value="quizTitleAsc">{{ t('quiz') }} ↑</option>
            <option value="quizTitleDesc">{{ t('quiz') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="pointsAsc">{{ t('points') }} ↑</option>
            <option value="pointsDesc">{{ t('points') }} ↓</option>

            <option value="answersCountAsc">{{ t('answers') }} ↑</option>
            <option value="answersCountDesc">{{ t('answers') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="attemptItemsCountDesc">{{ t('attempts') }} 9→0</option>
            <option value="attemptItemsCountAsc">{{ t('attempts') }} 0→9</option>
            <option disabled>─────────────</option>

            <option value="singleChoice">{{ t('questionTypeSingleChoice') }}</option>
            <option value="multipleChoice">{{ t('questionTypeMultipleChoice') }}</option>
            <option value="trueFalse">{{ t('questionTypeTrueFalse') }}</option>
            <option value="openText">{{ t('questionTypeOpenText') }}</option>
            <option disabled>─────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>─────────────</option>
        </select>
    </div>
</template>
