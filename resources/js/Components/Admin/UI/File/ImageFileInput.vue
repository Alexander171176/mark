<script setup>
import { ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'

import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: [File, String, null],
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

const emit = defineEmits(['update:modelValue', 'clear'])

const fileInput = ref(null)
const preview = ref(null)

const labelText = computed(() => props.label ?? t('image'))
const buttonLabel = computed(() => props.buttonText ?? t('selectImage'))
const emptyLabel = computed(() => props.emptyText ?? t('noImage'))

watch(
    () => props.modelValue,
    (value) => {
        if (typeof value === 'string' && value) {
            preview.value = value
            return
        }

        if (!value) {
            preview.value = null
        }
    },
    { immediate: true }
)

const selectFile = () => {
    fileInput.value?.click()
}

const updatePreview = () => {
    const file = fileInput.value?.files?.[0]

    if (!file) return

    emit('update:modelValue', file)

    const reader = new FileReader()

    reader.onload = (e) => {
        preview.value = e.target.result
    }

    reader.readAsDataURL(file)
}

const clearFile = () => {
    emit('update:modelValue', null)
    emit('clear')

    preview.value = null

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
            <template #icon>
                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                     viewBox="0 0 384 512">
                    <path d="M384 121.941V128H256V0h6.059a24 24 0 0 1 16.97 7.029l97.941 97.941a24.002 24.002 0 0 1 7.03 16.971zM248 160c-13.2 0-24-10.8-24-24V0H24C10.745 0 0 10.745 0 24v464c0 13.255 10.745 24 24 24h336c13.255 0 24-10.745 24-24V160H248zm-135.455 16c26.51 0 48 21.49 48 48s-21.49 48-48 48-48-21.49-48-48 21.491-48 48-48zm208 240h-256l.485-48.485L104.545 328c4.686-4.686 11.799-4.201 16.485.485L160.545 368 264.06 264.485c4.686-4.686 12.284-4.686 16.971 0L320.545 304v112z" />
                </svg>
            </template>
            {{ buttonLabel }}
        </PrimaryButton>

        <button
            v-if="preview"
            type="button"
            class="flex flex-row items-center justify-center gap-1 font-semibold
                   mt-2 text-sm text-red-700 dark:text-red-300 hover:underline"
            @click.prevent="clearFile"
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
