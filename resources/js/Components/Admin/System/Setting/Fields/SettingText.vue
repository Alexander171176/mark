<script setup>
import {
    defineEmits,
    defineProps,
} from 'vue'

import InputText from '@/Components/Admin/System/Setting/Input/InputText.vue'

defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },

    id: {
        type: String,
        required: true,
    },
})

const emit = defineEmits([
    'update:modelValue',
])

/**
 * Обновление строкового значения.
 */
const updateValue = (value) => {
    emit(
        'update:modelValue',
        value
    )
}
</script>

<template>
    <InputText
        :id="id"
        :model-value="modelValue"
        type="text"
        autocomplete="off"
        @update:model-value="updateValue"
    />
</template>
