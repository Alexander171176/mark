<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Создание задания (SchoolAssignment)
 *
 * Новый Admin Create contract:
 * - selector props используют те же SharedResource, что Edit;
 * - form.*_id — единственный источник выбранных связей;
 * - selected* — только UI-объекты VueMultiselect;
 * - при пересборке локализованных options выбранные элементы
 *   восстанавливаются по ID без потери связи.
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
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

import SelectStatus from '@/Components/Admin/School/SchoolAssignment/Select/SelectStatus.vue'
import SelectVisibility from '@/Components/Admin/School/SchoolAssignment/Select/SelectVisibility.vue'
import SelectGradingType from '@/Components/Admin/School/SchoolAssignment/Select/SelectGradingType.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
    lessons: { type: Array, default: () => [] },
    instructors: { type: Array, default: () => [] },
})

/* ==========================================================
 * TRANSLATIONS
 * ========================================================== */

const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    instructions: '',
})

const defaultLocale =
    props.currentLocale
    || props.availableLocales?.[0]
    || 'ru'

const activeLocale = ref(defaultLocale)

/* ==========================================================
 * FORM
 * ========================================================== */

const form = useForm({
    school_course_id: null,
    school_module_id: null,
    school_lesson_id: null,
    school_instructor_profile_id: null,

    activity: true,
    left: false,
    main: false,
    right: false,

    sort: 0,
    slug: '',

    status: 'draft',
    visibility: 'enrolled',
    attempts_limit: 0,
    grading_type: 'manual',
    max_score: 100,

    published_at: '',
    due_at: '',

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/* ==========================================================
 * SELECT HELPERS
 * ========================================================== */

const dynamicOptionsLimit = (items) => {
    return Array.isArray(items) ? items.length + 10 : 10
}

const getTranslationForLocale = (entity) => {
    if (!entity) return null

    if (Array.isArray(entity.translations)) {
        const exact = entity.translations.find(
            translation => translation?.locale === activeLocale.value
        )

        if (exact) return exact
    }

    return entity.translation || null
}

const getEntityTitle = (entity) => {
    return getTranslationForLocale(entity)?.title
        || entity?.slug
        || `#${entity?.id ?? ''}`
}

const findOptionById = (options, id) => {
    if (id === null || id === undefined || id === '') {
        return null
    }

    return options.find(
        option => Number(option.id) === Number(id)
    ) || null
}

/* ==========================================================
 * SELECT OPTIONS — NEW CONTRACT
 * ========================================================== */

const courseOptions = computed(() => {
    return (props.courses || []).map((course) => ({
        id: course.id,
        label: `[ID: ${course.id}] ${getEntityTitle(course)}`,
    }))
})

const moduleOptions = computed(() => {
    return (props.modules || []).map((module) => {
        const moduleTitle = getEntityTitle(module)
        const courseTitle = module?.course
            ? getEntityTitle(module.course)
            : ''

        return {
            id: module.id,
            course_id:
                module.school_course_id
                ?? module.course?.id
                ?? null,
            label: courseTitle
                ? `[ID: ${module.id}] [${courseTitle}] ${moduleTitle}`
                : `[ID: ${module.id}] ${moduleTitle}`,
        }
    })
})

const lessonOptions = computed(() => {
    return (props.lessons || []).map((lesson) => {
        const lessonTitle = getEntityTitle(lesson)
        const module = lesson?.module || null
        const course = module?.course || lesson?.course || null
        const moduleTitle = module ? getEntityTitle(module) : ''
        const courseTitle = course ? getEntityTitle(course) : ''

        let label = lessonTitle

        if (courseTitle && moduleTitle) {
            label = `[${courseTitle}] [${moduleTitle}] ${lessonTitle}`
        } else if (moduleTitle) {
            label = `[${moduleTitle}] ${lessonTitle}`
        } else if (courseTitle) {
            label = `[${courseTitle}] ${lessonTitle}`
        }

        return {
            id: lesson.id,
            module_id:
                lesson.school_module_id
                ?? module?.id
                ?? null,
            course_id:
                course?.id
                ?? module?.school_course_id
                ?? null,
            label: `[ID: ${lesson.id}] ${label}`,
        }
    })
})

const instructorOptions = computed(() => {
    return (props.instructors || []).map((instructor) => {
        const profileTitle = getEntityTitle(instructor)
        const userName = instructor?.user?.name
            || instructor?.user?.email
            || ''

        return {
            id: instructor.id,
            label: userName && userName !== profileTitle
                ? `[ID: ${instructor.id}] ${profileTitle} — ${userName}`
                : `[ID: ${instructor.id}] ${profileTitle}`,
        }
    })
})

/* ==========================================================
 * SELECTED UI OBJECTS
 * ========================================================== */

const selectedCourse = ref(null)
const selectedModule = ref(null)
const selectedLesson = ref(null)
const selectedInstructor = ref(null)

const syncSelectedOptions = () => {
    selectedCourse.value = findOptionById(
        courseOptions.value,
        form.school_course_id
    )

    selectedModule.value = findOptionById(
        moduleOptions.value,
        form.school_module_id
    )

    selectedLesson.value = findOptionById(
        lessonOptions.value,
        form.school_lesson_id
    )

    selectedInstructor.value = findOptionById(
        instructorOptions.value,
        form.school_instructor_profile_id
    )
}

watch(
    [courseOptions, moduleOptions, lessonOptions, instructorOptions],
    syncSelectedOptions,
    { immediate: true }
)

watch(selectedCourse, (value) => {
    form.school_course_id = value?.id ?? null
})

watch(selectedModule, (value) => {
    form.school_module_id = value?.id ?? null

    if (!value?.course_id) return

    form.school_course_id = value.course_id
    selectedCourse.value = findOptionById(
        courseOptions.value,
        value.course_id
    )
})

watch(selectedLesson, (value) => {
    form.school_lesson_id = value?.id ?? null

    if (value?.module_id) {
        form.school_module_id = value.module_id
        selectedModule.value = findOptionById(
            moduleOptions.value,
            value.module_id
        )
    }

    if (value?.course_id) {
        form.school_course_id = value.course_id
        selectedCourse.value = findOptionById(
            courseOptions.value,
            value.course_id
        )
    }
})

watch(selectedInstructor, (value) => {
    form.school_instructor_profile_id = value?.id ?? null
})

/* ==========================================================
 * IMAGES
 * ========================================================== */

const newImages = ref([])

const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

/* ==========================================================
 * SLUG
 * ========================================================== */

const handleSlugFocus = () => {
    if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(
            currentTranslation.value.title.toLowerCase()
        )
    }
}

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const submit = () => {
    form.transform((data) => {
        const transformed = {
            ...data,

            school_course_id: data.school_course_id ?? null,
            school_module_id: data.school_module_id ?? null,
            school_lesson_id: data.school_lesson_id ?? null,
            school_instructor_profile_id:
                data.school_instructor_profile_id ?? null,

            activity: data.activity ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,

            sort:
                data.sort === '' || data.sort === null
                    ? 0
                    : Number(data.sort),

            attempts_limit:
                data.attempts_limit === '' || data.attempts_limit === null
                    ? 0
                    : Number(data.attempts_limit),

            max_score:
                data.max_score === '' || data.max_score === null
                    ? 100
                    : Number(data.max_score),
        }

        newImages.value.forEach((image, index) => {
            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
        })

        return transformed
    })

    form.post(
        route('admin.schoolAssignments.store'),
        {
            errorBag: 'createSchoolAssignment',
            preserveScroll: true,
            forceFormData: true,

            onSuccess: () => {
                toast.success('Задание успешно создано!')
            },

            onError: (errors) => {
                console.error(
                    'Ошибка создания задания:',
                    errors
                )

                const firstKey = Object.keys(errors || {})[0]

                toast.error(
                    errors?.[firstKey]
                    || 'Проверьте корректность полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('createAssignment')">
        <template #header>
            <TitlePage>{{ t('createAssignment') }}</TitlePage>
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
                    <DefaultButton :href="route('admin.schoolAssignments.index')">
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
                    @submit.prevent="submit"
                    enctype="multipart/form-data"
                    class="p-3 w-full"
                >
                    <div class="pb-12">

                        <!-- Активность, сортировка -->
                        <div class="mb-3 flex justify-between
                                    flex-col lg:flex-row items-center gap-4">
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

                        <!-- Показывать в левом сайдбаре, в центральной части, в правом сайдбаре -->
                        <div class="mb-3 flex justify-between
                                    flex-col lg:flex-row items-center gap-4">

                            <!-- Показывать в левом сайдбаре -->
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.left" />
                                <LabelCheckbox for="left" :text="t('left')"
                                               class="text-sm h-8 flex items-center" />
                            </div>

                            <!-- Показывать в главных новостях -->
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.main" />
                                <LabelCheckbox for="main" :text="t('main')"
                                               class="text-sm h-8 flex items-center" />
                            </div>

                            <!-- Показывать в правом сайдбаре -->
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.right" />
                                <LabelCheckbox for="right" :text="t('right')"
                                               class="text-sm h-8 flex items-center" />
                            </div>

                        </div>

                        <!-- Статус, Дата публикации, Доступность -->
                        <div class="mb-3 flex justify-between flex-col
                                lg:flex-row items-center gap-4">

                            <!-- Статус -->
                            <SelectStatus v-model="form.status"
                                          :errorMessage="form.errors.status" />

                            <!-- Дата публикации -->
                            <div class="flex flex-col items-start">
                                <LabelInput for="published_at"
                                            :value="t('publishedAt')" />
                                <InputText
                                    id="published_at"
                                    type="datetime-local"
                                    v-model="form.published_at"
                                    autocomplete="published_at"
                                    class="w-full max-w-56"
                                />
                                <InputError class="mt-1 sm:mt-0" :message="form.errors.published_at" />
                            </div>

                            <!-- Доступность -->
                            <SelectVisibility v-model="form.visibility"
                                              :errorMessage="form.errors.visibility" />

                        </div>

                        <!-- Параметры задания -->
                        <div class="mb-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                            <!-- Лимит попыток -->
                            <div class="flex flex-col items-start">
                                <LabelInput for="attempts_limit" :value="t('attemptsLimit')" />
                                <InputNumber
                                    id="attempts_limit"
                                    type="number"
                                    min="0"
                                    v-model="form.attempts_limit"
                                    autocomplete="attempts_limit"
                                    class="w-full"
                                />
                                <InputError class="mt-1" :message="form.errors.attempts_limit" />
                            </div>

                            <!-- Тип оценки -->
                            <SelectGradingType
                                v-model="form.grading_type"
                                :errorMessage="form.errors.grading_type"
                            />

                            <!-- Максимальный балл -->
                            <div class="flex flex-col items-start">
                                <LabelInput for="max_score" :value="t('maxScore')" />
                                <InputNumber
                                    id="max_score"
                                    type="number"
                                    min="0"
                                    v-model="form.max_score"
                                    autocomplete="max_score"
                                    class="w-full"
                                />
                                <InputError class="mt-1" :message="form.errors.max_score" />
                            </div>

                            <!-- Дедлайн -->
                            <div class="flex flex-col items-start">
                                <LabelInput for="due_at" :value="t('dueAt')" />
                                <InputText
                                    id="due_at"
                                    type="datetime-local"
                                    v-model="form.due_at"
                                    autocomplete="due_at"
                                    class="w-full"
                                />
                                <InputError class="mt-1" :message="form.errors.due_at" />
                            </div>

                        </div>

                        <!-- Преподаватель -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="instructor"
                                :value="t('instructor')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="instructor"
                                v-model="selectedInstructor"
                                :options="instructorOptions"
                                :options-limit="dynamicOptionsLimit(instructorOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />
                            <InputError class="mt-2"
                                        :message="form.errors.school_instructor_profile_id" />
                        </div>

                        <!-- Курс -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="course"
                                :value="`${t('course')} (${t('autoCorrect')})`"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="course"
                                v-model="selectedCourse"
                                :options="courseOptions"
                                :options-limit="dynamicOptionsLimit(courseOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.school_course_id" />
                        </div>

                        <!-- Модуль -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="module"
                                :value="`${t('module')} (${t('autoCorrect')})`"
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
                            <InputError class="mt-2" :message="form.errors.school_module_id" />
                        </div>

                        <!-- Урок -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="lesson"
                                :value="t('lesson')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="lesson"
                                v-model="selectedLesson"
                                :options="lessonOptions"
                                :options-limit="dynamicOptionsLimit(lessonOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.school_lesson_id" />
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

                            <!-- Название -->
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

                            <!-- Подзаголовок/оффер -->
                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="subtitle"
                                    :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                                />
                                <MetaDescTextarea v-model="currentTranslation.subtitle" class="w-full" />
                                <InputError class="mt-2" :message="getError('subtitle')" />
                            </div>

                            <!-- Краткое описание -->
                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="short"
                                    :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                                />
                                <MetaDescTextarea v-model="currentTranslation.short" class="w-full" />
                                <InputError class="mt-2" :message="getError('short')" />
                            </div>

                            <!-- Описание задания -->
                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="description"
                                    :value="`${t('description')} [${activeLocale.toUpperCase()}]`"
                                />
                                <TinyEditor
                                    v-model="currentTranslation.description"
                                    :height="500" />
                                <InputError class="mt-2" :message="getError('description')" />
                            </div>

                            <!-- Инструкции -->
                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="instructions"
                                    :value="`${t('instructions')} [${activeLocale.toUpperCase()}]`"
                                />
                                <TinyEditor
                                    v-model="currentTranslation.instructions"
                                    :height="400"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="getError('instructions')" />
                            </div>

                        </div>

                        <!-- Загрузка новых изображений -->
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

                    <!-- Кнопки сохранить/назад -->
                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton
                            :href="route('admin.schoolAssignments.index')"
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
