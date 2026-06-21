<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: String,
})

const emits = defineEmits([
    'update:sortParam',
])

const updateSort = (event) => {
    emits(
        'update:sortParam',
        event.target.value
    )
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
            class="w-50 px-3 py-0.5 form-select
                   bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400
                   dark:border-slate-600
                   rounded-sm shadow-sm"
        >

            <!-- ID -->
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option disabled>─────────────</option>

            <!-- Пользовательская сортировка -->
            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>─────────────</option>

            <!-- Ключ -->
            <option value="keyAsc">{{ t('setupKey') }} A→Z</option>
            <option value="keyDesc">{{ t('setupKey') }} Z→A</option>
            <option disabled>─────────────</option>

            <!-- Название -->
            <option value="nameAsc">{{ t('title') }} A→Z</option>
            <option value="nameDesc">{{ t('title') }} Z→A</option>
            <option disabled>─────────────</option>

            <!-- Количество вариантов -->
            <option value="variantsDesc">{{ t('optionsOptions') }} 9→0</option>
            <option value="variantsAsc">{{ t('optionsOptions') }} 0→9</option>
            <option disabled>─────────────</option>

            <!-- Даты -->
            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>
            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>─────────────</option>

            <!-- Активность -->
            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────</option>

        </select>

    </div>
</template>
