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
                class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                       text-gray-600 dark:text-gray-900
                       border border-slate-400 dark:border-slate-600
                       rounded-sm shadow-sm">

            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>

            <option value="sortAsc">{{ t('sortNumber') }} ↑</option>
            <option value="sortDesc">{{ t('sortNumber') }} ↓</option>
            <option disabled>───────────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>───────────────────</option>

            <option value="courseTitleAsc">{{ t('course') }} ↑</option>
            <option value="courseTitleDesc">{{ t('course') }} ↓</option>

            <option value="currencyCodeAsc">{{ t('currency') }} ↑</option>
            <option value="currencyCodeDesc">{{ t('currency') }} ↓</option>
            <option disabled>───────────────────</option>

            <option value="effectivePriceAsc">{{ t('sortByEffectivePrice') }} ↑</option>
            <option value="effectivePriceDesc">{{ t('sortByEffectivePrice') }} ↓</option>

            <option value="priceAsc">{{ t('sortByPrice') }} ↑</option>
            <option value="priceDesc">{{ t('sortByPrice') }} ↓</option>

            <option value="salePriceAsc">{{ t('sortBySalePrice') }} ↑</option>
            <option value="salePriceDesc">{{ t('sortBySalePrice') }} ↓</option>

            <option value="compareAtPriceAsc">{{ t('sortByCompareAtPrice') }} ↑</option>
            <option value="compareAtPriceDesc">{{ t('sortByCompareAtPrice') }} ↓</option>

            <option value="discountPercentAsc">{{ t('sortByDiscountPercent') }} ↑</option>
            <option value="discountPercentDesc">{{ t('sortByDiscountPercent') }} ↓</option>
            <option disabled>───────────────────</option>

            <option value="startsAtAsc">{{ t('priceStartsAt') }} ↑</option>
            <option value="startsAtDesc">{{ t('priceStartsAt') }} ↓</option>

            <option value="endsAtAsc">{{ t('priceEndsAt') }} ↑</option>
            <option value="endsAtDesc">{{ t('priceEndsAt') }} ↓</option>
            <option disabled>───────────────────</option>

        </select>
    </div>
</template>
