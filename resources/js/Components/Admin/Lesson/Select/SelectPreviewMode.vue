<script setup>
import { useI18n } from 'vue-i18n'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: String,
        default: 'none',
    },
    errorMessage: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])

const options = [
    { value: 'none',     label: 'previewNone' },
    { value: 'full',     label: 'previewFull' },
    { value: 'percent',  label: 'previewPercent' },
    { value: 'duration', label: 'previewDuration' },
    { value: 'chars',    label: 'previewChars' },
]

const onChange = (event) => {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <div class="flex flex-col items-start">
        <LabelInput for="preview_mode">
            {{ t('previewMode') }}
        </LabelInput>
        <select
            id="preview_mode"
            class="mt-1 py-0.5 border-slate-500 rounded-sm shadow-sm
                   font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300
                   dark:bg-cyan-800 dark:text-slate-100"
            :value="modelValue"
            @change="onChange"
        >
            <option
                v-for="opt in options"
                :key="opt.value"
                :value="opt.value"
            >
                {{ t(opt.label) || opt.value }}
            </option>
        </select>
        <InputError class="mt-1" :message="errorMessage" />
    </div>
</template>
