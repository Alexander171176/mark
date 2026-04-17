<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Создание урока (паттерн)
 */
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'

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
import InputDecimalExt from '@/Components/Admin/Input/InputDecimalExt.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import SelectStatus from '@/Components/Admin/Lesson/Select/SelectStatus.vue'
import SelectAvailability from '@/Components/Admin/Lesson/Select/SelectAvailability.vue'
import SelectAccessType from '@/Components/Admin/Lesson/Select/SelectAccessType.vue'
import SelectPreviewMode from '@/Components/Admin/Lesson/Select/SelectPreviewMode.vue'
import LessonMetricsBlock from '@/Components/Admin/Lesson/Block/LessonMetricsBlock.vue'
import LessonContentSelect from '@/Components/Admin/Lesson/Block/LessonContentSelect.vue'

import VueMultiselect from 'vue-multiselect'
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'

// --- Инициализация ---
const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из контроллера:
 * - modules
 * - hashtags
 * - currentLocale
 *
 *  LessonController@create:
 *  return Inertia::render('Admin/Lessons/Create', [
 *      'modules'       => ModuleResource::collection(...),
 *      'hashtags'      => HashtagResource::collection(...),
 *      'currentLocale' => $currentLocale,
 *  ]);
 */
const props = defineProps({
    modules: { type: Array, default: () => [] },
    hashtags: { type: Array, default: () => [] },
    currentLocale: { type: String, default: 'ru' },

    // 🔹 Контент для привязки (передаём из контроллера)
    articles:      { type: Array, default: () => [] },
    videos:        { type: Array, default: () => [] },
})

/** Форма создания */
const form = useForm({
    // 🔹 Один курс
    module_id: null,

    activity: true,
    sort: 0,

    // локаль и базовые поля
    locale: props.currentLocale || 'ru',  // ⬅ сразу проставляем локаль
    title: '',
    slug: '',
    subtitle: '',
    short: '',
    description: '',

    // учебные параметры
    difficulty: 0,
    duration: 0,
    access_type: 'free',      // free|paid|bonus
    availability: 'public',
    status: 'draft',
    published_at: '',

    // мета-поля
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',

    // превью
    preview_mode: 'none',     // none|full|percent|duration|chars
    preview_value: 0,

    // связанный контент
    content_type: '',
    content_id: '',

    // метрики
    rating_avg: 0,
    rating_count: 0,
    popularity: 0,
    views: 0,
    likes: 0,

    hashtag_ids: [],
    images: [],
})

/**
 * Универсальный лимит для любых options:
 * количество элементов + 10 запас.
 */
const dynamicOptionsLimit = (items) => {
    if (!items) return 10
    return items.length + 10
}

/** 🔹 Опции селекта модуля с указанием курса */
const moduleOptions = computed(() =>
    props.modules.map(m => {
        const moduleTitle = m.title || m.slug || `#${m.id}`
        const courseTitle = m.course?.title ?? null
        const locale = m.locale || '—'

        let labelCore = moduleTitle

        if (courseTitle) {
            labelCore = `[${courseTitle}] ${moduleTitle}`
        }

        return {
            id: m.id,
            label: `[ID: ${m.id}] [${locale}] ${labelCore}`,
        }
    })
)

/** 🔹 Один выбранный модуль */
const selectedModule = ref(null)

/** 🔹 Синхронизируем выбранный модуль в форму */
watch(selectedModule, (val) => {
    form.module_id = val ? val.id : null
})

/** 🔹 Опции и выбранные хэштеги (VueMultiselect) */
const hashtagOptions = computed(() =>
    props.hashtags.map(h => ({
        id: h.id,
        label: h.name || `#${h.id}`,
        color: h.color || null,
    }))
)

/** 🔹 Один выбранный хештег */
const selectedHashtags = ref([])

/** 🔹 Синхронизируем выбранный хештег в форму */
watch(selectedHashtags, (val) => {
    form.hashtag_ids = Array.isArray(val) ? val.map(v => v.id) : []
})

/** Новые изображения (из MultiImageUpload) */
const newImages = ref([])

/** Обновление новых изображений */
const handleNewImagesUpdate = (images) => {
    newImages.value = images
}

/** Автогенерация slug по фокусу */
const handleSlugFocus = () => {
    if (form.title && !form.slug) {
        form.slug = transliterate(form.title.toLowerCase())
    }
}

