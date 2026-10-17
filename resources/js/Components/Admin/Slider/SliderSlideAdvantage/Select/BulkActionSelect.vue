<script setup>
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
            @change="emits('change', $event)"
        >
            <option value="">— {{ t('selectAction') }} —</option>
            <option value="selectAll">{{ t('selectAll') }}</option>
            <option value="deselectAll">{{ t('deselectAll') }}</option>
            <option disabled>───────────────────────</option>

            <option value="activate">{{ t('activate') }}</option>
            <option value="deactivate">{{ t('deactivate') }}</option>
            <option disabled>───────────────────────</option>

            <option value="left">{{ t('left') }}: ON</option>
            <option value="noLeft">{{ t('left') }}: OFF</option>
            <option value="main">{{ t('main') }}: ON</option>
            <option value="noMain">{{ t('main') }}: OFF</option>
            <option value="right">{{ t('right') }}: ON</option>
            <option value="noRight">{{ t('right') }}: OFF</option>
            <option disabled>───────────────────────</option>

            <option value="isNewOn">{{ t('sortIsNew') }}: ON</option>
            <option value="isNewOff">{{ t('sortIsNew') }}: OFF</option>
            <option value="isHitOn">{{ t('sortIsHit') }}: ON</option>
            <option value="isHitOff">{{ t('sortIsHit') }}: OFF</option>
            <option value="isSaleOn">{{ t('sortIsSale') }}: ON</option>
            <option value="isSaleOff">{{ t('sortIsSale') }}: OFF</option>
            <option disabled>───────────────────────</option>

            <option value="delete">{{ t('deleteSelected') }}</option>
            <option disabled>───────────────────────</option>
        </select>
    </div>
</template>
