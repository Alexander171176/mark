<script setup>
import { defineProps, defineEmits } from 'vue'

defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },

    label: {
        type: String,
        default: '',
    },

    placeholder: {
        type: String,
        default: '',
    },

    min: {
        type: [String, Number],
        default: null,
    },

    max: {
        type: [String, Number],
        default: null,
    },

    step: {
        type: [String, Number],
        default: 1,
    },

    disabled: {
        type: Boolean,
        default: false,
    },
})

const emits = defineEmits([
    'update:modelValue',
])

const updateValue = (event) => {
    emits(
        'update:modelValue',
        event.target.value
    )
}
</script>

<template>
    <div>
        <label
            v-if="label"
            class="mb-1 block text-sm font-medium
                   text-indigo-700 dark:text-indigo-300"
        >
            {{ label }}
        </label>

        <input
            type="number"
            :value="modelValue"
            :placeholder="placeholder"
            :min="min"
            :max="max"
            :step="step"
            :disabled="disabled"
            class="px-3 py-1 w-full rounded-sm
                   border-gray-400 text-sm shadow-sm
                   dark:border-gray-500
                   dark:bg-gray-700
                   dark:text-gray-100
                   disabled:cursor-not-allowed
                   disabled:opacity-60"
            @input="updateValue"
        >
    </div>
</template>
