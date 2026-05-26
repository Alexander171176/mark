<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    handleBulkAction: Function
})

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

            <!-- Активация -->
            <option disabled>──────────────────</option>
            <option value="activate">{{ t('activate') }}</option>
            <option value="deactivate">{{ t('deactivate') }}</option>
            <option disabled>──────────────────</option>
        </select>
    </div>
</template>
