<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование урока
 */
import { computed, ref, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import ClearMetaButton from '@/Components/Admin/UI/Buttons/ClearMetaButton.vue'
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputDecimalExt from '@/Components/Admin/UI/Input/InputDecimalExt.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/UI/Image/MultiImageEdit.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

import SelectStatus from '@/Components/Admin/School/SchoolLesson/Select/SelectStatus.vue'
import SelectAvailability from '@/Components/Admin/School/SchoolLesson/Select/SelectAvailability.vue'
import SelectAccessType from '@/Components/Admin/School/SchoolLesson/Select/SelectAccessType.vue'
import SelectPreviewMode from '@/Components/Admin/School/SchoolLesson/Select/SelectPreviewMode.vue'
import LessonContentSelect from '@/Components/Admin/School/SchoolLesson/Block/LessonContentSelect.vue'

// Toast уведомления
const toast = useToast()

// Локализация
const { t } = useI18n()

// Props страницы редактирования урока
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    lesson: { type: Object, required: true },
    modules: { type: Array, default: () => [] },
    hashtags: { type: Array, default: () => [] },
    articles: { type: Array, default: () => [] },
    videos: { type: Array, default: () => [] },
})

// Пустая структура перевода
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

// Формирование переводов из lesson.translations
const buildTranslations = () => {
    const result = {}

    ;(props.lesson.translations || []).forEach((translation) => {
        result[translation.locale] = {
            title: translation.title || '',
            subtitle: translation.subtitle || '',
            short: translation.short || '',
            description: translation.description || '',
            meta_title: translation.meta_title || '',
            meta_keywords: translation.meta_keywords || '',
            meta_desc: translation.meta_desc || '',
        }
    })

    // Определение локали по умолчанию
    const defaultLocale =
        props.currentLocale ||
        props.lesson.translation?.locale ||
        props.availableLocales[0] ||
        'ru'

    // Если переводов нет — создаём дефолтный
    if (!Object.keys(result).length) {
        result[defaultLocale] = makeTranslation()
    }

    // Гарантия существования дефолтной локали
    if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation()
    }

    return result
}

// Активная локаль по умолчанию
const defaultLocale =
    props.currentLocale ||
    props.lesson.translation?.locale ||
    props.availableLocales[0] ||
    'ru'

// Текущая активная локаль
const activeLocale = ref(defaultLocale)

// Основная форма редактирования урока
const form = useForm({
    _method: 'PUT',

    school_module_id:
        props.lesson.school_module_id ??
        props.lesson.module?.id ??
        null,

    sort: props.lesson.sort ?? 0,
    activity: Boolean(props.lesson.activity),

    slug: props.lesson.slug ?? '',

    difficulty: props.lesson.difficulty ?? 0,
    duration: Number(props.lesson.duration ?? 0),
    access_type: props.lesson.access_type ?? 'free',
    availability: props.lesson.availability ?? 'public',
    status: props.lesson.status ?? 'draft',
    published_at: props.lesson.published_at ?? '',

    preview_mode: props.lesson.preview_mode ?? 'none',
    preview_value: props.lesson.preview_value ?? 0,

    // Связанный контент
    content_type: props.lesson.content_type ?? '',
    content_id: props.lesson.content_id ?? '',

    // Хэштеги
    hashtag_ids: (props.lesson.hashtags || []).map(item => item.id),

    // Переводы
    translations: buildTranslations(),

    // Удалённые изображения
    deletedImages: [],
})

// Текущий активный перевод
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// Заголовок страницы
const pageTitle = computed(() => {
    return currentTranslation.value.title
        || props.lesson.translation?.title
        || props.lesson.title
        || `ID: ${props.lesson.id}`
})

// Получение ошибок переводов
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

// Форматирование даты в yyyy-MM-dd
const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const date = new Date(dateStr)

    if (isNaN(date.getTime())) return ''

    return date.toISOString().split('T')[0]
}

// Нормализация даты после загрузки страницы
onMounted(() => {
    if (form.published_at) {
        form.published_at = formatDate(form.published_at)
    }
})

// Динамический лимит multiselect
const dynamicOptionsLimit = (items) => {
    if (!items) return 10
    return items.length + 10
}

// Опции модулей
const moduleOptions = computed(() =>
    props.modules.map((item) => {
        const moduleTitle = item.title || item.slug || `#${item.id}`
        const courseTitle = item.course?.title || null

        return {
            id: item.id,
            label: courseTitle
                ? `[ID: ${item.id}] [${courseTitle}] ${moduleTitle}`
                : `[ID: ${item.id}] ${moduleTitle}`,
        }
    })
)

// Выбранный модуль
const selectedModule = ref(
    moduleOptions.value.find(item => Number(item.id) === Number(form.school_module_id)) || null
)

// Синхронизация выбранного модуля с form
watch(selectedModule, (val) => {
    form.school_module_id = val?.id ?? null
})

// Опции хэштегов
const hashtagOptions = computed(() =>
    props.hashtags.map((item) => ({
        id: item.id,
        label: item.name || item.title || item.slug || `#${item.id}`,
        color: item.color || null,
    }))
)

