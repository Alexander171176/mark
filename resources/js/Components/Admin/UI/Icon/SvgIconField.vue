<script setup>
import { ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'

import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: { type: [String, null], default: '' },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    rows: { type: Number, default: 7 },
})

const emit = defineEmits(['update:modelValue'])

const localValue = ref('')

watch(
    () => props.modelValue,
    (value) => {
        localValue.value = value || ''
    },
    { immediate: true }
)

watch(localValue, (value) => {
    emit('update:modelValue', value)
})

const previewHtml = computed(() => localValue.value.trim())

const hasSvg = computed(() => {
    return previewHtml.value.includes('<svg')
        && previewHtml.value.includes('</svg>')
})
</script>

<template>
    <div class="mb-3 flex flex-col items-start w-full">
        <LabelInput for="icon" :value="label || t('svg')" />

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 w-full">
            <div class="lg:col-span-3">
                <textarea
                    id="icon"
                    v-model="localValue"
                    :rows="rows"
                    class="w-full px-3 py-2 bg-white dark:bg-slate-900
                           text-slate-700 dark:text-slate-100
                           border border-slate-400 dark:border-slate-600
                           rounded-sm shadow-sm font-mono text-xs"
                    placeholder="<svg ...>...</svg>"
                ></textarea>

                <InputError class="mt-2" :message="error" />
            </div>

            <div
                class="flex flex-col items-center justify-center min-h-36
                       border border-dashed border-slate-400 dark:border-slate-500
                       rounded-sm bg-white dark:bg-slate-900 p-3"
            >
                <div class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">
                    {{ t('view') }}
                </div>

                <div
                    v-if="hasSvg"
                    v-html="previewHtml"
                    class="svg-icon-preview flex items-center justify-center
                           w-20 h-20 text-slate-700 dark:text-slate-100"
                />

                <div
                    v-else
                    class="flex items-center justify-center w-20 h-20
                           rounded-sm border border-slate-300 dark:border-slate-600
                           text-xs text-slate-400 dark:text-slate-500"
                >
                    SVG
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.svg-icon-preview :deep(svg) {
    width: 100%;
    height: 100%;
    max-width: 5rem;
    max-height: 5rem;
    fill: currentColor;
}
</style>
