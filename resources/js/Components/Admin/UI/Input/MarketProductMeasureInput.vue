<script setup>
import { ref, watch, useAttrs } from 'vue'

defineOptions({
    inheritAttrs: false,
})

const attrs = useAttrs()

const props = defineProps({
    modelValue: {
        type: [Number, String, null],
        default: '',
    },
    min: {
        type: [Number, String],
        default: 0,
    },
    max: {
        type: [Number, String, null],
        default: null,
    },
    fractionDigits: {
        type: Number,
        default: 2,
    },
    unit: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '0',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus'])

const inputValue = ref('')

const normalizeIncomingValue = (value) => {
    if (value === null || value === undefined || value === '') return ''

    return String(value).replace(',', '.')
}

watch(() => props.modelValue, (value) => {
    const normalized = normalizeIncomingValue(value)

    if (normalized !== inputValue.value.replace(',', '.')) {
        inputValue.value = normalized
    }
}, { immediate: true })

const sanitizeValue = (value) => {
    let result = String(value ?? '').replace(',', '.').replace(/[^\d.]/g, '')

    const firstDot = result.indexOf('.')

    if (firstDot !== -1) {
        result = result.slice(0, firstDot + 1) + result.slice(firstDot + 1).replace(/\./g, '')
    }

    const [integerPart = '', decimalPart = ''] = result.split('.')
    const normalizedInteger = integerPart.replace(/^0+(?=\d)/, '')
    const limitedDecimal = decimalPart.slice(0, props.fractionDigits)

    return firstDot !== -1 ? `${normalizedInteger || '0'}.${limitedDecimal}` : normalizedInteger
}

const handleInput = (event) => {
    const sanitized = sanitizeValue(event.target.value)

    inputValue.value = sanitized
    event.target.value = sanitized

    if (sanitized === '') {
        emit('update:modelValue', '')
        return
    }

    const number = Number(sanitized)

    emit('update:modelValue', Number.isFinite(number) ? number : '')
}

const handleBlur = (event) => {
    if (inputValue.value === '') {
        emit('blur', event)
        return
    }

    let number = Number(inputValue.value)

    if (!Number.isFinite(number)) {
        inputValue.value = ''
        emit('update:modelValue', '')
        emit('blur', event)
        return
    }

    number = Math.max(Number(props.min), number)

    if (props.max !== null && props.max !== '') {
        number = Math.min(Number(props.max), number)
    }

    inputValue.value = number.toFixed(props.fractionDigits)
    emit('update:modelValue', number)
    emit('blur', event)
}
</script>

<template>
    <div class="relative inline-flex w-full items-center">
        <input
            v-bind="attrs"
            type="text"
            inputmode="decimal"
            :value="inputValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :class="[
             'w-full px-3 py-0.5 rounded-sm bg-white dark:bg-cyan-800 ' +
             'border border-slate-500 dark:border-slate-400 ' +
             'focus:border-blue-500 focus:ring-1 focus:ring-blue-500 ' +
             'text-slate-700 dark:text-slate-100 font-semibold ' +
             'disabled:cursor-not-allowed disabled:opacity-60 ' +
             'shadow-sm outline-none transition',
                unit ? 'pr-12' : '',
            ]"
            @input="handleInput"
            @blur="handleBlur"
            @focus="$emit('focus', $event)"
        >

        <span v-if="unit"
              class="pointer-events-none absolute right-3 font-semibold text-sm
                     text-slate-500 dark:text-slate-300">
            {{ unit }}
        </span>
    </div>
</template>
