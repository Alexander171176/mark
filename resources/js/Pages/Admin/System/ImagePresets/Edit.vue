<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование пресета обработки изображений
 */

import { computed, watch } from 'vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { useForm } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'

import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'

import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

const toast = useToast()
const { t } = useI18n()

const props = defineProps({
    preset: {
        type: Object,
        required: true,
    },
})

const presetData = props.preset?.data ?? props.preset

const shapeOptions = [
    { value: 'rectangle', label: t('shapeRectangle') },
    { value: 'square', label: t('shapeSquare') },
    { value: 'circle', label: t('shapeCircle') },
]

const form = useForm({
    key: presetData.key || '',
    description: presetData.description || '',

    shape: presetData.shape || 'rectangle',

    width: presetData.width ?? 1200,
    height: presetData.height ?? 800,

    image_rotation_enabled: !!presetData.image_rotation_enabled,
    crop_rotation_enabled: !!presetData.crop_rotation_enabled,

    max_file_size_kb: presetData.max_file_size_kb ?? 2048,

    keep_original: !!presetData.keep_original,

    sort: presetData.sort ?? 0,
})

const isSingleSizeShape = computed(() => {
    return ['square', 'circle'].includes(form.shape)
})

const maxFileSizeMb = computed(() => {
    return form.max_file_size_kb
        ? (Number(form.max_file_size_kb) / 1024).toFixed(2)
        : '0.00'
})

const resolutionPreview = computed(() => {
    return `${form.width || 0}×${form.height || 0}`
})

watch(
    () => form.shape,
    (shape) => {
        if (['square', 'circle'].includes(shape)) {
            form.height = form.width
            form.crop_rotation_enabled = false
        }
    }
)

watch(
    () => form.width,
    (width) => {
        if (isSingleSizeShape.value) {
            form.height = width
        }
    }
)

const handleKeyInput = (event) => {
    form.key = event.target.value
        .toLowerCase()
        .replace(/[^a-z0-9_\-\s]/g, '')
        .replace(/[\s\-]+/g, '_')
        .replace(/_+/g, '_')
        .replace(/^_+|_+$/g, '')
}

const submitForm = () => {
    form.transform((data) => ({
        key: data.key,
        description: data.description,

        shape: data.shape,

        width: data.width,
        height: ['square', 'circle'].includes(data.shape)
            ? data.width
            : data.height,

        image_rotation_enabled: data.image_rotation_enabled ? 1 : 0,
        crop_rotation_enabled: data.shape === 'rectangle' && data.crop_rotation_enabled ? 1 : 0,

        max_file_size_kb: data.max_file_size_kb,

        keep_original: data.keep_original ? 1 : 0,

        sort: data.sort,
    }))

    form.put(route('admin.imagePresets.update', {
        imagePreset: presetData.id,
    }), {
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Пресет обработки изображений успешно обновлён.')
        },

        onError: (errors) => {
            const firstError = errors[Object.keys(errors)[0]]
            toast.error(firstError || 'Проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editImagePreset')">
        <template #header>
            <TitlePage>
                {{ t('editImagePreset') }}
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.imagePresets.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"></path>
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" class="p-3 w-full">
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.image_rotation_enabled" />
                                <LabelCheckbox
                                    for="image_rotation_enabled"
                                    :text="t('imageRotationEnabled')"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox
                                    v-model="form.crop_rotation_enabled"
                                    :disabled="isSingleSizeShape"
                                />
                                <LabelCheckbox
                                    for="crop_rotation_enabled"
                                    :text="t('cropRotationEnabled')"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.keep_original" />
                                <LabelCheckbox
                                    for="keep_original"
                                    :text="t('keepOriginal')"
                                />
                            </div>
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />

                            <InputNumber
                                id="sort"
                                type="number"
                                v-model="form.sort"
                                min="0"
                                class="w-full lg:w-28"
                            />

                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput for="key">
                                    <span class="text-red-500 dark:text-red-300 font-semibold">
                                        *
                                    </span>
                                    {{ t('key') }}
                                </LabelInput>

                                <div class="text-sm text-gray-900 dark:text-gray-400 mt-1">
                                    {{ form.key.length }} / 100 {{ t('characters') }}
                                </div>
                            </div>

                            <InputText
                                id="key"
                                type="text"
                                v-model="form.key"
                                @input="handleKeyInput"
                                required
                                maxlength="100"
                                pattern="[a-z0-9_]+"
                                :title="t('onlyText')"
                            />

                            <InputError class="mt-2" :message="form.errors.key" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="shape">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('shape') }}
                            </LabelInput>

                            <select
                                id="shape"
                                v-model="form.shape"
                                required
                                class="w-full form-select rounded-sm py-0.5
                                       bg-white dark:bg-cyan-800 text-sm
                                       text-slate-700 dark:text-slate-100
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option
                                    v-for="option in shapeOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.shape" />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="description" :value="t('description')" />

                            <div class="text-sm text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.description.length }} / 500 {{ t('characters') }}
                            </div>
                        </div>

                        <MetaDescTextarea
                            id="description"
                            v-model="form.description"
                            class="w-full"
                        />

                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div>
                            <LabelInput
                                for="width"
                                :value="isSingleSizeShape ? t('size') : t('width')"
                            />

                            <InputNumber
                                id="width"
                                type="number"
                                v-model="form.width"
                                min="1"
                                max="10000"
                            />

                            <InputError class="mt-2" :message="form.errors.width" />
                        </div>

                        <div v-if="!isSingleSizeShape">
                            <LabelInput for="height" :value="t('height')" />

                            <InputNumber
                                id="height"
                                type="number"
                                v-model="form.height"
                                min="1"
                                max="10000"
                            />

                            <InputError class="mt-2" :message="form.errors.height" />
                        </div>

                        <div>
                            <LabelInput for="max_file_size_kb" :value="t('maxFileSizeKb')" />

                            <InputNumber
                                id="max_file_size_kb"
                                type="number"
                                v-model="form.max_file_size_kb"
                                min="128"
                                max="51200"
                            />

                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-300">
                                {{ maxFileSizeMb }} MB
                            </div>

                            <InputError class="mt-2" :message="form.errors.max_file_size_kb" />
                        </div>
                    </div>

                    <div
                        class="mb-3 p-3 rounded-sm border border-slate-300 dark:border-slate-600
                               bg-white dark:bg-slate-800"
                    >
                        <div class="text-sm font-semibold text-slate-700 dark:text-slate-100">
                            {{ t('view') }}
                        </div>

                        <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                            {{ t('shape') }}:
                            <span class="font-semibold">{{ form.shape }}</span>
                        </div>

                        <div class="text-sm text-slate-600 dark:text-slate-300">
                            {{ t('typeSize') }}:
                            <span class="font-semibold">{{ resolutionPreview }}</span>
                        </div>

                        <div class="text-sm text-slate-600 dark:text-slate-300">
                            {{ t('file') }}:
                            <span class="font-semibold">
                                {{ form.max_file_size_kb }} KB / {{ maxFileSizeMb }} MB
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.imagePresets.index')">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"></path>
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
