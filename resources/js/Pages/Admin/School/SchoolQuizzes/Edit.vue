<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование квиза школы
 */

import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/UI/Image/MultiImageEdit.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

import SelectQuizType from '@/Components/Admin/School/SchoolQuiz/Select/SelectQuizType.vue'

/* ==========================================================
 * ЛОКАЛИЗАЦИЯ / УВЕДОМЛЕНИЯ
 * ========================================================== */

const { t } = useI18n()
const toast = useToast()

/* ==========================================================
 * PROPS
 * ========================================================== */

const props = defineProps({
    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    quiz: {
        type: Object,
        required: true,
    },

    courses: {
        type: Array,
        default: () => [],
    },

    modules: {
        type: Array,
        default: () => [],
    },

    lessons: {
        type: Array,
        default: () => [],
    },
})

/* ==========================================================
 * ПЕРЕВОДЫ
 * ========================================================== */

/**
 * Пустой перевод.
 */
const makeTranslation = () => ({
    title: '',
    short: '',
    description: '',
})

/**
 * Формирование всех переводов квиза.
 *
 * SchoolQuizResource для Edit отдаёт
 * полную коллекцию translations.
 */
const buildTranslations = () => {
    const result = {}

    ;(props.quiz.translations || []).forEach(
        (translation) => {
            result[translation.locale] = {
                title:
                    translation.title || '',

                short:
                    translation.short || '',

                description:
                    translation.description || '',
            }
        }
    )

    const locale =
        props.currentLocale
        || props.quiz?.translation?.locale
        || props.availableLocales?.[0]
        || 'ru'

    if (!Object.keys(result).length) {
        result[locale] =
            makeTranslation()
    }

    if (!result[locale]) {
        result[locale] =
            makeTranslation()
    }

    return result
}

/* ==========================================================
 * ДАТА
 * ========================================================== */

/**
 * Преобразование ISO даты
 * в формат datetime-local.
 */
const normalizeDateTimeLocal = (value) => {
    if (!value) {
        return ''
    }

    const str = String(value)

    const match = str.match(
        /^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2})/
    )

    return match
        ? match[1]
        : str
}

/* ==========================================================
 * АКТИВНАЯ ЛОКАЛЬ
 * ========================================================== */

const defaultLocale =
    props.currentLocale
    || props.quiz?.translation?.locale
    || props.availableLocales?.[0]
    || 'ru'

const activeLocale = ref(
    defaultLocale
)

/**
 * Если Inertia обновляет страницу при смене
 * интерфейсной локали без полного remount,
 * синхронизируем активную вкладку перевода.
 */
watch(
    () => props.currentLocale,
    (locale) => {
        if (!locale) {
            return
        }

        activeLocale.value =
            locale

        if (!form.translations[locale]) {
            form.translations[locale] =
                makeTranslation()
        }
    }
)

/* ==========================================================
 * ФОРМА
 * ========================================================== */

const form = useForm({
    _method: 'PUT',

    school_course_id:
        props.quiz.school_course_id
        ?? props.quiz.course?.id
        ?? null,

    school_module_id:
        props.quiz.school_module_id
        ?? props.quiz.module?.id
        ?? null,

    school_lesson_id:
        props.quiz.school_lesson_id
        ?? props.quiz.lesson?.id
        ?? null,

    activity:
        Boolean(
            props.quiz.activity
        ),

    left:
        Boolean(
            props.quiz.left
        ),

    main:
        Boolean(
            props.quiz.main
        ),

    right:
        Boolean(
            props.quiz.right
        ),

    sort:
        props.quiz.sort ?? 0,

    slug:
        props.quiz.slug ?? '',

    type:
        props.quiz.type ?? 'graded',

    attempts_limit:
        props.quiz.attempts_limit ?? 0,

    time_limit_minutes:
        props.quiz.time_limit_minutes ?? null,

    pass_score:
        props.quiz.pass_score ?? 70,

    published_at:
        normalizeDateTimeLocal(
            props.quiz.published_at
        ),

    translations:
        buildTranslations(),

    deletedImages: [],
})

/**
 * Текущий активный перевод.
 */
const currentTranslation = computed(
    () => {
        if (
            !form.translations[
                activeLocale.value
                ]
        ) {
            form.translations[
                activeLocale.value
                ] = makeTranslation()
        }

        return form.translations[
            activeLocale.value
            ]
    }
)

