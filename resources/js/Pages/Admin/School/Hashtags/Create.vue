<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Создание хештега (Hashtag)
 */
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import MetatagsButton from '@/Components/Admin/Buttons/MetatagsButton.vue'
import { computed } from 'vue'

// --- Инициализация ---
const { t } = useI18n()
const toast = useToast()

/** Форма создания хештега */
const form = useForm({
    activity: true,
    sort: 0,
    locale: '', // en, kk, ru
    name: '',
    slug: '',
    color: '#FF6600',
    short: '', // Краткое описание
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

/** Валидатор инпута цвета */
const isValidHexColor = (value) => /^#([0-9A-Fa-f]{6})$/.test(value || '')

/** Инпут цвета */
const colorForPicker = computed({
    get() {
        // если form.color валиден — отдаём его, иначе дефолт
        return isValidHexColor(form.color) ? form.color : '#FF6600'
    },
    set(value) {
        // color-пикер всегда отдаёт валидный #rrggbb → пишем как есть
        form.color = value
    },
})

/** Автогенерация slug по фокусу */
const handleSlugFocus = () => {
    if (!form.slug && form.name) {
        form.slug = transliterate(form.name.toLowerCase())
    }
}

/** Обрезка текста до заданной длины */
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''
    if (text.length <= maxLength) return text
    const truncated = text.substr(0, text.lastIndexOf(' ', maxLength))
    return addEllipsis ? `${truncated}...` : truncated
}

/** Генерация meta-полей, если не заданы вручную. */
const generateMetaFields = () => {
    // meta_title
    if (form.name && !form.meta_title) {
        form.meta_title = truncateText(form.name, 160)
    }

    // meta_keywords из short
    if (!form.meta_keywords && form.short) {
        // 1. убираем HTML
        let text = form.short.replace(/(<([^>]+)>)/gi, '')

        // 2. убираем пунктуацию
        text = text.replace(/[.,!?;:()\[\]{}"'«»]/g, '')

        // 3. разбиваем на слова
        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        const keywords = words.join(', ')
        form.meta_keywords = truncateText(keywords, 255)
    }

    // meta_desc из short
    if (form.short && !form.meta_desc) {
        const descText = form.short.replace(/(<([^>]+)>)/gi, '')
        form.meta_desc = truncateText(descText, 255, true)
    }
}

/** Отправка формы создания тега. */
const submit = () => {

    form.transform((data) => {
        const toNum = (val, digits = 2) => {
            if (val === '' || val === null || typeof val === 'undefined') return null
            const n = Number(val)
            return Number.isFinite(n) ? Number(n.toFixed(digits)) : null
        }

        return {
            ...data,
            activity: data.activity ? 1 : 0,
        }
    })

    form.post(route('admin.hashtags.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Тег успешно создан!')
        },
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createTag')">
        <template #header>
            <TitlePage>{{ t('createTag') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.hashtags.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                    <div class="grid grid-flow-col
                                sm:auto-cols-max justify-start sm:justify-end gap-2"
                    />
                </div>

                <form @submit.prevent="submit" class="p-3 w-full">

                    <div class="pb-12">

                        <div class="mb-3 flex justify-between flex-col
                                    lg:flex-row items-center gap-4">

                            <!-- Активность -->
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <!-- Локализация -->
                            <div class="flex flex-row items-center w-auto">
                                <SelectLocale
                                    v-model="form.locale"
                                    :errorMessage="form.errors.locale"
                                />
                                <InputError
                                    class="mt-2 lg:mt-0"
                                    :message="form.errors.locale"
                                />
                            </div>

                            <!-- Сортировка -->
                            <div class="flex flex-row items-center gap-2">
                                <div class="h-8 flex items-center">
                                    <LabelInput
                                        for="sort"
                                        :value="t('sort')"
                                        class="text-sm"
                                    />
                                </div>
                                <InputNumber
                                    id="sort"
                                    type="number"
                                    v-model.number="form.sort"
                                    class="w-full lg:w-28"
                                />
                                <InputError
                                    class="mt-2 lg:mt-0"
                                    :message="form.errors.sort"
                                />
                            </div>
                        </div>

                        <!-- Название -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="name">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('name') }}
                            </LabelInput>
                            <InputText
                                id="name"
                                type="text"
                                v-model="form.name"
                                required
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Slug: автогенерация по фокусу -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="slug">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('slug') }}
                            </LabelInput>
                            <InputText
                                id="slug"
                                type="text"
                                v-model="form.slug"
                                autocomplete="off"
                                class="w-full"
                                @focus="handleSlugFocus"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

                        <!-- Цвет лейбла тега -->
                        <!-- Цвет лейбла тега -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="color">
                                {{ t('colorLabelTag') }}
                            </LabelInput>

                            <div class="flex items-center gap-3 w-full">
                                <!-- Поле для HEX-кода -->
                                <InputText
                                    id="color_text"
                                    type="text"
                                    v-model="form.color"
                                    placeholder="#FF6600"
                                    autocomplete="off"
                                    class="w-full"
                                    maxlength="7"
                                />

                                <!-- Цветовой пикер, привязан к ОТФИЛЬТРОВАННОМУ значению -->
                                <input
                                    id="color"
                                    type="color"
                                    v-model="colorForPicker"
                                    class="h-9 w-16 rounded bg-transparent cursor-pointer"
                                />
                            </div>

                            <InputError class="mt-2" :message="form.errors.color" />
                        </div>

                        <!-- Краткое описание -->
                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="short"
                                    :value="t('shortDescription')"
                                />
                                <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                    {{ form.short.length }} / 255 {{ t('characters') }}
                                </div>
                            </div>
                            <MetaDescTextarea v-model="form.short" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.short" />
                        </div>

                        <!-- Описание -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="description" :value="t('description')" />
                            <TinyEditor v-model="form.description" :height="500" />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <!-- Мета title -->
                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="meta_title"
                                    :value="t('metaTitle')"
                                />
                                <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                    {{ form.meta_title.length }} / 160
                                    {{ t('characters') }}
                                </div>
                            </div>
                            <InputText
                                id="meta_title"
                                type="text"
                                v-model="form.meta_title"
                                maxlength="160"
                                autocomplete="url"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.meta_title"
                            />
                        </div>

                        <!-- Мета keywords -->
                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="meta_keywords"
                                    :value="t('metaKeywords')"
                                />
                                <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                    {{ form.meta_keywords.length }} / 255
                                    {{ t('characters') }}
                                </div>
                            </div>
                            <InputText
                                id="meta_keywords"
                                type="text"
                                v-model="form.meta_keywords"
                                maxlength="255"
                                autocomplete="meta_keywords"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.meta_keywords"
                            />
                        </div>

                        <!-- Мета Description -->
                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="meta_desc"
                                    :value="t('metaDescription')"
                                />
                                <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                    {{ form.meta_desc.length }} / 255
                                    {{ t('characters') }}
                                </div>
                            </div>
                            <MetaDescTextarea
                                v-model="form.meta_desc"
                                maxlength="255"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.meta_desc"
                            />
                        </div>

                        <!-- Кнопка генерации метатегов -->
                        <div class="flex justify-end mt-4">
                            <MetatagsButton @click.prevent="generateMetaFields">
                                <template #icon>
                                    <svg
                                        class="w-4 h-4 fill-current text-slate-600 shrink-0 mr-2"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z"
                                        />
                                    </svg>
                                </template>
                                {{ t('generateMetaTags') }}
                            </MetatagsButton>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.hashtags.index')"
                            class="mb-3">
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="mb-0"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
