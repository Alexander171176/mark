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
            <option disabled>──────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} ↑</option>
            <option value="sortDesc">{{ t('sortNumber') }} ↓</option>
            <option disabled>──────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>──────────────</option>

            <option value="titleAsc">{{ t('title') }} ↑</option>
            <option value="titleDesc">{{ t('title') }} ↓</option>
            <option disabled>──────────────</option>

            <option value="slugAsc">Slug A→Z</option>
            <option value="slugDesc">Slug Z→A</option>
            <option disabled>──────────────</option>

            <option value="left">{{ t('inLeft') }}</option>
            <option value="noLeft">{{ t('notLeft') }}</option>
            <option disabled>──────────────</option>

            <option value="main">{{ t('inMain') }}</option>
            <option value="noMain">{{ t('notMain') }}</option>
            <option disabled>──────────────</option>

            <option value="right">{{ t('inRight') }}</option>
            <option value="noRight">{{ t('notRight') }}</option>
            <option disabled>──────────────</option>

            <!-- Статус / Доступность -->
            <option value="statusAsc">{{ t('status') }} A→Z</option>
            <option value="statusDesc">{{ t('status') }} Z→A</option>

            <option value="visibilityAsc">{{ t('visibility') }} A→Z</option>
            <option value="visibilityDesc">{{ t('visibility') }} Z→A</option>
            <option disabled>──────────────</option>

            <option value="gradingTypeAsc">{{ t('gradingType') }} A→Z</option>
            <option value="gradingTypeDesc">{{ t('gradingType') }} Z→A</option>

            <option value="attemptsLimitDesc">{{ t('attemptsLimit') }} 9→0</option>
            <option value="attemptsLimitAsc">{{ t('attemptsLimit') }} 0→9</option>
            <option disabled>──────────────</option>

            <option value="maxScoreDesc">{{ t('maxScore') }} 9→0</option>
            <option value="maxScoreAsc">{{ t('maxScore') }} 0→9</option>

            <option value="submissionsDesc">{{ t('submissions') }} 9→0</option>
            <option value="submissionsAsc">{{ t('submissions') }} 0→9</option>
            <option disabled>──────────────</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>
            <option disabled>──────────────</option>

            <option value="courseDesc">{{ t('course') }} ID 9→0</option>
            <option value="courseAsc">{{ t('course') }} ID 0→9</option>

            <option value="courseTitleAsc">{{ t('course') }} A→Z</option>
            <option value="courseTitleDesc">{{ t('course') }} Z→A</option>
            <option disabled>──────────────</option>

            <option value="moduleDesc">{{ t('module') }} ID 9→0</option>
            <option value="moduleAsc">{{ t('module') }} ID 0→9</option>

            <option value="moduleTitleAsc">{{ t('module') }} A→Z</option>
            <option value="moduleTitleDesc">{{ t('module') }} Z→A</option>
            <option disabled>──────────────</option>

            <option value="lessonDesc">{{ t('lesson') }} ID 9→0</option>
            <option value="lessonAsc">{{ t('lesson') }} ID 0→9</option>

            <option value="lessonTitleAsc">{{ t('lesson') }} A→Z</option>
            <option value="lessonTitleDesc">{{ t('lesson') }} Z→A</option>
            <option disabled>──────────────</option>

            <option value="instructorDesc">{{ t('instructor') }} ID 9→0</option>
            <option value="instructorAsc">{{ t('instructor') }} ID 0→9</option>

            <option value="instructorTitleAsc">{{ t('instructor') }} A→Z</option>
            <option value="instructorTitleDesc">{{ t('instructor') }} Z→A</option>
            <option disabled>──────────────</option>

            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>

            <option value="dueAtDesc">{{ t('dueAt') }} ↓</option>
            <option value="dueAtAsc">{{ t('dueAt') }} ↑</option>
            <option disabled>──────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>──────────────</option>

        </select>
    </div>
</template>
