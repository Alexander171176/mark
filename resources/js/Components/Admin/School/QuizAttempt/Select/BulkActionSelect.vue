<script setup>
/**
 * BulkActionSelect для QuizAttempt
 * Паттерн
 */
import { defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const emits = defineEmits(['change'])
const { t } = useI18n()
</script>

<template>
    <div class="flex flex-col sm:flex-row items-center">
        <label
            class="block mb-2 sm:mb-0 sm:mr-2 font-semibold
                   text-sm text-slate-700 dark:text-slate-300"
        >
            {{ t('bulkActions') }}
        </label>

        <select
            class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm
                   bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600"
            @change="$emit('change', $event)"
        >
            <option value="">
                — {{ t('selectAction') }} —
            </option>

            <!-- Выбор -->
            <option value="selectAll">
                {{ t('selectAll') }}
            </option>
            <option value="deselectAll">
                {{ t('deselectAll') }}
            </option>

            <option disabled>──────────────────</option>

            <!-- Статусы -->
            <option value="status:in_progress">
                {{ t('setStatusInProgress') || 'Статус: В процессе' }}
            </option>
            <option value="status:completed">
                {{ t('setStatusCompleted') || 'Статус: Завершён' }}
            </option>
            <option value="status:graded">
                {{ t('setStatusGraded') || 'Статус: Проверен' }}
            </option>

            <option disabled>──────────────────</option>

            <!-- Удаление -->
            <option value="delete">
                {{ t('deleteSelected') }}
            </option>

            <option disabled>──────────────────</option>
        </select>
    </div>
</template>
