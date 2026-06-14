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
        <label
            for="sortParam"
            class="hidden lg:block sm:mr-2 tracking-wider
             text-sm font-semibold text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="sortParam"
            :value="sortParam"
            @change="updateSort"
            class="w-56 px-3 py-0.5 form-select
                   bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <!-- ID -->
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>

            <option disabled>──────────</option>

            <!-- Попытка -->
            <option value="attemptAsc">
                {{ t('attemptNumber') }} ↑
            </option>

            <option value="attemptDesc">
                {{ t('attemptNumber') }} ↓
            </option>

            <option disabled>──────────</option>

            <!-- Баллы -->
            <option value="scoreAsc">
                {{ t('score') }} ↑
            </option>

            <option value="scoreDesc">
                {{ t('score') }} ↓
            </option>

            <option value="maxScoreAsc">
                {{ t('maxScore') }} ↑
            </option>

            <option value="maxScoreDesc">
                {{ t('maxScore') }} ↓
            </option>

            <option value="percentAsc">
                {{ t('percent') }} ↑
            </option>

            <option value="percentDesc">
                {{ t('percent') }} ↓
            </option>

            <option disabled>──────────</option>

            <!-- Время -->
            <option value="durationAsc">
                {{ t('duration') }} ↑
            </option>

            <option value="durationDesc">
                {{ t('duration') }} ↓
            </option>

            <option value="startedAtAsc">
                {{ t('shortStarted') }} ↑
            </option>

            <option value="startedAtDesc">
                {{ t('shortStarted') }} ↓
            </option>

            <option value="finishedAtAsc">
                {{ t('shortExpires') }} ↑
            </option>

            <option value="finishedAtDesc">
                {{ t('shortExpires') }} ↓
            </option>

            <option disabled>──────────</option>

            <!-- Статус -->
            <option value="statusAsc">
                {{ t('status') }} ↑
            </option>

            <option value="statusDesc">
                {{ t('status') }} ↓
            </option>

            <option value="inProgress">
                {{ t('setStatusInProgress') }}
            </option>

            <option value="completed">
                {{ t('setStatusCompleted') }}
            </option>

            <option value="graded">
                {{ t('setStatusGraded') }}
            </option>

            <option disabled>──────────</option>

            <!-- Связи -->
            <option value="itemsAsc">
                {{ t('answers') }} ↑
            </option>

            <option value="itemsDesc">
                {{ t('answers') }} ↓
            </option>

            <option value="userNameAsc">
                {{ t('user') }} ↑
            </option>

            <option value="userNameDesc">
                {{ t('user') }} ↓
            </option>

            <option value="quizTitleAsc">
                {{ t('quiz') }} ↑
            </option>

            <option value="quizTitleDesc">
                {{ t('quiz') }} ↓
            </option>
        </select>
    </div>
</template>
