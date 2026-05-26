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

            <!-- ID -->
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>

            <!-- Порядок (sort) -->
            <option value="sortAsc">{{ t('sortNumber') }} ↑</option>
            <option value="sortDesc">{{ t('sortNumber') }} ↓</option>

            <option disabled>────────────────</option>

            <!-- Заголовок  -->
            <option value="titleAsc">{{ t('title') }} A→Z</option>
            <option value="titleDesc">{{ t('title') }} Z→A</option>
            <option disabled>────────────────</option>

            <!-- Публикация / даты -->
            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>
            <option disabled>────────────────</option>

            <!-- Окно доступности -->
            <option value="availabilityNowFirst">{{ t('availability') }}</option>
            <option value="availableFromAsc">{{ t('shortStarted') }} ↑</option>
            <option value="availableFromDesc">{{ t('shortStarted') }} ↓</option>
            <option value="availableUntilAsc">{{ t('shortExpires') }} ↑</option>
            <option value="availableUntilDesc">{{ t('shortExpires') }} ↓</option>
            <option disabled>────────────────</option>

            <!-- Стоимость -->
            <option value="priceDesc">{{ t('price') }} ↓</option>
            <option value="priceAsc">{{ t('price') }} ↑</option>
            <option disabled>────────────────</option>

            <option value="trialDaysDesc">{{ t('trial') }} ↓</option>
            <option value="trialDaysAsc">{{ t('trial') }} ↑</option>
            <option disabled>────────────────</option>

            <!-- Период/провайдер/локаль -->
            <option value="billingPeriodAsc">{{ t('billingPeriod') }} A→Z</option>
            <option value="billingPeriodDesc">{{ t('billingPeriod') }} Z→A</option>

            <option disabled>────────────────</option>

            <!-- Активность (фильтры) -->
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
        </select>
    </div>
</template>
