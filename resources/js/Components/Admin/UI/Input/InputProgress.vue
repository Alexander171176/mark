<script setup>
import { defineProps, defineEmits, ref, onMounted } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Number, String],
        required: true,
        default: 0
    }
})

const emit = defineEmits(['update:modelValue'])
const input = ref(null)

/**
 * Нормализуем в диапазон 0–100 + ограничиваем 3 символа
 */
const normalize = (value) => {
    if (value === '' || value === null || value === undefined) return ''

    // Обрезаем по символам, чтобы нельзя было ввести больше 3 знаков
    value = value.toString().slice(0, 3)

    let num = Number(value)

    if (Number.isNaN(num)) return ''

    if (num < 0) num = 0
    if (num > 100) num = 100

    return num
}

/**
 * Обрабатываем ввод
 */
const handleInput = (event) => {
    const raw = event.target.value

    const normalized = normalize(raw)
    emit('update:modelValue', normalized)

    // Обновляем физическое значение input (для визуального ограничения 3 символов)
    event.target.value = normalized
}

/**
 * На blur ещё раз нормализуем
 */
const handleBlur = () => {
    emit('update:modelValue', normalize(props.modelValue))
}

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus()
    }
})
</script>

<template>
    <input
        class="w-20 py-0.5 border-slate-500
               font-semibold text-sm
               focus:border-indigo-500 focus:ring-indigo-300
               rounded-sm shadow-sm
               dark:bg-cyan-800 dark:text-slate-100"
        :value="modelValue"
        @input="handleInput"
        @blur="handleBlur"
        ref="input"
        type="number"
        min="0"
        max="100"
        maxlength="3"
    />
</template>
