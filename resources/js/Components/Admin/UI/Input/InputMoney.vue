<!-- resources/js/Components/Admin/Input/InputMoney.vue -->
<script setup>
import { defineProps, defineEmits, ref, onMounted, computed } from 'vue'

const props = defineProps({
    modelValue: { type: [Number, String, null], default: '' },

    min: { type: Number, default: 0 },
    max: { type: [Number, null], default: null }, // ✅ по умолчанию нет ограничения

    step: { type: Number, default: 0.01 },
    fractionDigits: { type: Number, default: 2 },
})

const emit = defineEmits(['update:modelValue'])
const input = ref(null)

const displayValue = computed(() => {
    if (props.modelValue === null || props.modelValue === undefined) return ''
    return String(props.modelValue)
})

const normalize = (raw) => {
    if (raw === '' || raw === null || raw === undefined) return ''

    // разрешаем запятую
    raw = String(raw).replace(',', '.').trim()

    // если пользователь ввёл только "-" или ".", не ломаем ввод
    if (raw === '-' || raw === '.' || raw === '-.' ) return raw

    let num = parseFloat(raw)
    if (!Number.isFinite(num)) return ''

    if (props.min !== null && props.min !== undefined && num < props.min) {
        num = props.min
    }
    if (props.max !== null && props.max !== undefined && num > props.max) {
        num = props.max
    }

    const factor = Math.pow(10, props.fractionDigits)
    num = Math.round(num * factor) / factor

    return num
}

const handleInput = (e) => {
    const v = e.target.value
    if (v === '') {
        emit('update:modelValue', '')
        return
    }

    const normalized = normalize(v)

    // если normalized вернул строку (промежуточный ввод) — отдадим как есть
    emit('update:modelValue', normalized)
}

const handleBlur = () => {
    const normalized = normalize(displayValue.value)

    // если пользователь оставил промежуточное значение — чистим
    if (typeof normalized === 'string') {
        emit('update:modelValue', normalized === '' ? '' : Number(normalized) || '')
        return
    }

    emit('update:modelValue', normalized)
}

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})
</script>

<template>
    <input
        ref="input"
        type="number"
        inputmode="decimal"
        :step="step"
        :min="min"
        :max="max ?? undefined"
        :value="displayValue"
        @input="handleInput"
        @blur="handleBlur"
        v-bind="$attrs"
        class="w-28 py-0.5 border-slate-500
           font-semibold text-sm
           focus:border-indigo-500 focus:ring-indigo-300
           rounded-sm shadow-sm
           dark:bg-cyan-800 dark:text-slate-100"
    />
</template>
