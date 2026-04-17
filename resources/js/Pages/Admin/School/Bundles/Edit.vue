<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Редактирование бандла (Bundle)
 */
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { transliterate } from '@/utils/transliteration'
import { computed, defineProps, onMounted, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import ClearMetaButton from '@/Components/Admin/Buttons/ClearMetaButton.vue'
import MetatagsButton from '@/Components/Admin/Buttons/MetatagsButton.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import VueMultiselect from 'vue-multiselect'
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/Image/MultiImageEdit.vue'

// --- i18n, toast ---
const toast = useToast()
const { t } = useI18n()

/**
 * props:
 * bundle — ресурс бандла + отношения (images, courses)
 * courses — список всех курсов (для выбора состава бандла)
 */
const props = defineProps({
    bundle: { type: Object, required: true },
    courses: { type: Array, default: () => [] },
})

/** Форма редактирования */
const form = useForm({
    _method: 'PUT',

    sort: props.bundle.sort ?? 0,
    activity: Boolean(props.bundle.activity),

    locale: props.bundle.locale ?? '',
    title: props.bundle.title ?? '',
    slug: props.bundle.slug ?? '',
    subtitle: props.bundle.subtitle ?? '',
    short: props.bundle.short ?? '',
    description: props.bundle.description ?? '',

    published_at: props.bundle.published_at ?? '',

    meta_title: props.bundle.meta_title ?? '',
    meta_keywords: props.bundle.meta_keywords ?? '',
    meta_desc: props.bundle.meta_desc ?? '',

    // состав бандла
    course_ids: ((props.bundle.courses || props.bundle.bundle_courses) || []).map(c => c.id),

    deletedImages: [],
})

/** Универсальный лимит */
const dynamicOptionsLimit = (items) => {
    if (!items) return 10
    return items.length + 10
}

/** Формат даты */
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    if (isNaN(d.getTime())) return ''
    return d.toISOString().split('T')[0]
}

onMounted(() => {
    if (form.published_at) {
        form.published_at = formatDate(form.published_at)
    }
})

/** Опции курсов */
const courseOptions = computed(() =>
    props.courses.map(c => ({
        id: c.id,
        label: c.title ? `[ID: ${c.id}] ${c.title}` : `#${c.id}`,
    }))
)

/** Текущее значение мультиселекта (курсы бандла) */
const selectedCourses = ref(
    ((props.bundle.courses || props.bundle.bundle_courses) || []).map(c => ({
        id: c.id,
        label: c.title ? `[ID: ${c.id}] ${c.title}` : `#${c.id}`,
    }))
)

/** синхронизируем IDs курсов в форму */
watch(selectedCourses, (val) => {
    form.course_ids = Array.isArray(val) ? val.map(v => v.id) : []
})

/** Существующие изображения */
const existingImages = ref(
    (props.bundle.images || [])
        .filter(img => img.url)
        .map(img => ({
            id: img.id,
            url: img.webp_url || img.url,
            order: img.order || 0,
            alt: img.alt || '',
            caption: img.caption || '',
        }))
)

/** Новые изображения */
const newImages = ref([])

/** update existing */
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images
}

/** delete existing */
const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }
    existingImages.value = existingImages.value.filter(img => img.id !== deletedId)
}

/** update new */
const handleNewImagesUpdate = (images) => {
    newImages.value = images
}

/** автоген slug */
const handleSlugFocus = () => {
    if (form.title) {
        form.slug = transliterate(form.title.toLowerCase())
    }
}

/** truncate */
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''
    if (text.length <= maxLength) return text
    const truncated = text.substr(0, text.lastIndexOf(' ', maxLength))
    return addEllipsis ? `${truncated}...` : truncated
}

/** clear meta */
const clearMetaFields = () => {
    form.meta_title = ''
    form.meta_keywords = ''
    form.meta_desc = ''
}

