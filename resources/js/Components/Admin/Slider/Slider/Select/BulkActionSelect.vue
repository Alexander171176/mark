
<script setup>
/**
 * @version PulsarCMS 1.0
 *
 * Массовые действия со слайдерами.
 *
 * - Выбрать все;
 * - Снять выделение;
 * - Активировать;
 * - Деактивировать;
 * - Удалить выбранные.
 */

import { useI18n } from 'vue-i18n'

/* ======================== Services ======================== */

const { t } = useI18n()

/* ======================== Props ======================== */

defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
})

/* ======================== Emits ======================== */

const emit = defineEmits([
    'change',
])

/* ======================== Methods ======================== */

/**
 * Передача выбранного действия родительскому компоненту.
 *
 * После выбора сбрасываем select, чтобы одно и то же
 * действие можно было выполнить повторно.
 */
const handleChange = (event) => {
    const value = event.target.value

    if (!value) {
        return
    }

    emit('change', value)

    event.target.value = ''
}
</script>

<template>
    <div class="flex flex-col sm:flex-row items-center">
        <!-- Название -->

        <label
            class="block mb-2 sm:mb-0 sm:mr-2 font-semibold
                   text-sm text-slate-700 dark:text-slate-300"
        >
            {{ t('bulkActions') }}
        </label>

        <!-- Выбор массового действия -->

        <select
            :disabled="disabled"
            class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm
                   bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   disabled:opacity-50 disabled:cursor-not-allowed"
            @change="handleChange"
        >
            <!-- Выбор действия -->

            <option value="">
                — {{ t('selectAction') }} —
            </option>

            <!-- Выделение -->

            <option value="selectAll">
                {{ t('selectAll') }}
            </option>

            <option value="deselectAll">
                {{ t('deselectAll') }}
            </option>

            <option disabled>
                ───────────────────────
            </option>

            <!-- Активность -->

            <option value="activate">
                {{ t('activate') }}
            </option>

            <option value="deactivate">
                {{ t('deactivate') }}
            </option>

            <option disabled>
                ───────────────────────
            </option>

            <!-- Удаление -->

            <option value="delete">
                {{ t('deleteSelected') }}
            </option>

            <option disabled>
                ───────────────────────
            </option>
        </select>
    </div>
</template>
