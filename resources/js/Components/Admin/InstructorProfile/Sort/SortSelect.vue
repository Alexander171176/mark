<script setup>
import {defineProps, defineEmits} from 'vue';
import {useI18n} from 'vue-i18n';

const {t} = useI18n();

const props = defineProps({
    sortParam: String
});

const emits = defineEmits(['update:sortParam']);

const updateSort = (event) => {
    emits('update:sortParam', event.target.value);
};
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
            <option disabled>─────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} ↑</option>
            <option value="sortDesc">{{ t('sortNumber') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="titleAsc">{{ t('title') }} ↑</option>
            <option value="titleDesc">{{ t('title') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="slugAsc">{{ t('url') }} ↑</option>
            <option value="slugDesc">{{ t('url') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="ratingCountAsc">{{t('rating')}} ↑</option>
            <option value="ratingCountDesc">{{t('rating')}} ↓</option>
            <option disabled>─────────────</option>

            <option value="viewsAsc">{{ t('views') }} ↑</option>
            <option value="viewsDesc">{{ t('views') }} ↓</option>
            <option disabled>─────────────</option>

            <option value="hourlyRateAsc">{{t('hourlyRate')}} ↑</option>
            <option value="hourlyRateDesc">{{t('hourlyRate')}} ↓</option>
            <option disabled>─────────────</option>

            <option value="experienceAsc">{{t('experienceYears')}} ↑</option>
            <option value="experienceDesc">{{t('experienceYears')}} ↓</option>
            <option disabled>─────────────</option>

            <option value="createdAtAsc">{{t('createdAt')}} ↑</option>
            <option value="createdAtDesc">{{t('createdAt')}} ↓</option>
            <option disabled>─────────────</option>

            <option value="updatedAtAsc">{{t('updatedAt')}} ↑</option>
            <option value="updatedAtDesc">{{t('updatedAt')}} ↓</option>
            <option disabled>─────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────</option>
        </select>
    </div>
</template>