/** generate meta */
const generateMetaFields = () => {
    if (form.title && !form.meta_title) {
        form.meta_title = truncateText(form.title, 160)
    }

    if (!form.meta_keywords && form.short) {
        let text = form.short.replace(/(<([^>]+)>)/gi, '')
        text = text.replace(/[.,!?;:()\[\]{}"'«»]/g, '')

        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        const keywords = words.join(', ')
        form.meta_keywords = truncateText(keywords, 255)
    }

    if (form.short && !form.meta_desc) {
        const descText = form.short.replace(/(<([^>]+)>)/gi, '')
        form.meta_desc = truncateText(descText, 255, true)
    }
}

/** submit update */
const submitForm = () => {
    form.transform((data) => {
        return {
            ...data,
            activity: data.activity ? 1 : 0,
            images: [
                // новые
                ...newImages.value.map(img => ({
                    file: img.file,
                    order: img.order ?? 0,
                    alt: img.alt ?? '',
                    caption: img.caption ?? '',
                })),
                // существующие
                ...existingImages.value.map(img => ({
                    id: img.id,
                    order: img.order ?? 0,
                    alt: img.alt ?? '',
                    caption: img.caption ?? '',
                })),
            ],
            deletedImages: form.deletedImages,
        }
    })

    form.post(route('admin.bundles.update', props.bundle.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Бандл успешно обновлён!'),
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении бандла:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editBundle')">
        <template #header>
            <TitlePage>
                {{ t('editBundle') }} - {{ props.bundle.title }} [ID: {{ props.bundle.id }}]
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
                    <DefaultButton :href="route('admin.bundles.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" enctype="multipart/form-data" class="p-3 w-full">
                    <!-- Активность, локаль, сортировка -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox for="activity" :text="t('activity')"
                                           class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2 w-auto">
                            <SelectLocale v-model="form.locale"
                                          :errorMessage="form.errors.locale" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.locale" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            </div>
                            <InputNumber id="sort"
                                         type="number" v-model="form.sort" autocomplete="sort"
                                         class="w-full lg:w-28" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <!-- Дата публикации -->
                    <div class="mb-3 flex justify-center flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="published_at" :value="t('publishedAt')" />
                            <InputText
                                id="published_at"
                                type="date"
                                v-model="form.published_at"
                                autocomplete="published_at"
                                class="w-full max-w-56"
                            />
                            <InputError class="mt-1 sm:mt-0" :message="form.errors.published_at" />
                        </div>
                    </div>

                    <!-- Состав бандла -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="courses" :value="t('courses')" class="mb-1" />
                        <VueMultiselect
                            id="courses"
                            v-model="selectedCourses"
                            :options="courseOptions"
                            :options-limit="dynamicOptionsLimit(courseOptions)"
                            :multiple="true"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.course_ids" />
                    </div>

                    <!-- Название -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="title">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('title') }}
                        </LabelInput>
                        <InputText id="title" type="text"
                                   v-model="form.title" required autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <!-- Slug -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="slug">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('slug') }}
                        </LabelInput>
                        <InputText
                            id="slug"
                            type="text"
                            v-model="form.slug"
                            required
                            autocomplete="off"
                            @focus="handleSlugFocus"
                        />
                        <InputError class="mt-2" :message="form.errors.slug" />
                    </div>

                    <!-- Подзаголовок -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="subtitle" :value="t('subtitle')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.subtitle.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.subtitle" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.subtitle" />
                    </div>

                    <!-- Краткое описание -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="short" :value="t('shortDescription')" />
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

                    <!-- Мета Title -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_title" :value="t('metaTitle')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_title.length }} / 160 {{ t('characters') }}
                            </div>
                        </div>
                        <InputText id="meta_title" type="text"
                                   v-model="form.meta_title" maxlength="160"
                                   autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.meta_title" />
                    </div>

                    <!-- Мета Keywords -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_keywords" :value="t('metaKeywords')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_keywords.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <InputText
                            id="meta_keywords"
                            type="text"
                            v-model="form.meta_keywords"
                            maxlength="255"
                            autocomplete="off"
                        />
                        <InputError class="mt-2" :message="form.errors.meta_keywords" />
                    </div>

                    <!-- Мета Description -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_desc" :value="t('metaDescription')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_desc.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.meta_desc" maxlength="255" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.meta_desc" />
                    </div>

                    <!-- Кнопки мета -->
                    <div class="flex justify-end mt-4">
                        <ClearMetaButton @clear="clearMetaFields" class="mr-4">
                            <template #default>
                                <svg class="w-4 h-4 fill-current text-gray-500 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M8 0C3.58 0 0 3.58 0 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3 9H5V7h6v2z"
                                    />
                                </svg>
                                {{ t('clearMetaFields') }}
                            </template>
                        </ClearMetaButton>

                        <MetatagsButton @click.prevent="generateMetaFields">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-600 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z"
                                    />
                                </svg>
                            </template>
                            {{ t('generateMetaTags') }}
                        </MetatagsButton>
                    </div>

                    <!-- Существующие изображения -->
                    <div class="mt-4">
                        <MultiImageEdit
                            :images="existingImages"
                            @update:images="handleExistingImagesUpdate"
                            @delete-image="handleDeleteExistingImage"
                        />
                    </div>

                    <!-- Новые изображения -->
                    <div class="mt-4">
                        <MultiImageUpload @update:images="handleNewImagesUpdate" />
                    </div>

                    <!-- Кнопки -->
                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.bundles.index')" class="mb-3">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton class="ms-4 mb-0" :disabled="form.processing"
                                       :class="{ 'opacity-25': form.processing }">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100"
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

<style src="/resources/css/vue-multiselect.min.css"></style>
