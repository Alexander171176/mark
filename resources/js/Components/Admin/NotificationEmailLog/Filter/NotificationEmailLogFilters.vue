<script setup>
/**
 * @version PulsarCMS 1.0
 *
 * Фильтры журнала Email Notifications.
 *
 * Использует универсальные компоненты
 * административной панели.
 */

import { computed } from 'vue'
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

    events: {
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
| Возвращаем родителю новый объект.
|
*/

const updateFilter = (key, value) => {
    emits('update:modelValue', {
        ...props.modelValue,
        [key]: value,
    })
}

/*
|--------------------------------------------------------------------------
| Активные фильтры
|--------------------------------------------------------------------------
*/

const filterKeys = [
    'status',
    'event',
    'recipient',
    'uuid',
    'attempts',
    'date_from',
    'date_to',
]

const hasActiveFilters = computed(() => {
    return filterKeys.some((key) => {
        const value = props.modelValue?.[key]

        return value !== ''
            && value !== null
            && value !== undefined
    })
})
</script>

<template>
    <FilterPanel
        :title="t('filters')"
        :has-active-filters="hasActiveFilters"
        columns="xl:grid-cols-4"
        :apply-text="t('applyFilters')"
        :reset-text="t('clearFilters')"
        @apply="emits('apply')"
        @reset="emits('reset')"
    >
        <!-- Статус -->
        <FilterSelect
            :model-value="modelValue.status"
            :label="t('status')"
            :placeholder="t('allStatuses')"
            :options="statuses"
            @update:model-value="updateFilter('status', $event)"
        />

        <!-- Событие -->
        <FilterSelect
            :model-value="modelValue.event"
            :label="t('event')"
            :placeholder="t('allEvents')"
            :options="events"
            @update:model-value="updateFilter('event', $event)"
        />

        <!-- Получатель -->
        <FilterText
            :model-value="modelValue.recipient"
            :label="t('recipient')"
            placeholder="email@example.com"
            @update:model-value="updateFilter('recipient', $event)"
        />

        <!-- UUID -->
        <FilterText
            :model-value="modelValue.uuid"
            label="UUID"
            :placeholder="`${t('notification')} UUID`"
            @update:model-value="updateFilter('uuid', $event)"
        />

        <!-- Количество попыток -->
        <FilterNumber
            :model-value="modelValue.attempts"
            :label="t('limitCount')"
            :placeholder="t('limitCount')"
            :min="0"
            @update:model-value="updateFilter('attempts', $event)"
        />

        <!-- Период создания -->
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
