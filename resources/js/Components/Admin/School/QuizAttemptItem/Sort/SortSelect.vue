<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    sortParam: {
        type: String,
        default: 'idDesc',
    },
})

const emits = defineEmits(['update:sortParam'])

const updateSort = (e) => {
    emits('update:sortParam', e.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit sm:mr-4 mt-2 mb-2">
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
            class="w-44 px-3 py-0.5 form-select bg-white dark:bg-gray-200
             text-gray-600 dark:text-gray-900
             border border-slate-400 dark:border-slate-600
             rounded-sm shadow-sm"
        >
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option disabled>──────────────</option>

            <option value="attemptIdDesc">{{ t('sortAttemptIdDesc') }}</option>
            <option value="attemptIdAsc">{{ t('sortAttemptIdAsc') }}</option>
            <option disabled>──────────────</option>

            <option value="questionIdDesc">{{ t('sortQuestionIdDesc') }}</option>
            <option value="questionIdAsc">{{ t('sortQuestionIdAsc') }}</option>
            <option disabled>──────────────</option>

            <option value="correctFirst">{{ t('sortScoreAsc') }}</option>
            <option value="wrongFirst">{{ t('sortScoreDesc') }}</option>
            <option disabled>──────────────</option>

            <option value="createdAtDesc">{{ t('sortOldestFirst') }}</option>
            <option value="createdAtAsc">{{ t('sortNewestFirst') }}</option>
            <option disabled>──────────────</option>
        </select>
    </div>
</template>
