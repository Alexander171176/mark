<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание Профиля обработки изображений (ImageProcessorProfile)
 */
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { useForm } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'

const toast = useToast()
const { t } = useI18n()

/** =============================================
 * Форма создания профиля
 * ============================================= */
const form = useForm({
    key: '',
    name: '',
    description: '',
    activity: true,
    sort: 0,
})

/** =============================================
 * Нормализация ключа профиля
 * латиница → snake_case
 * ============================================= */
const handleKeyInput = (event) => {
    form.key = event.target.value
        .toLowerCase()
        .replace(/[^a-z0-9_\-\s]/g, '') // разрешаем символы
        .replace(/[\s\-]+/g, '_')       // пробелы и дефисы → _
        .replace(/_+/g, '_')            // несколько _ → один
        .replace(/^_+|_+$/g, '')        // убрать _ по краям
}

/** =============================================
 * Отправка формы создания
 * ============================================= */
const submitForm = () => {
    form.transform((data) => ({
        ...data,
        activity: data.activity ? 1 : 0,
    }))

    form.post(route('admin.imageProcessorProfiles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Профиль обработки изображений успешно создан.')
        },
        onError: (errors) => {
            const firstError = errors[Object.keys(errors)[0]]
            toast.error(firstError || 'Проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('addImageProcessingProfile')">
        <template #header>
            <TitlePage>
                {{ t('addImageProcessingProfile') }}
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
                    <DefaultButton :href="route('admin.imageProcessorProfiles.index')">
                        <template #icon>
                            <!-- SVG -->
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"></path>
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" class="p-3 w-full">
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-3">
                        <div class="flex justify-between w-full">
                            <div class="flex flex-row items-center">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox for="activity" :text="t('activity')" />
                            </div>
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            </div>

                            <InputNumber
                                id="sort"
                                type="number"
                                v-model="form.sort"
                                autocomplete="sort"
                                class="w-full lg:w-28"
                            />

                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="key" value="Ключ профиля" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
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
                            autocomplete="key"
                            pattern="[a-z0-9_]+"
                            title="Только латинские буквы, цифры и нижнее подчёркивание"
                        />

                        <InputError class="mt-2" :message="form.errors.key" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="name" value="Название профиля" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.name.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>

                        <InputText
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            maxlength="255"
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="description" value="Описание профиля" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
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

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.imageProcessorProfiles.index')">
                            <template #icon>
                                <!-- SVG -->
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
