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
            class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option disabled>──────────────</option>

            <option value="attemptIdDesc">{{ t('attempt') }} ID ↓</option>
            <option value="attemptIdAsc">{{ t('attempt') }} ID ↑</option>

            <option value="questionIdDesc">{{ t('question') }} ID ↓</option>
            <option value="questionIdAsc">{{ t('question') }} ID ↑</option>
            <option disabled>──────────────</option>

            <option value="scoreAsc">{{ t('score') }} ↑</option>
            <option value="scoreDesc">{{ t('score') }} ↓</option>

            <option value="maxScoreAsc">{{ t('maxScore') }} ↑</option>
            <option value="maxScoreDesc">{{ t('maxScore') }} ↓</option>
            <option disabled>──────────────</option>

            <option value="correctFirst">{{ t('isCorrect') }}</option>
            <option value="wrongFirst">{{ t('no') }}</option>
            <option disabled>──────────────</option>

            <option value="questionTextAsc">{{ t('question') }} ↑</option>
            <option value="questionTextDesc">{{ t('question') }} ↓</option>
            <option disabled>──────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>
            <option disabled>──────────────</option>
        </select>
    </div>
</template>
