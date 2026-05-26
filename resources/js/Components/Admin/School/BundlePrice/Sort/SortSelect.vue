<script setup>
/**
 * Селект сортировки для прайсов бандлов.
 * Паттерн как у прайсов курсов, но привязка: course -> bundle.
 */
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    sortParam: String,
})

const emits = defineEmits(['update:sortParam'])

const updateSort = (event) => {
    emits('update:sortParam', event.target.value)
}
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
            class="w-60 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option value="sort">{{ t('sortNumber') }}</option>
            <option disabled>───────────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>───────────────────</option>

            <!-- Привязки -->
            <option value="bundle">{{ t('sortByBundle') }}</option>
            <option value="currency">{{ t('sortByCurrency') }}</option>
            <option disabled>───────────────────</option>

            <!-- Цены -->
            <option value="effective_price">{{ t('sortByEffectivePrice') }}</option>
            <option value="price">{{ t('sortByPrice') }}</option>
            <option value="sale_price">{{ t('sortBySalePrice') }}</option>
            <option value="compare_at_price">{{ t('sortByCompareAtPrice') }}</option>
            <option value="discount_percent">{{ t('sortByDiscountPercent') }}</option>
            <option disabled>───────────────────</option>

            <!-- Период -->
            <option value="starts_at">{{ t('priceStartsAt') }}</option>
            <option value="ends_at">{{ t('priceEndsAt') }}</option>
            <option disabled>───────────────────</option>
        </select>
    </div>
</template>
