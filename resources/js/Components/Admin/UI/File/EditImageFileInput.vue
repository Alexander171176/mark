<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: [File, null],
        default: null,
    },
    initialPreview: {
        type: String,
        default: null,
    },
    label: {
        type: String,
        default: null,
    },
    buttonText: {
        type: String,
        default: null,
    },
    emptyText: {
        type: String,
        default: null,
    },
    error: {
        type: String,
        default: '',
    },
    accept: {
        type: String,
        default: 'image/png,image/jpeg,image/webp',
    },
    previewClass: {
        type: String,
        default: 'h-24 w-36 object-cover rounded-sm border border-slate-400',
    },
    placeholderClass: {
        type: String,
        default:
            'h-24 w-36 flex items-center justify-center ' +
            'rounded-sm border border-dashed border-slate-400 text-xs text-slate-500',
    },
})

const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const newPreview = ref(null)

const labelText = computed(() => props.label ?? t('image'))
const buttonLabel = computed(() => props.buttonText ?? t('selectImage'))
const emptyLabel = computed(() => props.emptyText ?? t('noImage'))

const preview = computed(() => {
    return newPreview.value || props.initialPreview || null
})

const selectFile = () => {
    fileInput.value?.click()
}

const updatePreview = () => {
    const file = fileInput.value?.files?.[0]

    if (!file) return

    emit('update:modelValue', file)

    const reader = new FileReader()

    reader.onload = (e) => {
        newPreview.value = e.target.result
    }

    reader.readAsDataURL(file)
}

const clearNewFile = () => {
    emit('update:modelValue', null)
    newPreview.value = null

    if (fileInput.value) {
        fileInput.value.value = null
    }
}
</script>

<template>
    <div class="flex flex-col items-center">
        <input
            ref="fileInput"
            type="file"
            class="hidden"
            :accept="accept"
            @change="updatePreview"
        />

        <LabelInput :value="labelText" />

        <div class="mt-2">
            <img
                v-if="preview"
                :src="preview"
                :alt="labelText"
                :class="previewClass"
            />

            <div v-else :class="placeholderClass">
                {{ emptyLabel }}
            </div>
        </div>

        <PrimaryButton
            class="mt-2 text-xs py-1 rounded-sm"
            type="button"
            @click.prevent="selectFile"
        >
            {{ buttonLabel }}
        </PrimaryButton>

        <button
            v-if="newPreview"
            type="button"
            class="flex flex-row items-center justify-center gap-1 font-semibold
                   mt-2 text-sm text-red-700 dark:text-red-300 hover:underline"
            @click.prevent="clearNewFile"
        >
            <svg class="w-4 h-4 fill-current text-red-700 dark:text-red-300 shrink-0 mr-2"
                 viewBox="0 0 352 512">
                <path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z" />
            </svg>
            {{ t('delete') }}
        </button>

        <InputError class="mt-2" :message="error" />
    </div>
</template>
