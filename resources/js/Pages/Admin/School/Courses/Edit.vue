<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование курса (мультиязычная архитектура)
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

import SelectLevel from '@/Components/Admin/School/Course/Select/SelectLevel.vue'
import SelectStatus from '@/Components/Admin/School/Course/Select/SelectStatus.vue'
import SelectAvailability from '@/Components/Admin/School/Course/Select/SelectAvailability.vue'

// Локализация и Toast уведомления
const toast = useToast()
const { t } = useI18n()

// Props из контроллера
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    course: { type: Object, required: true },
    instructorProfiles: { type: Array, default: () => [] },
    tracks: { type: Array, default: () => [] },
    hashtags: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
})

// Шаблон перевода
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

// Построение переводов курса
const buildTranslations = () => {
    const result = {}

    ;(props.course.translations || []).forEach((translation) => {
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

    const defaultLocale =
        props.currentLocale ||
        props.course.translation?.locale ||
        props.availableLocales[0] ||
        'ru'

    if (!Object.keys(result).length) {
        result[defaultLocale] = makeTranslation()
    }

    if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation()
    }

    return result
}

// Локаль по умолчанию
const defaultLocale =
    props.currentLocale ||
    props.course.translation?.locale ||
    props.availableLocales[0] ||
    'ru'

// Активная локаль вкладки
const activeLocale = ref(defaultLocale)

// Форма редактирования курса
const form = useForm({
    _method: 'PUT',

    school_instructor_profile_id:
        props.course.school_instructor_profile_id ??
        props.course.instructorProfile?.id ??
        null,

    sort: props.course.sort ?? 0,
    activity: Boolean(props.course.activity),

    is_new: Boolean(props.course.is_new),
    is_hit: Boolean(props.course.is_hit),
    is_sale: Boolean(props.course.is_sale),

    left: Boolean(props.course.left),
    main: Boolean(props.course.main),
    right: Boolean(props.course.right),

    slug: props.course.slug ?? '',

    level: props.course.level ?? '',
    difficulty: props.course.difficulty ?? 0,
    duration: Number(props.course.duration ?? 0),
    availability: props.course.availability ?? 'public',
    status: props.course.status ?? 'draft',
    published_at: props.course.published_at ?? '',

    track_ids: (props.course.tracks || []).map(item => item.id),
    hashtag_ids: (props.course.hashtags || []).map(item => item.id),
    related_course_ids: (props.course.related_courses || [])
        .filter(item => item.id !== props.course.id)
        .map(item => item.id),

    translations: buildTranslations(),

    deletedImages: [],
})

// Текущий перевод активной локали
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// Заголовок страницы
const pageTitle = computed(() => {
    return currentTranslation.value.title
        || props.course.translation?.title
        || props.course.title
        || `ID: ${props.course.id}`
})

// Получение ошибки перевода
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

// Форматирование даты публикации
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    if (isNaN(date.getTime())) return ''
    return date.toISOString().split('T')[0]
}

// Подготовка даты после загрузки компонента
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

// Опции инструкторов
const instructorProfileOptions = computed(() =>
    props.instructorProfiles.map((item) => {
        const title = item.public_name || item.title || item.name || `#${item.id}`
        const userName = item.user?.name ? ` — ${item.user.name}` : ''

        return {
            id: item.id,
            label: `[ID: ${item.id}] ${title}${userName}`,
        }
    })
)

// Выбранный инструктор
const selectedInstructorProfile = ref(
    instructorProfileOptions.value.find(item => item.id === form.school_instructor_profile_id) || null
)

// Синхронизация инструктора с формой
watch(selectedInstructorProfile, (val) => {
    form.school_instructor_profile_id = val?.id ?? null
})

// Опции треков
const trackOptions = computed(() =>
    props.tracks.map((item) => ({
        id: item.id,
        label: `[ID: ${item.id}] ${item.name || item.slug || `#${item.id}`}`,
    }))
)

// Выбранные треки
const selectedTracks = ref(
    (props.course.tracks || []).map((item) => ({
        id: item.id,
        label: `[ID: ${item.id}] ${item.name || item.slug || `#${item.id}`}`,
    }))
)

