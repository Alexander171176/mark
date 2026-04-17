<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    sortParam: {
        type: String,
        default: 'idDesc',
    },
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
            class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <!-- ID -->
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option disabled>──────────────────</option>

            <!-- Статус -->
            <option value="status">{{ t('status') }}</option>
            <option value="progress_desc">{{ t('progress') }} ↓</option>
            <option disabled>──────────────────</option>

            <!-- Даты доступа -->
            <option value="started_at">{{ t('shortStarted') }}</option>
            <option value="expires_at">{{ t('shortExpires') }}</option>
            <option value="completed_at">{{ t('shortCompleted') }}</option>
            <option disabled>──────────────────</option>

        </select>
    </div>
</template>
