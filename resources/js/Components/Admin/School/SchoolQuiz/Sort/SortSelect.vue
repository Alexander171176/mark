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
            <option disabled>──────────────────────────</option>

            <option value="titleAsc">{{ t('title') }} ↑</option>
            <option value="titleDesc">{{ t('title') }} ↓</option>
            <option disabled>──────────────────────────</option>

            <option value="slugAsc">Slug A→Z</option>
            <option value="slugDesc">Slug Z→A</option>
            <option disabled>──────────────────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option disabled>──────────────────────────</option>

            <option value="passScoreAsc">{{ t('passScore') }} ↑</option>
            <option value="passScoreDesc">{{ t('passScore') }} ↓</option>

            <option value="attemptsLimitAsc">{{ t('attemptsLimit') }} ↑</option>
            <option value="attemptsLimitDesc">{{ t('attemptsLimit') }} ↓</option>

            <option value="timeLimitAsc">{{ t('limitMinutes') }} ↑</option>
            <option value="timeLimitDesc">{{ t('limitMinutes') }} ↓</option>
            <option disabled>──────────────────────────</option>

            <option value="questionsDesc">{{ t('quizQuestions') }} 9→0</option>
            <option value="questionsAsc">{{ t('quizQuestions') }} 0→9</option>

            <option value="attemptsDesc">{{ t('quizAttempts') }} 9→0</option>
            <option value="attemptsAsc">{{ t('quizAttempts') }} 0→9</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>
            <option disabled>──────────────────────────</option>

            <option value="graded">{{ t('quizTypeGraded') }}</option>
            <option value="practice">{{ t('quizTypePractice') }}</option>
            <option disabled>──────────────────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>──────────────────────────</option>

            <option value="left">{{ t('inLeft') }}</option>
            <option value="noLeft">{{ t('notLeft') }}</option>
            <option disabled>──────────────────────────</option>

            <option value="main">{{ t('inMain') }}</option>
            <option value="noMain">{{ t('notMain') }}</option>
            <option disabled>──────────────────────────</option>

            <option value="right">{{ t('inRight') }}</option>
            <option value="noRight">{{ t('notRight') }}</option>
            <option disabled>──────────────────────────</option>

            <option value="typeAsc">{{ t('type') }} A→Z</option>
            <option value="typeDesc">{{ t('type') }} Z→A</option>
            <option disabled>──────────────────────────</option>

            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>──────────────────────────</option>

        </select>
    </div>
</template>