/**
 * Заголовок страницы.
 */
const pageTitle = computed(() => {
    return currentTranslation.value.title
        || props.quiz?.translation?.title
        || `ID: ${props.quiz.id}`
})

/**
 * Ошибка поля текущей локали.
 */
const getError = (key) => {
    return form.errors[
        `translations.${activeLocale.value}.${key}`
        ]
}

/* ==========================================================
 * MULTISELECT
 * ========================================================== */

/**
 * Динамический лимит VueMultiselect.
 */
const dynamicOptionsLimit = (items) => {
    if (!items) {
        return 10
    }

    return items.length + 10
}

/* ==========================================================
 * КУРСЫ
 * ========================================================== */

/**
 * Controller загружает только текущую locale:
 *
 * course.translations(locale)
 *
 * SchoolCourseSharedResource формирует:
 *
 * course.translation.title
 */
const courseOptions = computed(() =>
    props.courses.map(
        (course) => ({
            id: course.id,

            label:
                `[ID: ${course.id}] ${
                    course?.translation?.title
                    || course?.slug
                    || `#${course.id}`
                }`,
        })
    )
)

/* ==========================================================
 * МОДУЛИ
 * ========================================================== */

const moduleOptions = computed(() =>
    props.modules.map(
        (module) => {
            const moduleTitle =
                module?.translation?.title
                || module?.slug
                || `#${module.id}`

            const courseTitle =
                module?.course?.translation?.title
                || module?.course?.slug
                || ''

            return {
                id: module.id,

                label: courseTitle
                    ? `[ID: ${module.id}] [${courseTitle}] ${moduleTitle}`
                    : `[ID: ${module.id}] ${moduleTitle}`,
            }
        }
    )
)

/* ==========================================================
 * УРОКИ
 * ========================================================== */

const lessonOptions = computed(() =>
    props.lessons.map(
        (lesson) => {
            const lessonTitle =
                lesson?.translation?.title
                || lesson?.slug
                || `#${lesson.id}`

            const moduleTitle =
                lesson?.module?.translation?.title
                || lesson?.module?.slug
                || ''

            const courseTitle =
                lesson?.module?.course
                    ?.translation?.title
                || lesson?.module?.course?.slug
                || ''

            let label =
                lessonTitle

            if (
                courseTitle
                && moduleTitle
            ) {
                label =
                    `[${courseTitle}] [${moduleTitle}] ${lessonTitle}`
            } else if (moduleTitle) {
                label =
                    `[${moduleTitle}] ${lessonTitle}`
            }

            return {
                id: lesson.id,

                label:
                    `[ID: ${lesson.id}] ${label}`,
            }
        }
    )
)

/* ==========================================================
 * ВЫБРАННЫЕ COURSE / MODULE / LESSON
 * ========================================================== */

/**
 * Важно:
 *
 * selectedCourse больше не хранится
 * отдельным ref.
 *
 * Источник истины только:
 *
 * form.school_course_id
 *
 * Поэтому при смене locale courseOptions
 * пересчитывается и Multiselect автоматически
 * получает новый локализованный label.
 */
const selectedCourse = computed({
    get: () => {
        if (!form.school_course_id) {
            return null
        }

        return courseOptions.value.find(
            (course) =>
                Number(course.id)
                === Number(
                    form.school_course_id
                )
        ) || null
    },

    set: (value) => {
        form.school_course_id =
            value?.id ?? null
    },
})

/**
 * Выбранный модуль.
 */
const selectedModule = computed({
    get: () => {
        if (!form.school_module_id) {
            return null
        }

        return moduleOptions.value.find(
            (module) =>
                Number(module.id)
                === Number(
                    form.school_module_id
                )
        ) || null
    },

    set: (value) => {
        form.school_module_id =
            value?.id ?? null
    },
})

/**
 * Выбранный урок.
 */
const selectedLesson = computed({
    get: () => {
        if (!form.school_lesson_id) {
            return null
        }

        return lessonOptions.value.find(
            (lesson) =>
                Number(lesson.id)
                === Number(
                    form.school_lesson_id
                )
        ) || null
    },

    set: (value) => {
        form.school_lesson_id =
            value?.id ?? null
    },
})

