<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Выбор сортировки динамических форм.
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
            class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200
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
                ─────────────────
            </option>

            <!-- Порядок -->
            <option value="sortAsc">
                {{ t('sortNumber') }} 0→9
            </option>

            <option value="sortDesc">
                {{ t('sortNumber') }} 9→0
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- Название -->
            <option value="titleAsc">
                {{ t('title') }} A→Z
            </option>

            <option value="titleDesc">
                {{ t('title') }} Z→A
            </option>

            <!-- Код -->
            <option value="codeAsc">
                Code A→Z
            </option>

            <option value="codeDesc">
                Code Z→A
            </option>

            <option disabled>
                ─────────────────
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
                ─────────────────
            </option>

            <!-- Статус -->
            <option value="statusAsc">
                {{ t('status') }} A→Z
            </option>

            <option value="statusDesc">
                {{ t('status') }} Z→A
            </option>

            <option value="statusDraft">
                {{ t('statusDraft') }}
            </option>

            <option value="statusPublished">
                {{ t('statusPublished') }}
            </option>

            <option value="statusArchived">
                {{ t('statusArchived') }}
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- Количество полей -->
            <option value="fieldsCountDesc">
                Поля 9→0
            </option>

            <option value="fieldsCountAsc">
                Поля 0→9
            </option>

            <!-- Количество заявок -->
            <option value="submissionsCountDesc">
                Заявки 9→0
            </option>

            <option value="submissionsCountAsc">
                Заявки 0→9
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- Владелец -->
            <option value="ownerNameAsc">
                {{ t('owner') }} A→Z
            </option>

            <option value="ownerNameDesc">
                {{ t('owner') }} Z→A
            </option>

            <option value="ownerEmailAsc">
                {{ t('ownerEmail') }} A→Z
            </option>

            <option value="ownerEmailDesc">
                {{ t('ownerEmail') }} Z→A
            </option>

            <option disabled>
                ─────────────────
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
