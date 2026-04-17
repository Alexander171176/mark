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

const emit = defineEmits(['update:modelValue'])

const updateValue = (event) => {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <div class="flex flex-col items-start">
        <LabelInput for="grading_type" :value="t('gradingType')" />
        <select
            id="grading_type"
            :value="modelValue"
            @change="updateValue"
            class="block w-full py-0.5 text-md rounded-sm
                   border-slate-500 focus:border-indigo-500 focus:ring-indigo-300
                   shadow-sm dark:bg-cyan-800 dark:text-slate-100">

            <option value="">{{ t('select') }}</option>
            <option value="manual">{{ t('gradingManual') }}</option>
            <option value="auto">{{ t('gradingAuto') }}</option>
        </select>
        <InputError class="mt-1" :message="errorMessage" />
    </div>
</template>
