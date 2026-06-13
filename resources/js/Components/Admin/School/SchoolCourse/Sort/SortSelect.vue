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
            <option disabled>───────────────────</option>

            <option value="titleAsc">{{ t('title') }} ↑</option>
            <option value="titleDesc">{{ t('title') }} ↓</option>
            <option disabled>───────────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>───────────────────</option>

            <option value="left">{{ t('inLeft') }}</option>
            <option value="noLeft">{{ t('notLeft') }}</option>
            <option disabled>───────────────────</option>

            <option value="main">{{ t('inMain') }}</option>
            <option value="noMain">{{ t('notMain') }}</option>
            <option disabled>───────────────────</option>

            <option value="right">{{ t('inRight') }}</option>
            <option value="noRight">{{ t('notRight') }}</option>
            <option disabled>───────────────────</option>

            <!-- Новинки / хиты / распродажа -->
            <option value="isNew">{{ t('sortIsNew') }}</option>
            <option value="isHit">{{ t('sortIsHit') }}</option>
            <option value="isSale">{{ t('sortIsSale') }}</option>
            <option disabled>───────────────────</option>

            <!-- Просмотры, лайки -->
            <option value="viewsDesc">{{ t('views') }} 9→0</option>
            <option value="viewsAsc">{{ t('views') }} 0→9</option>

            <option value="likesDesc">{{ t('likes') }} 9→0</option>
            <option value="likesAsc">{{ t('likes') }} 0→9</option>
            <option disabled>───────────────────</option>

            <!-- Метрики курса -->
            <option value="popularityDesc">{{ t('popularity') }} 9→0</option>
            <option value="popularityAsc">{{ t('popularity') }} 0→9</option>

            <option value="ratingCountDesc">{{ t('ratingCount') }} 9→0</option>
            <option value="ratingCountAsc">{{ t('ratingCount') }} 0→9</option>

            <option value="ratingAvgDesc">{{ t('ratingAvg') }} 9→0</option>
            <option value="ratingAvgAsc">{{ t('ratingAvg') }} 0→9</option>
            <option disabled>───────────────────</option>

            <!-- Уровень / статус / доступность -->
            <option value="levelAsc">{{ t('level') }} A→Z</option>
            <option value="levelDesc">{{ t('level') }} Z→A</option>

            <option value="statusAsc">{{ t('status') }} A→Z</option>
            <option value="statusDesc">{{ t('status') }} Z→A</option>

            <option value="availabilityAsc">{{ t('availability') }} A→Z</option>
            <option value="availabilityDesc">{{ t('availability') }} Z→A</option>
            <option disabled>───────────────────</option>

            <!-- Метрики сложности и длительности -->
            <option value="difficultyDesc">{{ t('sortDifficulty') }} 9→0</option>
            <option value="difficultyAsc">{{ t('sortDifficulty') }} 0→9</option>

            <option value="durationDesc">{{ t('duration') }} 9→0</option>
            <option value="durationAsc">{{ t('duration') }} 0→9</option>
            <option disabled>───────────────────</option>

            <option value="studentsCountDesc">{{ t('sortStudentsCount') }} 9→0</option>
            <option value="studentsCountAsc">{{ t('sortStudentsCount') }} 0→9</option>
            <option disabled>───────────────────</option>

            <option value="modulesDesc">{{ t('modules') }} 9→0</option>
            <option value="modulesAsc">{{ t('modules') }} 0→9</option>

            <option value="lessonsDesc">{{ t('lessons') }} 9→0</option>
            <option value="lessonsAsc">{{ t('lessons') }} 0→9</option>

            <option value="tracksDesc">{{ t('tracks') }} 9→0</option>
            <option value="tracksAsc">{{ t('tracks') }} 0→9</option>

            <option value="hashtagsDesc">{{ t('hashtags') }} 9→0</option>
            <option value="hashtagsAsc">{{ t('hashtags') }} 0→9</option>
            <option disabled>───────────────────</option>

            <option value="reviewsDesc">{{ t('reviews') }} 9→0</option>
            <option value="reviewsAsc">{{ t('reviews') }} 0→9</option>

            <option value="enrollmentsDesc">{{ t('enrollments') }} 9→0</option>
            <option value="enrollmentsAsc">{{ t('enrollments') }} 0→9</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>

            <option value="pricesDesc">{{ t('prices') }} 9→0</option>
            <option value="pricesAsc">{{ t('prices') }} 0→9</option>
            <option disabled>───────────────────</option>

            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>───────────────────</option>

        </select>
    </div>
</template>
