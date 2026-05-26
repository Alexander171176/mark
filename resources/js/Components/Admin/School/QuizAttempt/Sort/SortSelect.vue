<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    sortParam: String,
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
            class="w-44 px-3 py-0.5 form-select bg-white dark:bg-gray-200
             text-gray-600 dark:text-gray-900
             border border-slate-400 dark:border-slate-600
             rounded-sm shadow-sm"
        >
            <!-- ID -->
            <optgroup :label="t('id')">
                <option value="idDesc">{{ t('idDesc') }}</option>
                <option value="idAsc">{{ t('idAsc') }}</option>
                <option disabled>──────────</option>
            </optgroup>

            <!-- Started -->
            <optgroup :label="t('shortStarted')">
                <option value="startedAtDesc">{{ t('idDesc') }}</option>
                <option value="startedAtAsc">{{ t('idAsc') }}</option>
                <option disabled>──────────</option>
            </optgroup>

            <!-- Finished -->
            <optgroup :label="t('shortExpires')">
                <option value="finishedAtDesc">{{ t('idDesc') }}</option>
                <option value="finishedAtAsc">{{ t('idAsc') }}</option>
                <option disabled>──────────</option>
            </optgroup>

            <!-- Percent -->
            <optgroup :label="t('percent')">
                <option value="percentDesc">{{ t('idDesc') }}</option>
                <option value="percentAsc">{{ t('idAsc') }}</option>
                <option disabled>──────────</option>
            </optgroup>
        </select>
    </div>
</template>