// Синхронизация треков с формой
watch(selectedTracks, (val) => {
    form.track_ids = Array.isArray(val) ? val.map(item => item.id) : []
})

// Опции хештегов
const hashtagOptions = computed(() =>
    props.hashtags.map((item) => ({
        id: item.id,
        label: `[ID: ${item.id}] ${item.name || item.slug || `#${item.id}`}`,
        color: item.color || null,
    }))
)

// Выбранные хештеги
const selectedHashtags = ref(
    (props.course.hashtags || []).map((item) => ({
        id: item.id,
        label: `[ID: ${item.id}] ${item.name || item.slug || `#${item.id}`}`,
        color: item.color || null,
    }))
)

// Синхронизация хештегов с формой
watch(selectedHashtags, (val) => {
    form.hashtag_ids = Array.isArray(val) ? val.map(item => item.id) : []
})

// Опции связанных курсов
const relatedCourseOptions = computed(() =>
    props.courses
        .filter(item => item.id !== props.course.id)
        .map((item) => ({
            id: item.id,
            label: `[ID: ${item.id}] ${item.title || item.slug || `#${item.id}`}`,
        }))
)

// Выбранные связанные курсы
const selectedRelatedCourses = ref(
    (props.course.related_courses || [])
        .filter(item => item.id !== props.course.id)
        .map((item) => ({
            id: item.id,
            label: `[ID: ${item.id}] ${item.title || item.slug || `#${item.id}`}`,
        }))
)

// Синхронизация связанных курсов с формой
watch(selectedRelatedCourses, (val) => {
    form.related_course_ids = Array.isArray(val) ? val.map(item => item.id) : []
})

// Существующие изображения курса
const existingImages = ref(
    (props.course.images || [])
        .filter(image => image.webp_url || image.url || image.image_url)
        .map(image => ({
            id: image.id,
            url: image.webp_url || image.url || image.image_url,
            order: image.order || 0,
            alt: image.alt || '',
            caption: image.caption || '',
        }))
)

// Новые изображения курса
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

    existingImages.value = existingImages.value.filter(image => image.id !== deletedId)
}

// Обновление новых изображений
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

// Генерация slug из заголовка
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