/** Обрезка текста для мета-тегов */
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''
    if (text.length <= maxLength) return text
    const truncated = text.substr(0, text.lastIndexOf(' ', maxLength))
    return addEllipsis ? `${truncated}...` : truncated
}

/** Очистка мета-полей */
const clearMetaFields = () => {
    form.meta_title = ''
    form.meta_keywords = ''
    form.meta_desc = ''
}

/** Генерация meta-полей, если не заданы вручную. */
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

/** Отправка формы создания урока */
const submitForm = () => {
    form.transform((data) => {
        const toNum = (val, digits = 2) => {
            if (val === '' || val === null || typeof val === 'undefined') return null
            const n = Number(val)
            return Number.isFinite(n) ? Number(n.toFixed(digits)) : null
        }

        let difficulty = toNum(data.difficulty, 2)
        if (difficulty !== null) {
            if (difficulty < 0) difficulty = 0
            if (difficulty > 5) difficulty = 5
        }

        const payload = {
            ...data,
            difficulty,
            activity: data.activity ? 1 : 0,
            images: newImages.value.map(img => ({
                file: img.file,
                order: img.order ?? 0,
                alt: img.alt ?? '',
                caption: img.caption ?? '',
            })),
        }

        // Страховка: если один из пары не задан — обнуляем оба
        if (!payload.content_type || !payload.content_id) {
            payload.content_type = null
            payload.content_id = null
        }

        return payload
    })

    form.post(route('admin.lessons.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Урок обучения успешно создан!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при создании Урока обучения:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createLesson')">
        <template #header>
            <TitlePage>{{ t('createLesson') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Кнопка назад -->
                    <DefaultButton :href="route('admin.lessons.index')">
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
                </div>

                <form
                    @submit.prevent="submitForm"
                    enctype="multipart/form-data"
                    class="p-3 w-full">

                    <!-- Активность, локаль, сортировка -->
                    <div
                        class="mb-3 flex justify-between flex-col
                               lg:flex-row items-center gap-4">

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2 w-auto">
                            <SelectLocale
                                v-model="form.locale"
                                :errorMessage="form.errors.locale"
                            />
                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.locale"
                            />
                        </div>

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
                                v-model="form.sort"
                                autocomplete="sort"
                                class="w-full lg:w-28"
                            />
                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.sort"
                            />
                        </div>
                    </div>

                    <!-- Доступность, Статус -->
                    <div class="mb-3 flex justify-between flex-col
                                lg:flex-row items-center gap-4">

                        <!-- Статус -->
                        <SelectStatus v-model="form.status"
                                      :errorMessage="form.errors.status" />

                        <!-- Доступность -->
                        <SelectAvailability v-model="form.availability"
                                            :errorMessage="form.errors.availability" />

                    </div>

                    <!-- Тип доступа и режим превью -->
                    <div class="mb-3 flex justify-between flex-col
                                lg:flex-row items-center gap-4">

                        <!-- Тип доступа -->
                        <SelectAccessType
                            v-model="form.access_type"
                            :errorMessage="form.errors.access_type"
                        />

                        <!-- Режим превью -->
                        <SelectPreviewMode
                            v-model="form.preview_mode"
                            :errorMessage="form.errors.preview_mode"
                        />

                        <!-- Значение превью -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="preview_value">
                                {{ t('previewValue') }}
                            </LabelInput>
                            <InputNumber
                                id="preview_value"
                                type="number"
                                min="0"
                                v-model.number="form.preview_value"
                                class="w-full lg:w-28"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.preview_value"
                            />
                        </div>

                    </div>

                    <!-- Длительность, Дата публикации -->
                    <div class="mb-3 flex justify-between flex-col
                                lg:flex-row items-center gap-4">

                        <!-- Дата публикации -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="published_at"
                                        :value="t('publishedAt')"/>
                            <InputText
                                id="published_at"
                                type="date"
                                v-model="form.published_at"
                                autocomplete="published_at"
                                class="w-full max-w-56"
                            />
                            <InputError class="mt-1 sm:mt-0" :message="form.errors.published_at"/>
                        </div>

                        <!-- Сложность -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="difficulty">
                                {{ t('difficulty') }}
                            </LabelInput>
                            <InputDecimalExt
                                id="difficulty"
                                v-model="form.difficulty"
                                :min="0"
                                :max="5"
                                :step="0.01"
                                :fraction-digits="2"
                                class="w-full lg:w-28"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.difficulty"
                            />
                        </div>

                        <!-- Длительность -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="duration">
                                {{ t('duration') }}
                            </LabelInput>
                            <InputNumber
                                id="duration"
                                type="number"
                                min="0"
                                v-model.number="form.duration"
                                class="w-full lg:w-28"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.duration"
                            />
                        </div>

                    </div>

                    <!-- Модуль -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="module"
                            :value="t('module')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="module"
                            v-model="selectedModule"
                            :options="moduleOptions"
                            :options-limit="dynamicOptionsLimit(moduleOptions)"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.module_id" />
                    </div>

                    <!-- Название -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="title">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('title') }}
                        </LabelInput>
                        <InputText
                            id="title"
                            type="text"
                            v-model="form.title"
                            required
                            autocomplete="title"
                        />
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
                            autocomplete="slug"
                            @focus="handleSlugFocus"
                        />
                        <InputError class="mt-2" :message="form.errors.slug" />
                    </div>

                    <!-- Подзаголовок/оффер -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="subtitle"
                                :value="t('subtitle')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.subtitle.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.subtitle" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.subtitle" />
                    </div>

                    <!-- Краткое описание -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="short"
                                :value="t('shortDescription')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.short.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.short" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.short" />
                    </div>

                    <!-- Описание курса -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="description" :value="t('description')" />
                        <TinyEditor v-model="form.description" :height="500" />
                        <InputError
                            class="mt-2"
                            :message="form.errors.description"
                        />
                    </div>

                    <!-- Хэштеги -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="hashtags"
                            :value="t('hashtags')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="hashtags"
                            v-model="selectedHashtags"
                            :options="hashtagOptions"
                            :multiple="true"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.hashtag_ids" />
                    </div>

                    <!-- Связанный контент (Article / Video) -->
                    <LessonContentSelect
                        :articles="articles"
                        :videos="videos"
                        v-model:contentType="form.content_type"
                        v-model:contentId="form.content_id"
                        :error-type="form.errors.content_type"
                        :error-id="form.errors.content_id"
                    />

                    <LessonMetricsBlock
                        :rating_avg="form.rating_avg"
                        :rating_count="form.rating_count"
                        :popularity="form.popularity"
                        :views="form.views"
                        :likes="form.likes"
                        :errors="{
                            rating_avg: form.errors.rating_avg,
                            rating_count: form.errors.rating_count,
                            popularity: form.errors.popularity,
                            views: form.errors.views,
                            likes: form.errors.likes
                        }"
                        @update:rating_avg="val => (form.rating_avg = val)"
                        @update:rating_count="val => (form.rating_count = val)"
                        @update:popularity="val => (form.popularity = val)"
                        @update:views="val => (form.views = val)"
                        @update:likes="val => (form.likes = val)"
                    />

                    <!-- Мета Title -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="meta_title"
                                :value="t('metaTitle')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.meta_title.length }} / 160
                                {{ t('characters') }}
                            </div>
                        </div>
                        <InputText
                            id="meta_title"
                            type="text"
                            v-model="form.meta_title"
                            maxlength="160"
                            autocomplete="off"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.meta_title"
                        />
                    </div>

                    <!-- Мета Keywords -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="meta_keywords"
                                :value="t('metaKeywords')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.meta_keywords.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <InputText
                            id="meta_keywords"
                            type="text"
                            v-model="form.meta_keywords"
                            maxlength="255"
                            autocomplete="off"
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
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
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

                    <!-- Кнопки мета-полей -->
                    <div class="flex justify-end mt-4">
                        <ClearMetaButton @clear="clearMetaFields" class="mr-4">
                            <template #default>
                                <svg
                                    class="w-4 h-4 fill-current text-gray-500 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M8 0C3.58 0 0 3.58 0 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3 9H5V7h6v2z"
                                    />
                                </svg>
                                {{ t('clearMetaFields') }}
                            </template>
                        </ClearMetaButton>

                        <MetatagsButton @click.prevent="generateMetaFields">
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-600 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z"
                                    />
                                </svg>
                            </template>
                            {{ t('generateMetaTags') }}
                        </MetatagsButton>
                    </div>

                    <!-- Загрузка новых изображений -->
                    <div class="mt-4">
                        <MultiImageUpload @update:images="handleNewImagesUpdate" />
                    </div>

                    <!-- Кнопки сохранить/назад -->
                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.lessons.index')"
                            class="mb-3"
                        >
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
                            :disabled="form.processing"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100"
                                    viewBox="0 0 16 16"
                                >
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
