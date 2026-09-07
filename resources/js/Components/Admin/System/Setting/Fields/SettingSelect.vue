<script setup>
import {
    defineEmits,
    defineProps,
} from 'vue'

defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },

    id: {
        type: String,
        required: true,
    },

    options: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'update:modelValue',
])

/**
 * Обновление выбранного значения.
 */
const updateValue = (event) => {
    emit(
        'update:modelValue',
        event.target.value
    )
}
</script>

<template>
    <select
        :id="id"
        :value="modelValue"
        class="block w-fit px-2 py-1
               bg-slate-100 dark:bg-cyan-800
               border border-slate-500 dark:border-slate-100
               rounded-md shadow-sm font-semibold text-sm
               focus:outline-none focus:border-indigo-500
               focus:ring-indigo-300 dark:text-slate-100"
        @change="updateValue"
    >
        <option
            v-for="option in options"
            :key="option.value"
            :value="option.value"
        >
            {{ option.label }}
        </option>
    </select>
</template>