// Выбранные хэштеги
const selectedHashtags = ref([])

// Восстановление выбранных хэштегов
watch(
    hashtagOptions,
    (options) => {
        const ids = form.hashtag_ids || []
        selectedHashtags.value = options.filter(item => ids.includes(item.id))
    },
    { immediate: true }
)

// Синхронизация хэштегов с form
watch(selectedHashtags, (val) => {
    form.hashtag_ids = Array.isArray(val) ? val.map(item => item.id) : []
})

// Существующие изображения
const existingImages = ref(
    (props.lesson.images || [])
        .filter(image => image.webp_url || image.url || image.image_url)
        .map(image => ({
            id: image.id,
            url: image.webp_url || image.url || image.image_url,
            order: image.order || 0,
            alt: image.alt || '',
            caption: image.caption || '',
        }))
)

// Новые изображения
const newImages = ref([])

// Обновление существующих изображений
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images || []
}

// Удаление существующего изображения
const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }

    existingImages.value = existingImages.value.filter(
        image => image.id !== deletedId
    )
}

// Обновление новых изображений
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

// Автогенерация slug из title
const handleSlugFocus = () => {
    if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

// Обрезка текста
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''

    const str = String(text)

    if (str.length <= maxLength) return str

    const lastSpaceIndex = str.lastIndexOf(' ', maxLength)
    const truncated = lastSpaceIndex === -1
        ? str.substring(0, maxLength)
        : str.substring(0, lastSpaceIndex)

    return addEllipsis ? `${truncated}...` : truncated
}

// Очистка SEO полей
const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

// Генерация SEO полей
const generateMetaFields = () => {
    const translation = currentTranslation.value

    if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(translation.title, 160)
    }

    if (!translation.meta_keywords && translation.short) {
        let text = String(translation.short).replace(/(<([^>]+)>)/gi, '')
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, '')

        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        translation.meta_keywords = truncateText(words.join(', '), 255)
    }

    if (translation.short && !translation.meta_desc) {
        const descText = String(translation.short).replace(/(<([^>]+)>)/gi, '')
        translation.meta_desc = truncateText(descText, 255, true)
    }
}

