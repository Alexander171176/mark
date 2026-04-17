<script setup>
import { useI18n } from 'vue-i18n'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    errorMessage: {
        type: String,
        default: '',
    },
})

const emits = defineEmits(['update:modelValue'])

// 🔹 Мапа "значение из БД" → "ключ перевода"
const quizTypeLabelKeyMap = {
    graded: 'quizTypeGraded',     // graded — с оценкой
    practice: 'quizTypePractice', // practice — тренировочный
}

// 🔹 Опции селекта
const quizTypeOptions = [
    { value: '',       labelKey: 'notSelected' },     // placeholder
    { value: 'graded', labelKey: 'quizTypeGraded' },
    { value: 'practice', labelKey: 'quizTypePractice' },
]

const handleChange = (event) => {
    emits('update:modelValue', event.target.value)
}

const getOptionLabel = (option) => {
    return t(option.labelKey)
}
</script>

<template>
    <div class="flex flex-col items-start">
        <LabelInput for="quiz_type">
            {{ t('type') }}
        </LabelInput>

        <select
            id="quiz_type"
            class="block w-full py-0.5 border-slate-500 text-md rounded-sm shadow-sm
                   focus:border-indigo-500 focus:ring-indigo-300
                   dark:bg-cyan-800 dark:text-slate-100"
            :value="modelValue"
            @change="handleChange"
        >
            <option
                v-for="opt in quizTypeOptions"
                :key="opt.value || 'empty'"
                :value="opt.value"
            >
                {{ getOptionLabel(opt) }}
            </option>
        </select>

        <InputError
            v-if="errorMessage"
            class="mt-1"
            :message="errorMessage"
        />
    </div>
</template>
