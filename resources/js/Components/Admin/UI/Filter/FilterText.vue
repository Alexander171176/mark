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

    disabled: {
        type: Boolean,
        default: false,
    },

    readonly: {
        type: Boolean,
        default: false,
    },

    autocomplete: {
        type: String,
        default: 'off',
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
            type="text"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            :autocomplete="autocomplete"
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
