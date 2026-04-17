<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Number, String],
        default: '',
    },
    id: {
        type: String,
        default: 'content_id',
    },
    min: {
        type: Number,
        default: 0,
    },
})

const emit = defineEmits(['update:modelValue'])

/**
 * Внутреннее значение:
 * - null/undefined -> ''
 * - число/строка -> как есть
 */
const internalValue = computed({
    get() {
        if (props.modelValue === null || typeof props.modelValue === 'undefined') {
            return ''
        }
        return props.modelValue
    },
    set(val) {
        // если поле очистили — храним пустую строку
        if (val === '' || val === null) {
            emit('update:modelValue', '')
            return
        }

        const num = Number(val)
        // если не число — тоже в ''
        if (Number.isNaN(num)) {
            emit('update:modelValue', '')
        } else {
            emit('update:modelValue', num)
        }
    },
})
</script>

<template>
    <input
        class="px-2 py-0.5 rounded-sm"
        v-bind="$attrs"
        :id="id"
        type="number"
        :min="min"
        v-model="internalValue"
    />
</template>
