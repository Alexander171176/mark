<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: { type: String, default: 'nameAsc' },
})

const emit = defineEmits(['update:sortParam'])

const updateSort = (event) => {
    emit('update:sortParam', event.target.value)
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
            <option disabled>─────────────</option>

            <option value="nameAsc">{{ t('name') }} A→Z</option>
            <option value="nameDesc">{{ t('name') }} Z→A</option>
            <option disabled>─────────────</option>

            <option value="guardNameAsc">Guard A→Z</option>
            <option value="guardNameDesc">Guard Z→A</option>
            <option disabled>─────────────</option>

            <option value="permissionsDesc">{{ t('permissions') }} 9→0</option>
            <option value="permissionsAsc">{{ t('permissions') }} 0→9</option>
            <option disabled>─────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>─────────────</option>
        </select>
    </div>
</template>
