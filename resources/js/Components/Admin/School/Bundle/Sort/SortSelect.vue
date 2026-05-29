<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: String,
})

const emits = defineEmits(['update:sortParam'])

const updateSort = (event) => {
    emits('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit my-2">
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
            class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>

            <option value="sortAsc">{{ t('sort') }} ↑</option>
            <option value="sortDesc">{{ t('sort') }} ↓</option>

            <option disabled>────────────────</option>

            <option value="titleAsc">{{ t('title') }} (A → Z)</option>
            <option value="titleDesc">{{ t('title') }} (Z → A)</option>

            <option value="publishedAtAsc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtDesc">{{ t('publishedAt') }} ↑</option>

            <option disabled>────────────────</option>

            <option value="views">{{ t('views') }}</option>
            <option value="likes">{{ t('likes') }}</option>

            <option disabled>────────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
        </select>
    </div>
</template>
