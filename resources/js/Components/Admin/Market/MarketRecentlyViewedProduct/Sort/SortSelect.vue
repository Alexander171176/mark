<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: {
        type: String,
        default: 'viewedAtDesc',
    },
})

const emit = defineEmits([
    'update:sortParam',
])

/**
 * Изменение сортировки
 * истории просмотренных товаров.
 */
const updateSort = (event) => {
    emit(
        'update:sortParam',
        event.target.value
    )
}
</script>

<template>
    <div class="flex justify-center items-center h-fit my-2">
        <label
            for="sortParam"
            class="hidden lg:block sm:mr-2 tracking-wider
                   text-sm font-semibold
                   text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="sortParam"
            :value="sortParam"
            @change="updateSort"
            class="w-56 px-3 py-0.5 form-select
                   bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <!-- Дата просмотра -->
            <option value="viewedAtDesc">{{ t('viewedAt') }} ↓</option>
            <option value="viewedAtAsc">{{ t('viewedAt') }} ↑</option>
            <option disabled>─────────────────</option>

            <!-- ID записи истории -->
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option disabled>─────────────────</option>

            <!-- ID товара -->
            <option value="productIdDesc">{{ t('marketProduct') }} ID ↓</option>
            <option value="productIdAsc">{{ t('marketProduct') }} ID ↑</option>
            <option disabled>─────────────────</option>

            <!-- Название товара -->
            <option value="productTitleAsc">{{ t('marketProduct') }} A→Z</option>
            <option value="productTitleDesc">{{ t('marketProduct') }} Z→A</option>
            <option disabled>─────────────────</option>
        </select>
    </div>
</template>
