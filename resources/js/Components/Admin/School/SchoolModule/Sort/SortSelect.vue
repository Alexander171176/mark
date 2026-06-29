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
            <option disabled>─────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} ↑</option>
            <option value="sortDesc">{{ t('sortNumber') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="titleAsc">{{ t('title') }} ↑</option>
            <option value="titleDesc">{{ t('title') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="slugAsc">Slug A→Z</option>
            <option value="slugDesc">Slug Z→A</option>
            <option disabled>─────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────</option>

            <option value="lessonsDesc">{{ t('lessonsCount') }} 9→0</option>
            <option value="lessonsAsc">{{ t('lessonsCount') }} 0→9</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>
            <option disabled>─────────────</option>

            <!-- просмотры, лайки -->
            <option value="viewsDesc">{{ t('views') }} 9→0</option>
            <option value="viewsAsc">{{ t('views') }} 0→9</option>

            <option value="likesDesc">{{ t('likes') }} 9→0</option>
            <option value="likesAsc">{{ t('likes') }} 0→9</option>

            <option value="likesCountDesc">{{ t('ratingCount') }} 9→0</option>
            <option value="likesCountAsc">{{ t('ratingCount') }} 0→9</option>
            <option disabled>─────────────</option>

            <!-- Метрики курса -->
            <option value="popularityDesc">{{ t('popularity') }} 9→0</option>
            <option value="popularityAsc">{{ t('popularity') }} 0→9</option>

            <option value="ratingCountDesc">{{ t('ratingCount') }} 9→0</option>
            <option value="ratingCountAsc">{{ t('ratingCount') }} 0→9</option>

            <option value="ratingAvgDesc">{{ t('ratingAvg') }} 9→0</option>
            <option value="ratingAvgAsc">{{ t('ratingAvg') }} 0→9</option>
            <option disabled>─────────────</option>

            <!-- Статус / Доступность -->
            <option value="statusAsc">{{ t('status') }} A→Z</option>
            <option value="statusDesc">{{ t('status') }} Z→A</option>

            <option value="availabilityAsc">{{ t('availability') }} A→Z</option>
            <option value="availabilityDesc">{{ t('availability') }} Z→A</option>
            <option disabled>─────────────</option>

            <!-- Метрики сложности и длительности -->
            <option value="difficultyDesc">{{ t('sortDifficulty') }} 9→0</option>
            <option value="difficultyAsc">{{ t('sortDifficulty') }} 0→9</option>

            <option value="durationDesc">{{ t('duration') }} 9→0</option>
            <option value="durationAsc">{{ t('duration') }} 0→9</option>
            <option disabled>─────────────</option>

            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>─────────────</option>

            <option value="courseDesc">{{ t('course') }} ID 9→0</option>
            <option value="courseAsc">{{ t('course') }} ID 0→9</option>
            <option disabled>─────────────</option>

        </select>
    </div>
</template>
