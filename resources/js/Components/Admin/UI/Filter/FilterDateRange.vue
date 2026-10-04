<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import FilterDate from '@/Components/Admin/UI/Filter/FilterDate.vue'

const { t } = useI18n()

const props = defineProps({
    from: {
        type: String,
        default: '',
    },

    to: {
        type: String,
        default: '',
    },

    fromLabel: {
        type: String,
        default: '',
    },

    toLabel: {
        type: String,
        default: '',
    },

    min: {
        type: String,
        default: '',
    },

    max: {
        type: String,
        default: '',
    },

    disabled: {
        type: Boolean,
        default: false,
    },
})

const emits = defineEmits([
    'update:from',
    'update:to',
])

const resolvedFromLabel = computed(() => {
    return props.fromLabel || `${t('date')} ${t('from')}`
})

const resolvedToLabel = computed(() => {
    return props.toLabel || `${t('date')} ${t('to')}`
})
</script>

<template>
    <FilterDate
        :model-value="from"
        :label="resolvedFromLabel"
        :min="min"
        :max="to || max"
        :disabled="disabled"
        @update:model-value="emits('update:from', $event)"
    />

    <FilterDate
        :model-value="to"
        :label="resolvedToLabel"
        :min="from || min"
        :max="max"
        :disabled="disabled"
        @update:model-value="emits('update:to', $event)"
    />
</template>
