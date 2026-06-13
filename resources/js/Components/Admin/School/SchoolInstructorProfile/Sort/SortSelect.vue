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
            <option disabled>─────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>─────────────</option>

            <option value="titleAsc">{{ t('title') }} A→Z</option>
            <option value="titleDesc">{{ t('title') }} Z→A</option>
            <option disabled>─────────────</option>

            <option value="slugAsc">{{ t('url') }} ↑</option>
            <option value="slugDesc">{{ t('url') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────</option>

            <option value="viewsAsc">{{ t('views') }} ↑</option>
            <option value="viewsDesc">{{ t('views') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="ratingAvgAsc">{{ t('rating') }} AVG 0→9</option>
            <option value="ratingAvgDesc">{{ t('rating') }} AVG 9→0</option>
            <option value="ratingCountAsc">{{ t('rating') }} ↑</option>
            <option value="ratingCountDesc">{{ t('rating') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="hourlyRateAsc">{{ t('hourlyRate') }} ↑</option>
            <option value="hourlyRateDesc">{{ t('hourlyRate') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="experienceAsc">{{ t('experienceYears') }} ↑</option>
            <option value="experienceDesc">{{ t('experienceYears') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="dateAsc">{{ t('sortOldestFirst') }}</option>
            <option value="dateDesc">{{ t('sortNewestFirst') }}</option>

            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>
            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>

            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="coursesDesc">{{ t('courses') }} 9→0</option>
            <option value="coursesAsc">{{ t('courses') }} 0→9</option>
            <option disabled>─────────────</option>

            <option value="payoutsDesc">{{ t('payouts') }} 9→0</option>
            <option value="payoutsAsc">{{ t('payouts') }} 0→9</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>
            <option disabled>─────────────</option>
        </select>
    </div>
</template>
