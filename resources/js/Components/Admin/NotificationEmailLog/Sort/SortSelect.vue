
<script setup>
/**
 * @version PulsarCMS 1.0
 *
 * Выбор сортировки журнала Email Notifications.
 */

import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: {
        type: String,
        default: 'idDesc',
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
            for="emailLogsSortParam"
            class="hidden lg:block sm:mr-2 tracking-wider
                   text-sm font-semibold text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="emailLogsSortParam"
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

            <!-- Дата создания -->
            <option value="createdAtDesc">
                {{ t('createdAt') }} ↓
            </option>

            <option value="createdAtAsc">
                {{ t('createdAt') }} ↑
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- Получатель -->
            <option value="recipientAsc">
                {{ t('recipient') }} A→Z
            </option>

            <option value="recipientDesc">
                {{ t('recipient') }} Z→A
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

            <option disabled>
                ─────────────────
            </option>

            <!-- Количество попыток -->
            <option value="attemptsAsc">
                {{ t('sortAttemptIdAsc') }} 0→9
            </option>

            <option value="attemptsDesc">
                {{ t('sortAttemptIdDesc') }} 9→0
            </option>
        </select>
    </div>
</template>
