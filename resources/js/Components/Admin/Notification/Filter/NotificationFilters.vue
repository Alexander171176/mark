<script setup>
import { computed, defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

import FilterPanel from '@/Components/Admin/UI/Filter/FilterPanel.vue'
import FilterSelect from '@/Components/Admin/UI/Filter/FilterSelect.vue'
import FilterDateRange from '@/Components/Admin/UI/Filter/FilterDateRange.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({}),
    },
})

const emits = defineEmits([
    'update:modelValue',
    'apply',
    'reset',
])

/*
|--------------------------------------------------------------------------
| Варианты фильтров
|--------------------------------------------------------------------------
*/

/**
 * Состояние прочтения.
 *
 * Пустое значение означает
 * показ всех уведомлений.
 */
const readStatusOptions = computed(() => [
    {
        value: 'unread',
        label: 'Непрочитанные',
    },
    {
        value: 'read',
        label: 'Прочитанные',
    },
])

/**
 * Категории внутренних уведомлений PulsarCMS.
 *
 * Значения соответствуют универсальному
 * контракту PulsarNotification.
 *
 * Список можно расширять по мере появления
 * новых модулей системы.
 */
const categoryOptions = computed(() => [
    {
        value: 'system',
        label: 'Система',
    },
    {
        value: 'form',
        label: 'Формы',
    },
    {
        value: 'blog',
        label: 'Блог',
    },
    {
        value: 'comment',
        label: 'Комментарии',
    },
    {
        value: 'review',
        label: 'Отзывы',
    },
    {
        value: 'market',
        label: 'Маркетплейс',
    },
    {
        value: 'school',
        label: 'Школа',
    },
    {
        value: 'crm',
        label: 'CRM',
    },
])

/**
 * Уровни уведомлений.
 *
 * Соответствуют универсальным уровням
 * PulsarNotification.
 */
const levelOptions = computed(() => [
    {
        value: 'info',
        label: 'Информация',
    },
    {
        value: 'success',
        label: 'Успешно',
    },
    {
        value: 'warning',
        label: 'Предупреждение',
    },
    {
        value: 'error',
        label: 'Ошибка',
    },
])

/**
 * Принадлежность уведомлений.
 *
 * Фильтр фактически применяется backend
 * только для пользователя с ролью admin.
 *
 * Обычный пользователь через baseQuery()
 * всегда видит только собственные уведомления.
 */
const ownershipOptions = computed(() => [
    {
        value: 'mine',
        label: 'Мои',
    },
    {
        value: 'others',
        label: 'Других пользователей',
    },
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
        columns="xl:grid-cols-4"
        :apply-text="t('applyFilters')"
        :reset-text="t('clearFilters')"
        @apply="emits('apply')"
        @reset="emits('reset')"
    >
        <!-- Состояние прочтения -->
        <FilterSelect
            :model-value="modelValue.read_status"
            label="Состояние"
            placeholder="Все состояния"
            :options="readStatusOptions"
            @update:model-value="updateFilter('read_status', $event)"
        />

        <!-- Категория -->
        <FilterSelect
            :model-value="modelValue.category"
            label="Категория"
            placeholder="Все категории"
            :options="categoryOptions"
            @update:model-value="updateFilter('category', $event)"
        />

        <!-- Уровень -->
        <FilterSelect
            :model-value="modelValue.level"
            label="Уровень"
            placeholder="Все уровни"
            :options="levelOptions"
            @update:model-value="updateFilter('level', $event)"
        />

        <!-- Принадлежность -->
        <FilterSelect
            :model-value="modelValue.ownership"
            label="Принадлежность"
            placeholder="Все уведомления"
            :options="ownershipOptions"
            @update:model-value="updateFilter('ownership', $event)"
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
