<script setup>
import { computed, defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

import FilterPanel from '@/Components/Admin/UI/Filter/FilterPanel.vue'
import FilterText from '@/Components/Admin/UI/Filter/FilterText.vue'
import FilterNumber from '@/Components/Admin/UI/Filter/FilterNumber.vue'
import FilterSelect from '@/Components/Admin/UI/Filter/FilterSelect.vue'
import FilterDateRange from '@/Components/Admin/UI/Filter/FilterDateRange.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({}),
    },

    statuses: {
        type: [Array, Object],
        default: () => [],
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },
})

const emits = defineEmits([
    'update:modelValue',
    'apply',
    'reset',
])

/*
|--------------------------------------------------------------------------
| Обновление отдельного фильтра
|--------------------------------------------------------------------------
|
| modelValue напрямую не изменяем.
| Каждый раз возвращаем родителю новый объект.
|
*/

const updateFilter = (key, value) => {
    emits(
        'update:modelValue',
        {
            ...props.modelValue,
            [key]: value,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Активные фильтры
|--------------------------------------------------------------------------
*/

const hasActiveFilters = computed(() => {
    return Object.values(
        props.modelValue || {}
    ).some(
        (value) =>
            value !== ''
            && value !== null
            && value !== undefined
    )
})
</script>

<template>
    <FilterPanel
        :title="t('filters')"
        :has-active-filters="hasActiveFilters"
        columns="xl:grid-cols-5"
        :apply-text="t('applyFilters')"
        :reset-text="t('clearFilters')"
        @apply="emits('apply')"
        @reset="emits('reset')"
    >
        <!-- Форма -->
        <FilterNumber
            :model-value="modelValue.form_id"
            :label="`${t('form')} ID`"
            :placeholder="`${t('form')} ID`"
            :min="1"
            @update:model-value="updateFilter('form_id', $event)"
        />

        <!-- Статус -->
        <FilterSelect
            :model-value="modelValue.status"
            :label="t('status')"
            :placeholder="t('allStatuses')"
            :options="statuses"
            @update:model-value="updateFilter('status', $event)"
        />

        <!-- Источник -->
        <FilterText
            :model-value="modelValue.source"
            :label="t('source')"
            placeholder="public_form"
            @update:model-value="updateFilter('source', $event)"
        />

        <!-- Локаль -->
        <FilterSelect
            :model-value="modelValue.locale"
            :label="t('requestLocale')"
            :placeholder="t('allLocales')"
            :options="availableLocales"
            @update:model-value="updateFilter('locale', $event)"
        />

        <!-- Отправитель -->
        <FilterNumber
            :model-value="modelValue.user_id"
            :label="`${t('user')} ID`"
            :placeholder="`${t('user')} ID`"
            :min="1"
            @update:model-value="updateFilter('user_id', $event)"
        />

        <!-- Ответственный -->
        <FilterText
            :model-value="modelValue.assigned_user_id"
            :label="t('responsible')"
            :placeholder="`${t('responsible')} ID`"
            @update:model-value="updateFilter('assigned_user_id', $event)"
        />

        <!-- UTM source -->
        <FilterText
            :model-value="modelValue.utm_source"
            label="UTM source"
            placeholder="utm_source"
            @update:model-value="updateFilter('utm_source', $event)"
        />

        <!-- UTM campaign -->
        <FilterText
            :model-value="modelValue.utm_campaign"
            label="UTM campaign"
            placeholder="utm_campaign"
            @update:model-value="updateFilter('utm_campaign', $event)"
        />

        <!-- Период -->
        <FilterDateRange
            :from="modelValue.date_from"
            :to="modelValue.date_to"
            :from-label="`${t('date')} ${t('from')}`"
            :to-label="`${t('date')} ${t('to')}`"
            @update:from="updateFilter('date_from', $event)"
            @update:to="updateFilter('date_to', $event)"
        />
    </FilterPanel>
</template>