/* ==========================================================
 * ИСХОДНЫЕ СВЯЗАННЫЕ СУЩНОСТИ
 * ========================================================== */

/**
 * Здесь используем исходные props,
 * а не сокращённые Multiselect options,
 * потому что для автосинхронизации нужны
 * school_course_id / school_module_id
 * и вложенные relations.
 */
const selectedModuleEntity = computed(
    () => {
        if (!form.school_module_id) {
            return null
        }

        return props.modules.find(
            (module) =>
                Number(module.id)
                === Number(
                    form.school_module_id
                )
        ) || null
    }
)

const selectedLessonEntity = computed(
    () => {
        if (!form.school_lesson_id) {
            return null
        }

        return props.lessons.find(
            (lesson) =>
                Number(lesson.id)
                === Number(
                    form.school_lesson_id
                )
        ) || null
    }
)

/* ==========================================================
 * АВТОСИНХРОНИЗАЦИЯ ИЕРАРХИИ
 * ========================================================== */

/**
 * Урок является самым глубоким уровнем.
 *
 * При выборе урока автоматически
 * определяем его модуль и курс.
 */
watch(
    () => form.school_lesson_id,
    (lessonId) => {
        if (!lessonId) {
            return
        }

        const lesson =
            selectedLessonEntity.value

        if (!lesson) {
            return
        }

        /* --------------------------
         * Модуль урока
         * -------------------------- */

        const moduleId =
            lesson.school_module_id
            ?? lesson.module_id
            ?? lesson.module?.id
            ?? null

        if (moduleId) {
            const normalizedModuleId =
                Number(moduleId)

            if (
                Number(
                    form.school_module_id
                ) !== normalizedModuleId
            ) {
                const hadModule =
                    Boolean(
                        form.school_module_id
                    )

                form.school_module_id =
                    normalizedModuleId

                if (hadModule) {
                    toast.info(
                        'Модуль автоматически подставлен из выбранного урока'
                    )
                }
            }
        }

        /* --------------------------
         * Курс урока
         * -------------------------- */

        const courseId =
            lesson.module?.school_course_id
            ?? lesson.module?.course_id
            ?? lesson.module?.course?.id
            ?? null

        if (courseId) {
            const normalizedCourseId =
                Number(courseId)

            if (
                Number(
                    form.school_course_id
                ) !== normalizedCourseId
            ) {
                const hadCourse =
                    Boolean(
                        form.school_course_id
                    )

                form.school_course_id =
                    normalizedCourseId

                if (hadCourse) {
                    toast.info(
                        'Курс автоматически подставлен из выбранного урока'
                    )
                }
            }
        }
    }
)

/**
 * При выборе модуля:
 *
 * 1. автоматически определяем курс;
 * 2. если выбранный урок относится
 *    к другому модулю — сбрасываем урок.
 */
watch(
    () => form.school_module_id,
    (moduleId) => {
        if (!moduleId) {
            /**
             * Без модуля выбранный урок
             * больше не может считаться
             * согласованным.
             */
            if (form.school_lesson_id) {
                form.school_lesson_id =
                    null
            }

            return
        }

        const module =
            selectedModuleEntity.value

        if (!module) {
            return
        }

        /* --------------------------
         * Курс модуля
         * -------------------------- */

        const courseId =
            module.school_course_id
            ?? module.course_id
            ?? module.course?.id
            ?? null

        if (courseId) {
            const normalizedCourseId =
                Number(courseId)

            if (
                Number(
                    form.school_course_id
                ) !== normalizedCourseId
            ) {
                const hadCourse =
                    Boolean(
                        form.school_course_id
                    )

                form.school_course_id =
                    normalizedCourseId

                if (hadCourse) {
                    toast.info(
                        'Курс автоматически подставлен из выбранного модуля'
                    )
                }
            }
        }

        /* --------------------------
         * Проверяем урок
         * -------------------------- */

        if (form.school_lesson_id) {
            const lesson =
                selectedLessonEntity.value

            const lessonModuleId =
                lesson?.school_module_id
                ?? lesson?.module_id
                ?? lesson?.module?.id
                ?? null

            if (
                lesson
                && lessonModuleId
                && Number(lessonModuleId)
                !== Number(moduleId)
            ) {
                form.school_lesson_id =
                    null

                toast.info(
                    'Урок сброшен, так как не относится к выбранному модулю'
                )
            }
        }
    }
)

