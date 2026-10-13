<script setup>
/**
 * Сортировка слайдеров в административном списке.
 *
 * Значения параметров согласованы с локальной сортировкой Index.vue.
 * Все варианты, кроме slidesDesc, поддерживаются scopeSortByParam()
 * модели Slider. slidesDesc работает только на frontend.
 */

import { useI18n } from 'vue-i18n'

/* =========================================================
 * LOCALIZATION
 * ========================================================= */

/** Сервис переводов интерфейса */
const { t } = useI18n()

/* =========================================================
 * PROPS / EMITS
 * ========================================================= */

/** Текущая сортировка и состояние доступности селекта */
defineProps({
    sortParam: {
        type: String,
        default: 'sortAsc',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

/** Событие изменения выбранного способа сортировки */
const emit = defineEmits(['update:sortParam'])

/* =========================================================
 * METHODS
 * ========================================================= */

/** Передача нового значения сортировки родительской странице */
const updateSort = event => {
    emit('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit">
        <label
            for="sliderSortParam"
            class="hidden lg:block sm:mr-2 tracking-wider
                   text-sm font-semibold text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="sliderSortParam"
            :value="sortParam"
            :disabled="disabled"
            @change="updateSort"
            class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <!-- Порядок отображения -->
            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>─────────────────</option>

            <!-- Идентификатор -->
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option disabled>─────────────────</option>

            <!-- Название слайдера -->
            <option value="titleAsc">{{ t('title') }} A→Z</option>
            <option value="titleDesc">{{ t('title') }} Z→A</option>
            <option disabled>─────────────────</option>

            <!-- Количество слайдов -->
            <option value="slidesDesc">Больше слайдов</option>
            <option disabled>─────────────────</option>

            <!-- Дата создания -->
            <option value="createdDesc">Новые сначала</option>
        </select>
    </div>
</template>
