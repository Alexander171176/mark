<script setup>
// под рейтинг, баллы (0-5)
import { defineProps, defineEmits, ref, onMounted, computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Number, String, null],
        default: null,
    },
    min: {
        type: Number,
        default: 0,
    },
    max: {
        type: Number,
        default: 5,
    },
    step: {
        type: Number,
        default: 0.01,
    },
    fractionDigits: {
        type: Number,
        default: 2,
    },
})

const emit = defineEmits(['update:modelValue'])

const input = ref(null)

const displayValue = computed(() => {
    if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') {
        return ''
    }
    return props.modelValue.toString()
})

const normalizeNumber = (raw) => {
    if (raw === '' || raw === null || raw === undefined) return null

    // разрешим и запятую, и точку
    raw = String(raw).replace(',', '.')

    let num = parseFloat(raw)
    if (isNaN(num)) return null

    // min / max
    if (props.min !== null && props.min !== undefined && num < props.min) {
        num = props.min
    }
    if (props.max !== null && props.max !== undefined && num > props.max) {
        num = props.max
    }

    // округляем до fractionDigits
    const factor = Math.pow(10, props.fractionDigits)
    num = Math.round(num * factor) / factor

    return num
}

const handleInput = (event) => {
    let value = event.target.value

    if (value === '') {
        emit('update:modelValue', '')
        return
    }

    const num = normalizeNumber(value)

    // Если не число — оставляем пустым
    if (num === null) {
        emit('update:modelValue', '')
    } else {
        emit('update:modelValue', num)
    }
}

const handleBlur = () => {
    const num = normalizeNumber(displayValue.value)

    if (num === null) {
        emit('update:modelValue', '')
        return
    }

    emit('update:modelValue', num)
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
        :max="max"
        :value="displayValue"
        @input="handleInput"
        @blur="handleBlur"
        v-bind="$attrs"
        class="w-20 py-0.5 border-slate-500
               font-semibold text-sm
               focus:border-indigo-500 focus:ring-indigo-300
               rounded-sm shadow-sm
               dark:bg-cyan-800 dark:text-slate-100"
    />
</template>
