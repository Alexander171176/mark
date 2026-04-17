<!-- /resources/js/Components/Admin/Input/InputDecimal.vue -->
<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: { type: [Number, String, null], default: null },
    min: { type: [Number, String], default: null },
    max: { type: [Number, String], default: null },
    step: { type: [Number, String], default: '0.01' },
    fractionDigits: { type: Number, default: 2 }
})

const emit = defineEmits(['update:modelValue'])

const displayValue = computed({
    get() {
        if (props.modelValue === null || props.modelValue === '') return ''
        const n = Number(props.modelValue)
        return Number.isFinite(n) ? n.toFixed(props.fractionDigits) : String(props.modelValue)
    },
    set(v) {
        if (v === '' || v === null) {
            emit('update:modelValue', null)
            return
        }
        const n = Number(v)
        emit('update:modelValue', Number.isFinite(n) ? n : v)
    }
})

const onBlur = () => {
    if (displayValue.value === '' || displayValue.value === null) return
    const n = Number(displayValue.value)
    if (!Number.isFinite(n)) return
    let clamped = n
    if (props.min !== null && props.min !== undefined) clamped = Math.max(clamped, Number(props.min))
    if (props.max !== null && props.max !== undefined) clamped = Math.min(clamped, Number(props.max))
    emit('update:modelValue', Number(clamped.toFixed(props.fractionDigits)))
}
</script>

<template>
    <input
        type="number"
        class="py-0.5 border-slate-500 font-semibold text-sm
               focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm
               dark:bg-cyan-800 dark:text-slate-100 w-full lg:w-28"
        v-model="displayValue"
        :min="min ?? undefined"
        :max="max ?? undefined"
        :step="step"
        @blur="onBlur"
    />
</template>
