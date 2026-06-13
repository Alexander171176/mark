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
            <option disabled>──────────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>──────────────────</option>

            <option value="titleAsc">{{ t('title') }} A→Z</option>
            <option value="titleDesc">{{ t('title') }} Z→A</option>

            <option value="slugAsc">Slug A→Z</option>
            <option value="slugDesc">Slug Z→A</option>
            <option disabled>──────────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>──────────────────</option>

            <option value="online">{{ t('online') }}</option>
            <option value="offline">{{ t('offline') }}</option>
            <option value="onlineDesc">{{ t('online') }} ON→OFF</option>
            <option value="onlineAsc">{{ t('online') }} OFF→ON</option>
            <option disabled>──────────────────</option>

            <option value="statusAsc">{{ t('status') }} A→Z</option>
            <option value="statusDesc">{{ t('status') }} Z→A</option>

            <option value="timezoneAsc">{{ t('timezone') }} A→Z</option>
            <option value="timezoneDesc">{{ t('timezone') }} Z→A</option>

            <option value="locationAsc">{{ t('location') }} A→Z</option>
            <option value="locationDesc">{{ t('location') }} Z→A</option>
            <option disabled>──────────────────</option>

            <option value="capacityDesc">{{ t('capacity') }} 9→0</option>
            <option value="capacityAsc">{{ t('capacity') }} 0→9</option>

            <option value="viewsDesc">{{ t('views') }} 9→0</option>
            <option value="viewsAsc">{{ t('views') }} 0→9</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>

            <option value="cohortEnrollmentsDesc">{{ t('enrollments') }} 9→0</option>
            <option value="cohortEnrollmentsAsc">{{ t('enrollments') }} 0→9</option>
            <option disabled>──────────────────</option>

            <option value="startsAtDesc">{{ t('scheduleStartsAt') }} ↓</option>
            <option value="startsAtAsc">{{ t('scheduleStartsAt') }} ↑</option>

            <option value="endsAtDesc">{{ t('scheduleEndsAt') }} ↓</option>
            <option value="endsAtAsc">{{ t('scheduleEndsAt') }} ↑</option>
            <option disabled>──────────────────</option>

            <option value="enrollStartsAtDesc">{{ t('scheduleEnrollStartsAt') }} ↓</option>
            <option value="enrollStartsAtAsc">{{ t('scheduleEnrollStartsAt') }} ↑</option>

            <option value="enrollEndsAtDesc">{{ t('scheduleEnrollEndsAt') }} ↓</option>
            <option value="enrollEndsAtAsc">{{ t('scheduleEnrollEndsAt') }} ↑</option>
            <option disabled>──────────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>──────────────────</option>
        </select>
    </div>
</template>
