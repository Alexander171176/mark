<script setup>
import { defineEmits, defineProps, onMounted, ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Number, String, null],
        default: '',
    },
})

const emit = defineEmits([
    'update:modelValue',
])

const input = ref(null)

/**
 * Обработка ввода.
 *
 * Пустое поле возвращает пустую строку.
 * Отрицательное значение преобразуется в 0.
 */
const handleInput = (event) => {
    const value = event.target.value

    if (value === '') {
        emit('update:modelValue', '')
        return
    }

    let numberValue = Number.parseInt(value, 10)

    if (Number.isNaN(numberValue)) {
        emit('update:modelValue', '')
        return
    }

    if (numberValue < 0) {
        numberValue = 0
    }

    emit('update:modelValue', numberValue)
}

/**
 * Проверка значения после потери фокуса.
 */
const handleBlur = () => {
    if (props.modelValue === null || props.modelValue === '') {
        return
    }

    const numberValue = Number(props.modelValue)

    if (Number.isFinite(numberValue) && numberValue < 0) {
        emit('update:modelValue', 0)
    }
}

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus()
    }
})
</script>

<template>
    <input
        ref="input"
        type="number"
        min="0"
        class="w-20 py-0.5 border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"
        :value="modelValue ?? ''"
        @input="handleInput"
        @blur="handleBlur"
    />
</template>
