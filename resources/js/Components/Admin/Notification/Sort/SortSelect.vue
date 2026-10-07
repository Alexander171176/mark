<script setup>
import { defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: {
        type: String,
        default: 'createdAtDesc',
    },
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
            for="notificationSortParam"
            class="hidden lg:block sm:mr-2 tracking-wider
                   text-sm font-semibold text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="notificationSortParam"
            :value="sortParam"
            class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
            @change="updateSort"
        >
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

            <!-- Состояние прочтения -->
            <option value="readAsc">
                {{ t('read') }} ↓
            </option>

            <option value="readDesc">
                {{ t('read') }} ↑
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- Категория -->
            <option value="categoryAsc">
                {{ t('category') }} A→Z
            </option>

            <option value="categoryDesc">
                {{ t('category') }} Z→A
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- Уровень -->
            <option value="levelAsc">
                {{ t('level') }} A→Z
            </option>

            <option value="levelDesc">
                {{ t('level') }} Z→A
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- Дата изменения -->
            <option value="updatedAtDesc">
                {{ t('updatedAt') }} ↓
            </option>

            <option value="updatedAtAsc">
                {{ t('updatedAt') }} ↑
            </option>
        </select>
    </div>
</template>