/**
 * При ручной смене курса проверяем,
 * соответствуют ли ему выбранные
 * модуль и урок.
 */
watch(
    () => form.school_course_id,
    (courseId) => {
        /**
         * Если курс очищен —
         * очищаем и всю вложенную иерархию.
         */
        if (!courseId) {
            form.school_module_id =
                null

            form.school_lesson_id =
                null

            return
        }

        /* --------------------------
         * Проверяем модуль
         * -------------------------- */

        if (form.school_module_id) {
            const module =
                selectedModuleEntity.value

            const moduleCourseId =
                module?.school_course_id
                ?? module?.course_id
                ?? module?.course?.id
                ?? null

            if (
                moduleCourseId
                && Number(moduleCourseId)
                !== Number(courseId)
            ) {
                form.school_module_id =
                    null

                form.school_lesson_id =
                    null

                toast.info(
                    'Модуль и урок сброшены: они не относятся к выбранному курсу'
                )

                return
            }
        }

        /* --------------------------
         * Проверяем урок
         * -------------------------- */

        if (form.school_lesson_id) {
            const lesson =
                selectedLessonEntity.value

            const lessonCourseId =
                lesson?.module
                    ?.school_course_id
                ?? lesson?.module
                    ?.course_id
                ?? lesson?.module
                    ?.course?.id
                ?? null

            if (
                lessonCourseId
                && Number(lessonCourseId)
                !== Number(courseId)
            ) {
                form.school_lesson_id =
                    null

                toast.info(
                    'Урок сброшен: он не относится к выбранному курсу'
                )
            }
        }
    }
)

/* ==========================================================
 * ИЗОБРАЖЕНИЯ
 * ========================================================== */

/**
 * Существующие изображения квиза.
 *
 * Controller загрузил images.media,
 * поэтому URL уже сформированы Resource.
 */
const existingImages = ref(
    (props.quiz.images || [])
        .filter(
            (image) =>
                image.webp_url
                || image.url
                || image.image_url
        )
        .map(
            (image) => ({
                id:
                image.id,

                url:
                    image.webp_url
                    || image.url
                    || image.image_url,

                order:
                    image.order ?? 0,

                alt:
                    image.alt || '',

                caption:
                    image.caption || '',
            })
        )
)

/**
 * Новые изображения.
 */
const newImages = ref([])

/**
 * Обновление существующих изображений.
 */
const handleExistingImagesUpdate = (
    images
) => {
    existingImages.value =
        images || []
}

/**
 * Удаление существующего изображения.
 */
const handleDeleteExistingImage = (
    deletedId
) => {
    if (
        !form.deletedImages.includes(
            deletedId
        )
    ) {
        form.deletedImages.push(
            deletedId
        )
    }

    existingImages.value =
        existingImages.value.filter(
            (image) =>
                image.id !== deletedId
        )
}

/**
 * Обновление новых изображений.
 */
const handleNewImagesUpdate = (
    images
) => {
    newImages.value =
        images || []
}

/* ==========================================================
 * SLUG
 * ========================================================== */

/**
 * Генерация slug из заголовка
 * текущей активной локали.
 */