// Отправка формы редактирования курса
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

        const transformed = {
            ...data,

            difficulty,
            duration: data.duration === '' || data.duration === null
                ? null
                : Number(data.duration),

            activity: data.activity ? 1 : 0,
            is_new: data.is_new ? 1 : 0,
            is_hit: data.is_hit ? 1 : 0,
            is_sale: data.is_sale ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,

            school_instructor_profile_id: selectedInstructorProfile.value?.id ?? null,
            track_ids: Array.isArray(selectedTracks.value) ? selectedTracks.value.map(item => item.id) : [],
            hashtag_ids: Array.isArray(selectedHashtags.value) ? selectedHashtags.value.map(item => item.id) : [],
            related_course_ids: Array.isArray(selectedRelatedCourses.value)
                ? selectedRelatedCourses.value.map(item => item.id)
                : [],
        }

        delete transformed.images
        delete transformed.deletedImages

        let index = 0

        existingImages.value.forEach((image) => {
            transformed[`images[${index}][id]`] = image.id
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
            index++
        })

        newImages.value.forEach((image) => {
            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
            index++
        })

        form.deletedImages.forEach((id, deletedIndex) => {
            transformed[`deletedImages[${deletedIndex}]`] = id
        })

        return transformed
    })

    form.post(route('admin.schoolCourses.update', {
        schoolCourse: props.course.id,
    }), {
        errorBag: 'editSchoolCourse',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Курс успешно обновлён!')
            newImages.value = []
            form.deletedImages = []
        },
        onError: (errors) => {
            console.error('Ошибка обновления курса:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editCourse')">
        <template #header>
            <TitlePage>
                {{ t('editCourse') }}: {{ pageTitle }} [ID: {{ props.course.id }}]
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
                    <DefaultButton :href="route('admin.schoolCourses.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
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
                                <LabelInput
                                    for="sort"
                                    :value="t('sort')"
                                    class="text-sm"
                                />
                                <InputNumber
                                    id="sort"
                                    type="number"
                                    v-model.number="form.sort"
                                    autocomplete="sort"
                                    class="w-full lg:w-28"
                                />
                                <InputError
                                    class="mt-2 lg:mt-0"
                                    :message="form.errors.sort"
                                />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.left" />
                                <LabelCheckbox
                                    for="left"
                                    :text="t('left')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.main" />
                                <LabelCheckbox
                                    for="main"
                                    :text="t('main')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.right" />
                                <LabelCheckbox
                                    for="right"
                                    :text="t('right')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.is_new" />
                                <LabelCheckbox
                                    for="is_new"
                                    :text="t('sortIsNew')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.is_hit" />
                                <LabelCheckbox
                                    for="is_hit"
                                    :text="t('sortIsHit')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.is_sale" />
                                <LabelCheckbox
                                    for="is_sale"
                                    :text="t('sortIsSale')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <SelectLevel
                                v-model="form.level"
                                :errorMessage="form.errors.level"
                            />

                            <SelectStatus
                                v-model="form.status"
                                :errorMessage="form.errors.status"
                            />

                            <SelectAvailability
                                v-model="form.availability"
                                :errorMessage="form.errors.availability"
                            />
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput
                                    for="published_at"
                                    :value="t('publishedAt')"
                                />
                                <InputText
                                    id="published_at"
                                    type="date"
                                    v-model="form.published_at"
                                    autocomplete="published_at"
                                    class="w-full max-w-56"
                                />
                                <InputError
                                    class="mt-1 sm:mt-0"
                                    :message="form.errors.published_at"
                                />
                            </div>

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

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="school_instructor_profile_id"
                                :value="t('instructor')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="school_instructor_profile_id"
                                v-model="selectedInstructorProfile"
                                :options="instructorProfileOptions"
                                :options-limit="dynamicOptionsLimit(instructorProfileOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :allow-empty="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.school_instructor_profile_id"
                            />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="track_ids"
                                :value="t('tracks')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="track_ids"
                                v-model="selectedTracks"
                                :options="trackOptions"
                                :options-limit="dynamicOptionsLimit(trackOptions)"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.track_ids"
                            />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="hashtag_ids"
                                :value="t('hashtags')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="hashtag_ids"
                                v-model="selectedHashtags"
                                :options="hashtagOptions"
                                :options-limit="dynamicOptionsLimit(hashtagOptions)"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.hashtag_ids"
                            />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="related_course_ids"
                                :value="t('relatedCourses')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="related_course_ids"
                                v-model="selectedRelatedCourses"
                                :options="relatedCourseOptions"
                                :options-limit="dynamicOptionsLimit(relatedCourseOptions)"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.related_course_ids"
                            />
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
                            <InputError
                                class="mt-2"
                                :message="form.errors.slug"
                            />
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
                                    autocomplete="title"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('title')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="subtitle"
                                        :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                                    />
                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.subtitle || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    v-model="currentTranslation.subtitle"
                                    class="w-full"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('subtitle')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="short"
                                        :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                                    />
                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.short || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    v-model="currentTranslation.short"
                                    class="w-full"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('short')"
                                />
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

                                <InputError
                                    class="mt-2"
                                    :message="getError('description')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="meta_title"
                                        :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                                    />
                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_title || '').length }} / 160 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="meta_title"
                                    type="text"
                                    v-model="currentTranslation.meta_title"
                                    maxlength="160"
                                    autocomplete="meta_title"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('meta_title')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="meta_keywords"
                                        :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`"
                                    />
                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_keywords || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="meta_keywords"
                                    type="text"
                                    v-model="currentTranslation.meta_keywords"
                                    maxlength="255"
                                    autocomplete="meta_keywords"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('meta_keywords')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="meta_desc"
                                        :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`"
                                    />
                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_desc || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    v-model="currentTranslation.meta_desc"
                                    maxlength="255"
                                    class="w-full"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('meta_desc')"
                                />
                            </div>

                            <div class="flex justify-end gap-2 mt-4">
                                <ClearMetaButton @click.prevent="clearMetaFields">
                                    <template #default>
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
                        </div>

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
                        <DefaultButton
                            :href="route('admin.schoolCourses.index')">
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