// Отправка формы
const submitForm = () => {
    form.transform((data) => {

        // Преобразование значения в число
        const toNum = (val, digits = 2) => {
            if (val === '' || val === null || typeof val === 'undefined') {
                return null
            }

            const n = Number(val)

            return Number.isFinite(n)
                ? Number(n.toFixed(digits))
                : null
        }

        // Ограничение сложности от 0 до 5
        let difficulty = toNum(data.difficulty, 2)

        if (difficulty !== null) {
            if (difficulty < 0) difficulty = 0
            if (difficulty > 5) difficulty = 5
        }

        // Подготовка данных формы
        const transformed = {
            ...data,

            school_module_id: selectedModule.value?.id ?? null,

            difficulty,

            duration: data.duration === '' || data.duration === null
                ? null
                : Number(data.duration),

            preview_value: data.preview_value === '' || data.preview_value === null
                ? null
                : Number(data.preview_value),

            activity: data.activity ? 1 : 0,

            hashtag_ids: selectedHashtags.value.map(item => item.id),
        }

        // Сброс связанного контента если связь пустая
        if (!transformed.content_type || !transformed.content_id) {
            transformed.content_type = null
            transformed.content_id = null
        }

        // Удаление лишних полей
        delete transformed.images
        delete transformed.deletedImages

        let index = 0

        // Существующие изображения
        existingImages.value.forEach((image) => {
            transformed[`images[${index}][id]`] = image.id
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
            index++
        })

        // Новые изображения
        newImages.value.forEach((image) => {
            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
            index++
        })

        // Удалённые изображения
        form.deletedImages.forEach((id, deletedIndex) => {
            transformed[`deletedImages[${deletedIndex}]`] = id
        })

        return transformed
    })

    // Отправка формы обновления
    form.post(route('admin.schoolLessons.update', {
        schoolLesson: props.lesson.id,
    }), {
        errorBag: 'editSchoolLesson',
        preserveScroll: true,
        forceFormData: true,

        // Успешное обновление
        onSuccess: () => {
            toast.success('Урок успешно обновлён!')

            newImages.value = []
            form.deletedImages = []
        },

        // Ошибки валидации
        onError: (errors) => {
            console.error('Ошибка обновления урока:', errors)

            const firstKey = Object.keys(errors || {})[0]

            toast.error(
                errors[firstKey] || 'Проверьте корректность полей.'
            )
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editLesson')">
        <template #header>
            <TitlePage>
                {{ t('editLesson') }}: {{ pageTitle }} [ID: {{ props.lesson.id }}]
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
                    <DefaultButton :href="route('admin.schoolLessons.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form
                    @submit.prevent="submitForm"
                    enctype="multipart/form-data"
                    class="p-3 w-full"
                >
                    <div class="pb-12">
                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                                <InputNumber
                                    id="sort"
                                    type="number"
                                    v-model.number="form.sort"
                                    class="w-full lg:w-28"
                                />
                                <InputError :message="form.errors.sort" />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <SelectStatus
                                v-model="form.status"
                                :errorMessage="form.errors.status"
                            />

                            <SelectAvailability
                                v-model="form.availability"
                                :errorMessage="form.errors.availability"
                            />

                            <SelectAccessType
                                v-model="form.access_type"
                                :errorMessage="form.errors.access_type"
                            />
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <SelectPreviewMode
                                v-model="form.preview_mode"
                                :errorMessage="form.errors.preview_mode"
                            />

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

                                <InputError :message="form.errors.preview_value" />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="published_at" :value="t('publishedAt')" />

                                <InputText
                                    id="published_at"
                                    type="date"
                                    v-model="form.published_at"
                                    class="w-full max-w-56"
                                />

                                <InputError :message="form.errors.published_at" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="difficulty">{{ t('difficulty') }}</LabelInput>

                                <InputDecimalExt
                                    id="difficulty"
                                    v-model="form.difficulty"
                                    :min="0"
                                    :max="5"
                                    :step="0.01"
                                    :fraction-digits="2"
                                    class="w-full lg:w-28"
                                />

                                <InputError :message="form.errors.difficulty" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="duration">{{ t('duration') }}</LabelInput>

                                <InputNumber
                                    id="duration"
                                    type="number"
                                    min="0"
                                    v-model.number="form.duration"
                                    class="w-full lg:w-28"
                                />

                                <InputError :message="form.errors.duration" />
                            </div>
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="school_module_id" :value="t('module')" class="mb-1" />

                            <VueMultiselect
                                id="school_module_id"
                                v-model="selectedModule"
                                :options="moduleOptions"
                                :options-limit="dynamicOptionsLimit(moduleOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :allow-empty="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.school_module_id" />
                        </div>

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

                        <div
                            class="my-5 p-3 border border-slate-300 dark:border-slate-500
                                   bg-white dark:bg-slate-800 rounded-sm"
                        >
                            <TranslationTabs
                                v-model="activeLocale"
                                :translations="form.translations"
                                :available-locales="availableLocales"
                                :make-translation="makeTranslation"
                                @update:translations="form.translations = $event"
                                @removed="toast.warning('Перевод удалён.')"
                                @added="toast.success('Локаль добавлена.')"
                            />

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput for="title">
                                        <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                        {{ t('title') }} [{{ activeLocale.toUpperCase() }}]
                                    </LabelInput>

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.title || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="title"
                                    type="text"
                                    v-model="currentTranslation.title"
                                    maxlength="255"
                                    required
                                />

                                <InputError class="mt-2" :message="getError('title')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="subtitle"
                                    :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                                />

                                <MetaDescTextarea v-model="currentTranslation.subtitle" class="w-full" />
                                <InputError class="mt-2" :message="getError('subtitle')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="short"
                                    :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                                />

                                <MetaDescTextarea v-model="currentTranslation.short" class="w-full" />
                                <InputError class="mt-2" :message="getError('short')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="description"
                                    :value="`${t('description')} [${activeLocale.toUpperCase()}]`"
                                />

                                <TinyEditor
                                    v-model="currentTranslation.description"
                                    :height="500"
                                />

                                <InputError class="mt-2" :message="getError('description')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="meta_title"
                                    :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                                />

                                <InputText
                                    id="meta_title"
                                    type="text"
                                    v-model="currentTranslation.meta_title"
                                    maxlength="160"
                                />

                                <InputError class="mt-2" :message="getError('meta_title')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="meta_keywords"
                                    :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`"
                                />

                                <InputText
                                    id="meta_keywords"
                                    type="text"
                                    v-model="currentTranslation.meta_keywords"
                                    maxlength="255"
                                />

                                <InputError class="mt-2" :message="getError('meta_keywords')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="meta_desc"
                                    :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`"
                                />

                                <MetaDescTextarea
                                    v-model="currentTranslation.meta_desc"
                                    maxlength="255"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="getError('meta_desc')" />
                            </div>

                            <div class="flex justify-end gap-2 mt-4">
                                <ClearMetaButton @click.prevent="clearMetaFields">
                                    <template #default>
                                        {{ t('clearMetaFields') }}
                                    </template>
                                </ClearMetaButton>

                                <MetatagsButton @click.prevent="generateMetaFields">
                                    {{ t('generateMetaTags') }}
                                </MetatagsButton>
                            </div>
                        </div>

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

                        <LessonContentSelect
                            :articles="articles"
                            :videos="videos"
                            v-model:contentType="form.content_type"
                            v-model:contentId="form.content_id"
                            :error-type="form.errors.content_type"
                            :error-id="form.errors.content_id"
                        />

                        <div class="mt-4">
                            <MultiImageEdit
                                :images="existingImages"
                                @update:images="handleExistingImagesUpdate"
                                @delete-image="handleDeleteExistingImage"
                            />
                        </div>

                        <div class="mt-4">
                            <MultiImageUpload @update:images="handleNewImagesUpdate" />

                            <div
                                v-if="newImages.length"
                                class="text-xs text-slate-600 dark:text-slate-300 mt-2"
                            >
                                {{ t('images') }}: {{ newImages.length }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton :href="route('admin.schoolLessons.index')">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
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
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
