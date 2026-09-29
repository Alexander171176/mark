<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Выбор сортировки полей динамических форм.
 *
 * Значения параметров соответствуют
 * FormField::scopeSortByParam().
 */

import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: {
        type: String,
        default: 'sortAsc',
    },
})

const emit = defineEmits([
    'update:sortParam',
])

/**
 * Обновление параметра сортировки.
 */
const updateSort = (event) => {
    emit(
        'update:sortParam',
        event.target.value
    )
}
</script>

<template>
    <div class="flex justify-center items-center h-fit">
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
            class="w-64 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <!-- ID -->
            <option value="idDesc">
                ID ↓
            </option>

            <option value="idAsc">
                ID ↑
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Порядок -->
            <option value="sortAsc">
                {{ t('sortNumber') }} 0→9
            </option>

            <option value="sortDesc">
                {{ t('sortNumber') }} 9→0
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Название / label -->
            <option value="labelAsc">
                {{ t('label') }} A→Z
            </option>

            <option value="labelDesc">
                {{ t('label') }} Z→A
            </option>

            <!-- Системное имя -->
            <option value="nameAsc">
                {{ t('name') }} A→Z
            </option>

            <option value="nameDesc">
                {{ t('name') }} Z→A
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Тип поля -->
            <option value="typeAsc">
                {{ t('type') }} A→Z
            </option>

            <option value="typeDesc">
                {{ t('type') }} Z→A
            </option>

            <!-- Ширина -->
            <option value="widthAsc">
                {{ t('width') }} A→Z
            </option>

            <option value="widthDesc">
                {{ t('width') }} Z→A
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Активность -->
            <option value="activityDesc">
                {{ t('activity') }} ON→OFF
            </option>

            <option value="activityAsc">
                {{ t('activity') }} OFF→ON
            </option>

            <option value="activity">
                {{ t('active') }}
            </option>

            <option value="inactive">
                {{ t('inactive') }}
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Обязательность -->
            <option value="requiredDesc">
                {{ t('required') }} YES→NO
            </option>

            <option value="requiredAsc">
                {{ t('required') }} NO→YES
            </option>

            <option value="required">
                {{ t('required') }}
            </option>

            <option value="optional">
                {{ t('optional') }}
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Только чтение -->
            <option value="readonlyDesc">
                {{ t('readOnly') }}  YES→NO
            </option>

            <option value="readonlyAsc">
                {{ t('readOnly') }}  NO→YES
            </option>

            <option value="readonly">
                {{ t('readOnly') }}
            </option>

            <option value="editable">
                {{ t('editable') }}
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Disabled -->
            <option value="disabledDesc">
                {{ t('disabled') }} YES→NO
            </option>

            <option value="disabledAsc">
                {{ t('disabled') }} NO→YES
            </option>

            <option value="disabled">
                {{ t('disabled') }}
            </option>

            <option value="enabled">
                {{ t('enabled') }}
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Количество вариантов -->
            <option value="optionsDesc">
                {{ t('variants') }} 9→0
            </option>

            <option value="optionsAsc">
                {{ t('variants') }} 0→9
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Родительская форма -->
            <option value="formIdDesc">
                {{ t('form') }} ID ↓
            </option>

            <option value="formIdAsc">
                {{ t('form') }} ID ↑
            </option>

            <option value="formTitleAsc">
                {{ t('form') }} A→Z
            </option>

            <option value="formTitleDesc">
                {{ t('form') }} Z→A
            </option>

            <option disabled>
                ────────────────────
            </option>

            <!-- Даты -->
            <option value="createdAtDesc">
                {{ t('createdAt') }} ↓
            </option>

            <option value="createdAtAsc">
                {{ t('createdAt') }} ↑
            </option>

            <option value="updatedAtDesc">
                {{ t('updatedAt') }} ↓
            </option>

            <option value="updatedAtAsc">
                {{ t('updatedAt') }} ↑
            </option>
        </select>
    </div>
</template>
