<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Создание курса (Course)
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
import SelectLevel from '@/Components/Admin/Course/Select/SelectLevel.vue'
import SelectStatus from '@/Components/Admin/Course/Select/SelectStatus.vue'
import SelectAvailability from '@/Components/Admin/Course/Select/SelectAvailability.vue'
import VueMultiselect from 'vue-multiselect'
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из контроллера:
 * - instructorProfiles
 * - learningCategories
 * - hashtags
 *
 * В контроллере:
 *  return Inertia::render('Admin/Courses/Create', [
 *      'instructorProfiles' => InstructorProfileResource::collection(...),
 *      'learningCategories' => LearningCategoryResource::collection(...),
 *      'hashtags'           => HashtagResource::collection(...),
 *  ]);
 */
const props = defineProps({
    instructorProfiles: { type: Array, default: () => [] },
    learningCategories: { type: Array, default: () => [] },
    hashtags: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
})

/** Форма создания курса */
const form = useForm({
    activity: true,
    sort: 0,

    // флаги размещения
    is_new: false,
    is_hit: false,
    is_sale: false,
    left: false,
    main: false,
    right: false,

    // локаль и базовые поля
    locale: '',
    title: '',
    slug: '',
    subtitle: '',
    short: '',
    description: '',

    // учебные параметры
    level: '',
    difficulty: 0,
    duration: 0,
    availability: 'public',
    status: 'draft',
    published_at: '',

    // мета-поля
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',

    // связи
    instructor_profile_id: null,
    learning_category_ids: [],
    hashtag_ids: [],
    related_course_ids: [],

    // изображения (только новые)
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

/** Опции селекта профилей инструктора */
const instructorProfileOptions = computed(() =>
    props.instructorProfiles.map(i => {
        const locale   = i.locale || '—'
        const userName = i.user?.name || i.full_name || i.title || `#${i.id}`
        const title    = i.title ?? null

        let label = `[ID: ${i.id}] [${locale}] ${userName}`

        if (title && title !== userName) {
            label += ` — ${title}`
        }

        return {
            id: i.id,
            label,
        }
    })
)

/** 🔹 Один выбранный инструктор */
const selectedInstructorProfile = ref(null)

/** 🔹 Синхронизируем выбранного инструктора в форме */
watch(selectedInstructorProfile, val => {
    form.instructor_profile_id = val?.id ?? null
})

/** Опции выбора селекта категорий */
const learningCategoryOptions = computed(() =>
    props.learningCategories.map(c => {
        const name   = c.name || c.slug || `#${c.id}`
        const locale = c.locale || '—'

        return {
            id: c.id,
            label: `[ID: ${c.id}] [${locale}] ${name}`,
        }
    })
)

/** Опции выбора селекта хештегов */
const hashtagOptions = computed(() =>
    props.hashtags.map(t => {
        const name   = t.name || t.slug || `#${t.id}`
        const locale = t.locale || '—'

        return {
            id: t.id,
            label: `[ID: ${t.id}] [${locale}] ${name}`,
            color: t.color || null,
        }
    })
)

/** Опции выбора селекта рекомендованных курсов */
const relatedCourseOptions = computed(() =>
    props.courses.map(c => {
        const locale = c.locale || '—'
        const title  = c.title || `#${c.id}`

        return {
            id: c.id,
            label: `[ID: ${c.id}] [${locale}] ${title}`,
        }
    })
)

/** 🔹 Выбранные категории/хештеги/рекомендованные курсы */
const selectedLearningCategories = ref([])
const selectedHashtags = ref([])
const selectedRelatedCourses = ref([])

/** Синхронизируем IDs категорий в форму */
watch(selectedLearningCategories, (val) => {
    form.learning_category_ids = Array.isArray(val) ? val.map(v => v.id) : []
})

/** Синхронизируем IDs хештегов в форму */
watch(selectedHashtags, (val) => {
    form.hashtag_ids = Array.isArray(val) ? val.map(v => v.id) : []
})

/** Синхронизируем IDs рекомендованных курсов в форму */
watch(selectedRelatedCourses, (val) => {
    form.related_course_ids = Array.isArray(val) ? val.map(v => v.id) : []
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

/** Отправка формы создания курса */
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

        return {
            ...data,
            difficulty,
            activity: data.activity ? 1 : 0,
            is_new: data.is_new ? 1 : 0,
            is_hit: data.is_hit ? 1 : 0,
            is_sale: data.is_sale ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,

            instructor_profile_id: selectedInstructorProfile.value?.id ?? null,
            learning_category_ids: Array.isArray(selectedLearningCategories.value)
                ? selectedLearningCategories.value.map(v => v.id)
                : [],
            hashtag_ids: Array.isArray(selectedHashtags.value)
                ? selectedHashtags.value.map(v => v.id)
                : [],
            related_course_ids: Array.isArray(selectedRelatedCourses.value)
                ? selectedRelatedCourses.value.map(v => v.id)
                : [],

            images: Array.isArray(newImages.value)
                ? newImages.value.map(img => ({
                    file: img.file,
                    order: img.order ?? 0,
                    alt: img.alt ?? '',
                    caption: img.caption ?? '',
                }))
                : [],
        }
    })

    form.post(route('admin.courses.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Курс успешно создан!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при создании курса:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createCourse')">
        <template #header>
            <TitlePage>{{ t('createCourse') }}</TitlePage>
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
                    <DefaultButton :href="route('admin.courses.index')">
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
                    class="p-3 w-full"
                >
                    <!-- Активность, локаль, сортировка -->
                    <div
                        class="mb-3 flex justify-between flex-col
                               lg:flex-row items-center gap-4"
                    >
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

                    <!-- Показывать в левой/главной/правой колонке -->
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

                    <!-- Новинки / Хиты / Распродажа -->
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

                    <!-- Уровень, Статус, Доступность -->
                    <div
                        class="mb-3 flex justify-between flex-col
                               lg:flex-row items-center gap-4"
                    >
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

                    <!-- Дата публикации, Сложность, Длительность -->
                    <div
                        class="mb-3 flex justify-between flex-col
                               lg:flex-row items-center gap-4"
                    >
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

                    <!-- Категории обучения -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="learning_categories"
                            :value="t('learningCategories')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="learning_categories"
                            v-model="selectedLearningCategories"
                            :options="learningCategoryOptions"
                            :options-limit="dynamicOptionsLimit(learningCategoryOptions)"
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
                            :message="form.errors.learning_category_ids"
                        />
                    </div>

                    <!-- Связь с профилем инструктора -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="instructor_profile"
                            :value="t('instructor')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="instructor_profile"
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
                            :message="form.errors.instructor_profile_id"
                        />
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
                            autocomplete="off"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.title"
                        />
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
                            autocomplete="off"
                            class="w-full"
                            @focus="handleSlugFocus"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.slug"
                        />
                    </div>

                    <!-- Подзаголовок / оффер -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="subtitle"
                                :value="t('subtitle')"
                            />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.subtitle.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea
                            v-model="form.subtitle"
                            class="w-full"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.subtitle"
                        />
                    </div>

                    <!-- Краткое описание -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="short"
                                :value="t('shortDescription')"
                            />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.short.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea
                            v-model="form.short"
                            class="w-full"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.short"
                        />
                    </div>

                    <!-- Описание курса -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="description"
                            :value="t('description')"
                        />
                        <TinyEditor
                            v-model="form.description"
                            :height="500"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.description"
                        />
                    </div>

                    <!-- Теги обучения -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="learning_tags"
                            :value="t('hashtags')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="learning_tags"
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

                    <!-- Рекомендованные курсы -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="related_courses"
                            :value="t('relatedCourses')"
                            class="mb-1"
                        />
                        <VueMultiselect
                        id="related_courses"
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

                    <!-- Мета Title -->
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
                            :href="route('admin.courses.index')"
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