const handleSlugFocus = () => {
    if (
        !form.slug
        && currentTranslation.value.title
    ) {
        form.slug = transliterate(
            currentTranslation.value.title
                .toLowerCase()
        )
    }
}

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const submitForm = () => {
    form.transform(
        (data) => {
            const transformed = {
                ...data,

                school_course_id:
                    data.school_course_id
                    || null,

                school_module_id:
                    data.school_module_id
                    || null,

                school_lesson_id:
                    data.school_lesson_id
                    || null,

                attempts_limit:
                    Number(
                        data.attempts_limit
                        || 0
                    ),

                time_limit_minutes:
                    data.time_limit_minutes === ''
                    || data.time_limit_minutes === null
                        ? null
                        : Number(
                            data.time_limit_minutes
                        ),

                pass_score:
                    Number(
                        data.pass_score
                        || 0
                    ),

                sort:
                    Number(
                        data.sort
                        || 0
                    ),

                activity:
                    data.activity
                        ? 1
                        : 0,

                left:
                    data.left
                        ? 1
                        : 0,

                main:
                    data.main
                        ? 1
                        : 0,

                right:
                    data.right
                        ? 1
                        : 0,
            }

            /**
             * Массив изображений собираем
             * вручную для multipart/form-data.
             */
            delete transformed.images
            delete transformed.deletedImages

            let index = 0

            /* --------------------------
             * Существующие изображения
             * -------------------------- */

            existingImages.value.forEach(
                (image) => {
                    transformed[
                        `images[${index}][id]`
                        ] = image.id

                    transformed[
                        `images[${index}][order]`
                        ] = image.order ?? 0

                    transformed[
                        `images[${index}][alt]`
                        ] = image.alt ?? ''

                    transformed[
                        `images[${index}][caption]`
                        ] = image.caption ?? ''

                    index++
                }
            )

            /* --------------------------
             * Новые изображения
             * -------------------------- */

            newImages.value.forEach(
                (image) => {
                    transformed[
                        `images[${index}][file]`
                        ] = image.file

                    transformed[
                        `images[${index}][order]`
                        ] = image.order ?? 0

                    transformed[
                        `images[${index}][alt]`
                        ] = image.alt ?? ''

                    transformed[
                        `images[${index}][caption]`
                        ] = image.caption ?? ''

                    index++
                }
            )

            /* --------------------------
             * Удалённые изображения
             * -------------------------- */

            form.deletedImages.forEach(
                (id, deletedIndex) => {
                    transformed[
                        `deletedImages[${deletedIndex}]`
                        ] = id
                }
            )

            return transformed
        }
    )

    form.post(
        route(
            'admin.schoolQuizzes.update',
            {
                schoolQuiz:
                props.quiz.id,
            }
        ),
        {
            errorBag:
                'editSchoolQuiz',

            preserveScroll:
                true,

            forceFormData:
                true,

            onSuccess: () => {
                toast.success(
                    'Квиз успешно обновлён!'
                )

                newImages.value = []

                form.deletedImages = []
            },

            onError: (errors) => {
                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors[firstKey]
                    || 'Проверьте корректность полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('editQuiz')">
        <template #header>
            <TitlePage>
                {{ t('editQuiz') }}:
                {{ pageTitle }}
                [ID: {{ quiz.id }}]
            </TitlePage>
        </template>

        <div
            class="px-4 sm:px-6 lg:px-8 py-8
                   w-full max-w-12xl mx-auto"
        >
            <div
                class="p-4
                       bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div
                    class="sm:flex sm:justify-between
                           sm:items-center mb-2"
                >
                    <DefaultButton
                        :href="route('admin.schoolQuizzes.index')"
                    >
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current
                                       text-slate-100
                                       shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0
                                       .7.7 1.2 1.7 1.4 2.7l2-.3
                                       c-.2-1.5-.9-2.8-1.9-3.8
                                       C10.1.4 5.7.4 2.9 3.1
                                       L.7.9 0 7.3l6.4-.7-2.1-2.1z
                                       M15.6 8.7l-6.4.7 2.1 2.1
                                       c-1.9 1.9-5.1 1.9-7 0
                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3
                                       c.2 1.5.9 2.8 1.9 3.8
                                       1.4 1.4 3.1 2 4.9 2
                                       1.8 0 3.6-.7 4.9-2
                                       l2.2 2.2.8-6.4z"
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

                        <!-- ==================================================
                             ACTIVITY / SORT
                             ================================================== -->

                        <div
                            class="mb-3 flex justify-between
                                   flex-col lg:flex-row
                                   items-center gap-4"
                        >
                            <div
                                class="flex flex-row
                                       items-center gap-2"
                            >
                                <ActivityCheckbox
                                    v-model="form.activity"
                                />

                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div
                                class="flex flex-row
                                       items-center gap-2"
                            >
                                <LabelInput
                                    for="sort"
                                    :value="t('sort')"
                                    class="text-sm"
                                />

                                <InputNumber
                                    id="sort"
                                    type="number"
                                    v-model.number="form.sort"
                                    class="w-full lg:w-28"
                                />

                                <InputError
                                    :message="form.errors.sort"
                                />
                            </div>
                        </div>

                        <!-- ==================================================
                             POSITION FLAGS
                             ================================================== -->

                        <div
                            class="mb-3 flex justify-between
                                   flex-col lg:flex-row
                                   items-center gap-4"
                        >
                            <div
                                class="flex flex-row
                                       items-center gap-2"
                            >
                                <ActivityCheckbox
                                    v-model="form.left"
                                />

                                <LabelCheckbox
                                    for="left"
                                    :text="t('left')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div
                                class="flex flex-row
                                       items-center gap-2"
                            >
                                <ActivityCheckbox
                                    v-model="form.main"
                                />

                                <LabelCheckbox
                                    for="main"
                                    :text="t('main')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div
                                class="flex flex-row
                                       items-center gap-2"
                            >
                                <ActivityCheckbox
                                    v-model="form.right"
                                />

                                <LabelCheckbox
                                    for="right"
                                    :text="t('right')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>
                        </div>

                        <!-- ==================================================
                             TYPE / PUBLISHED
                             ================================================== -->

                        <div
                            class="mb-3 flex justify-between
                                   flex-col lg:flex-row
                                   items-center gap-4"
                        >
                            <SelectQuizType
                                v-model="form.type"
                                :errorMessage="form.errors.type"
                            />

                            <div
                                class="flex flex-col
                                       items-start"
                            >
                                <LabelInput
                                    for="published_at"
                                    :value="t('publishedAt')"
                                />

                                <InputText
                                    id="published_at"
                                    type="datetime-local"
                                    v-model="form.published_at"
                                    class="w-full max-w-56"
                                />

                                <InputError
                                    :message="form.errors.published_at"
                                />
                            </div>
                        </div>

                        <!-- ==================================================
                             LIMITS
                             ================================================== -->

                        <div
                            class="mb-3 grid
                                   grid-cols-1
                                   md:grid-cols-3
                                   gap-4"
                        >
                            <div
                                class="flex flex-col
                                       items-start"
                            >
                                <LabelInput
                                    for="attempts_limit"
                                    :value="t('limitCount')"
                                />

                                <InputNumber
                                    id="attempts_limit"
                                    type="number"
                                    min="0"
                                    v-model.number="form.attempts_limit"
                                    class="w-full"
                                />

                                <InputError
                                    :message="form.errors.attempts_limit"
                                />
                            </div>

                            <div
                                class="flex flex-col
                                       items-start"
                            >
                                <LabelInput
                                    for="time_limit_minutes"
                                    :value="t('limitMinutes')"
                                />

                                <InputNumber
                                    id="time_limit_minutes"
                                    type="number"
                                    min="1"
                                    v-model.number="form.time_limit_minutes"
                                    class="w-full"
                                />

                                <InputError
                                    :message="form.errors.time_limit_minutes"
                                />
                            </div>

                            <div
                                class="flex flex-col
                                       items-start"
                            >
                                <LabelInput
                                    for="pass_score"
                                    :value="t('passScore')"
                                />

                                <InputNumber
                                    id="pass_score"
                                    type="number"
                                    min="0"
                                    max="100"
                                    v-model.number="form.pass_score"
                                    class="w-full"
                                />

                                <InputError
                                    :message="form.errors.pass_score"
                                />
                            </div>
                        </div>

                        <!-- ==================================================
                             COURSE
                             ================================================== -->

                        <div
                            class="mb-3 flex flex-col
                                   items-start"
                        >
                            <LabelInput
                                for="school_course_id"
                                :value="t('course')"
                                class="mb-1"
                            />

                            <VueMultiselect
                                id="school_course_id"
                                v-model="selectedCourse"
                                :options="courseOptions"
                                :options-limit="dynamicOptionsLimit(courseOptions)"
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
                                :message="form.errors.school_course_id"
                            />
                        </div>

                        <!-- ==================================================
                             MODULE
                             ================================================== -->

                        <div
                            class="mb-3 flex flex-col
                                   items-start"
                        >
                            <LabelInput
                                for="school_module_id"
                                :value="t('module')"
                                class="mb-1"
                            />

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

                            <InputError
                                class="mt-2"
                                :message="form.errors.school_module_id"
                            />
                        </div>

                        <!-- ==================================================
                             LESSON
                             ================================================== -->

                        <div
                            class="mb-3 flex flex-col
                                   items-start"
                        >
                            <LabelInput
                                for="school_lesson_id"
                                :value="t('lesson')"
                                class="mb-1"
                            />

                            <VueMultiselect
                                id="school_lesson_id"
                                v-model="selectedLesson"
                                :options="lessonOptions"
                                :options-limit="dynamicOptionsLimit(lessonOptions)"
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
                                :message="form.errors.school_lesson_id"
                            />
                        </div>

                        <!-- ==================================================
                             SLUG
                             ================================================== -->

                        <div
                            class="mb-3 flex flex-col
                                   items-start"
                        >
                            <LabelInput for="slug">
                                <span
                                    class="text-red-500
                                           dark:text-red-300
                                           font-semibold"
                                >
                                    *
                                </span>

                                {{ t('slug') }}
                            </LabelInput>

                            <InputText
                                id="slug"
                                type="text"
                                v-model="form.slug"
                                class="w-full"
                                autocomplete="slug"
                                @focus="handleSlugFocus"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.slug"
                            />
                        </div>

                        <!-- ==================================================
                             TRANSLATION TABS
                             ================================================== -->

                        <TranslationTabs
                            v-model="activeLocale"
                            :translations="form.translations"
                            :available-locales="availableLocales"
                            :make-translation="makeTranslation"
                            @update:translations="form.translations = $event"
                            @removed="toast.warning('Перевод удалён.')"
                            @added="toast.success('Локаль добавлена.')"
                        />

                        <!-- ==================================================
                             TITLE
                             ================================================== -->

                        <div
                            class="mb-3 flex flex-col
                                   items-start"
                        >
                            <LabelInput for="title">
                                <span
                                    class="text-red-500
                                           dark:text-red-300
                                           font-semibold"
                                >
                                    *
                                </span>

                                {{ t('title') }}
                                [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText
                                id="title"
                                type="text"
                                v-model="currentTranslation.title"
                                class="w-full"
                            />

                            <InputError
                                class="mt-2"
                                :message="getError('title')"
                            />
                        </div>

                        <!-- ==================================================
                             SHORT
                             ================================================== -->

                        <div
                            class="mb-3 flex flex-col
                                   items-start"
                        >
                            <LabelInput
                                for="short"
                                :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                            />

                            <MetaDescTextarea
                                id="short"
                                v-model="currentTranslation.short"
                                class="w-full"
                            />

                            <InputError
                                class="mt-2"
                                :message="getError('short')"
                            />
                        </div>

                        <!-- ==================================================
                             DESCRIPTION
                             ================================================== -->

                        <div
                            class="mb-3 flex flex-col
                                   items-start"
                        >
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

                        <!-- ==================================================
                             EXISTING IMAGES
                             ================================================== -->

                        <div class="mt-4">
                            <MultiImageEdit
                                :images="existingImages"
                                @update:images="handleExistingImagesUpdate"
                                @delete-image="handleDeleteExistingImage"
                            />
                        </div>

                        <!-- ==================================================
                             NEW IMAGES
                             ================================================== -->

                        <div class="mt-4">
                            <MultiImageUpload
                                v-model:images="newImages"
                                @update:images="handleNewImagesUpdate"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.images"
                            />
                        </div>
                    </div>

                    <!-- ======================================================
                         BUTTONS
                         ====================================================== -->

                    <div
                        class="flex items-center
                               justify-center mt-4"
                    >
                        <DefaultButton
                            :href="route('admin.schoolQuizzes.index')"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current
                                           text-slate-100
                                           shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0
                                           .7.7 1.2 1.7 1.4 2.7l2-.3
                                           c-.2-1.5-.9-2.8-1.9-3.8
                                           C10.1.4 5.7.4 2.9 3.1
                                           L.7.9 0 7.3l6.4-.7-2.1-2.1z
                                           M15.6 8.7l-6.4.7 2.1 2.1
                                           c-1.9 1.9-5.1 1.9-7 0
                                           -.7-.7-1.2-1.7-1.4-2.7l-2 .3
                                           c.2 1.5.9 2.8 1.9 3.8
                                           1.4 1.4 3.1 2 4.9 2
                                           1.8 0 3.6-.7 4.9-2
                                           l2.2 2.2.8-6.4z"
                                    />
                                </svg>
                            </template>

                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4 mb-0"
                            :class="{
                                'opacity-25':
                                    form.processing
                            }"
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
