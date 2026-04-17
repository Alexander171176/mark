<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Создание викторины (Quiz)
 */
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import SelectQuizType from '@/Components/Admin/Quiz/Select/SelectQuizType.vue'

import VueMultiselect from 'vue-multiselect'
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'

// --- i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из QuizController@create:
 *  - courses
 *  - modules
 *  - lessons
 *  - currentLocale
 */
const props = defineProps({
    currentLocale: { type: String, default: 'ru' },
    courses: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
    lessons: { type: Array, default: () => [] },
})

/**
 * Форма создания квиза
 */
const form = useForm({
    course_id: null,
    module_id: null,
    lesson_id: null,

    activity: true,
    left: false,
    main: false,
    right: false,
    sort: 0,
    locale: props.currentLocale || 'ru',
    title: '',
    slug: '',
    short: '',
    description: '',

    // Параметры квиза
    type: '',
    attempts_limit: 0,
    time_limit_minutes: 0,
    pass_score: 0,

    published_at: '',
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

/** 🔹 Опции селекта курса (один курс) */
const courseOptions = computed(() =>
    props.courses.map(c => {
        const title = c.title || c.slug || `#${c.id}`

        return {
            id: c.id,
            label: `[ID: ${c.id}] [${c.locale}] ${title}`,
        }
    })
)

/** 🔹 Один выбранный курс  */
const selectedCourse = ref(null)

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

/** 🔹 Опции селекта урока */
const lessonOptions = computed(() =>
    props.lessons.map(l => {
        const lessonTitle = l.title || l.slug || `#${l.id}`
        const moduleTitle = l.module?.title ?? null
        const courseTitle = l.module?.course?.title ?? null
        const locale = l.locale || '—'

        let labelCore = lessonTitle

        if (courseTitle && moduleTitle) {
            labelCore = `[${courseTitle}] [${moduleTitle}] ${lessonTitle}`
        } else if (moduleTitle) {
            labelCore = `[${moduleTitle}] ${lessonTitle}`
        }

        return {
            id: l.id,
            label: `[ID: ${l.id}] [${locale}] ${labelCore}`,
        }
    })
)

/** 🔹 Один выбранный урок */
const selectedLesson = ref(null)

/* ============================================================
   Связанные сущности (по form.*_id)
   ============================================================ */

/** ✅ Выбранный модуль (по form.module_id) */
const selectedModuleEntity = computed(() =>
    props.modules.find(m => Number(m.id) === Number(form.module_id))
)

/** ✅ Выбранный урок (по form.lesson_id) */
const selectedLessonEntity = computed(() =>
    props.lessons.find(l => Number(l.id) === Number(form.lesson_id))
)

/* ============================================================
   Флаги, чтобы избежать "зацикливания" watchers
   ============================================================ */
const isAutoSyncFromLesson = ref(false)
const isAutoSyncFromModule = ref(false)
const isAutoSyncFromCourse = ref(false)

/* ============================================================
   Синхронизация VueMultiselect -> form.*_id
   ============================================================ */

/** 🔹 Курс: multiselect -> form.course_id */
watch(selectedCourse, (val) => {
    form.course_id = val ? val.id : null
})

/** 🔹 Модуль: multiselect -> form.module_id */
watch(selectedModule, (val) => {
    form.module_id = val ? val.id : null
})

/** 🔹 Урок: multiselect -> form.lesson_id */
watch(selectedLesson, (val) => {
    form.lesson_id = val ? val.id : null
})

/* ============================================================
   Автологика
   ============================================================ */

/**
 * ✅ 1) При выборе урока — автоподставляем module_id и course_id
 */
watch(
    () => form.lesson_id,
    () => {
        if (isAutoSyncFromCourse.value || isAutoSyncFromModule.value) return

        const lesson = selectedLessonEntity.value

        // если урок сбросили — ничего не трогаем
        if (!lesson) return

        isAutoSyncFromLesson.value = true

        // 1) module_id из урока
        if (lesson.module_id) {
            const newModuleId = Number(lesson.module_id)

            if (!form.module_id) {
                form.module_id = newModuleId
            } else if (Number(form.module_id) !== newModuleId) {
                form.module_id = newModuleId
                toast.info('Модуль автоматически подставлен из выбранного урока')
            }
        }

        // 2) course_id из урока (через module.course_id или module.course.id)
        const courseIdFromLesson =
            lesson.module?.course_id ?? lesson.module?.course?.id ?? null

        if (courseIdFromLesson) {
            const newCourseId = Number(courseIdFromLesson)

            if (!form.course_id) {
                form.course_id = newCourseId
            } else if (Number(form.course_id) !== newCourseId) {
                form.course_id = newCourseId
                toast.info('Курс автоматически подставлен из выбранного урока')
            }
        }

        // 3) синхронизируем multiselect-объекты, чтобы UI обновился
        if (form.module_id) {
            selectedModule.value =
                moduleOptions.value.find(m => Number(m.id) === Number(form.module_id)) || null
        }

        if (form.course_id) {
            selectedCourse.value =
                courseOptions.value.find(c => Number(c.id) === Number(form.course_id)) || null
        }

        isAutoSyncFromLesson.value = false
    }
)

/**
 * ✅ 2) При выборе модуля — автоподставляем курс
 */
watch(
    () => form.module_id,
    () => {
        if (isAutoSyncFromLesson.value || isAutoSyncFromCourse.value) return

        const module = selectedModuleEntity.value

        // если модуль сбросили — ничего не трогаем
        if (!module) return

        isAutoSyncFromModule.value = true

        // 1) course_id из модуля
        const courseIdFromModule = module.course_id ?? module.course?.id ?? null

        if (courseIdFromModule) {
            const newCourseId = Number(courseIdFromModule)

            if (!form.course_id) {
                form.course_id = newCourseId
            } else if (Number(form.course_id) !== newCourseId) {
                form.course_id = newCourseId
                toast.info('Курс автоматически подставлен из выбранного модуля')
            }

            // UI-синк course multiselect
            selectedCourse.value =
                courseOptions.value.find(c => Number(c.id) === Number(form.course_id)) || null
        }

        // 2) (опционально) если выбран урок и он не из этого модуля — сбрасываем урок
        if (form.lesson_id) {
            const lesson = selectedLessonEntity.value
            if (lesson && Number(lesson.module_id) !== Number(form.module_id)) {
                form.lesson_id = null
                selectedLesson.value = null
                toast.info('Урок сброшен, так как не относится к выбранному модулю')
            }
        }

        isAutoSyncFromModule.value = false
    }
)

/**
 * ✅ 3) При ручной смене курса — (опционально) сбрасываем модуль/урок,
 * если они не из этого курса.
 *
 * Важно: это делаем только при смене курса пользователем,
 * а не когда курс подставился автоматически из урока/модуля.
 */
watch(
    () => form.course_id,
    (newVal, oldVal) => {
        // если курс менялся авто-логикой — не считаем это "ручной сменой"
        if (isAutoSyncFromLesson.value || isAutoSyncFromModule.value) return

        // если реально изменили (и старое значение было)
        if (!newVal || Number(newVal) === Number(oldVal)) {
            // если очистили курс вручную — сбрасываем всё ниже
            if (!newVal) {
                isAutoSyncFromCourse.value = true
                form.module_id = null
                form.lesson_id = null
                selectedModule.value = null
                selectedLesson.value = null
                isAutoSyncFromCourse.value = false
            }
            return
        }

        isAutoSyncFromCourse.value = true

        // 1) если выбран модуль и он не из нового курса — сбрасываем модуль + урок
        if (form.module_id) {
            const module = selectedModuleEntity.value
            const moduleCourseId = module?.course_id ?? module?.course?.id ?? null

            if (moduleCourseId && Number(moduleCourseId) !== Number(newVal)) {
                form.module_id = null
                selectedModule.value = null

                // при смене курса логично сбросить и урок
                form.lesson_id = null
                selectedLesson.value = null

                toast.info('Модуль и урок сброшены: они не относятся к выбранному курсу')
            }
        }

        // 2) если выбран урок и он не из нового курса — сбрасываем урок
        if (form.lesson_id) {
            const lesson = selectedLessonEntity.value
            const lessonCourseId =
                lesson?.module?.course_id ?? lesson?.module?.course?.id ?? null

            if (lessonCourseId && Number(lessonCourseId) !== Number(newVal)) {
                form.lesson_id = null
                selectedLesson.value = null
                toast.info('Урок сброшен: он не относится к выбранному курсу')
            }
        }

        isAutoSyncFromCourse.value = false
    }
)

/**
 * Чтобы при "программном" изменении form.*_id (автологика)
 * multiselect всегда показывал выбранные объекты — держим UI в синхроне.
 * (особенно полезно, если form.*_id меняется не через multiselect)
 */
watch(
    () => form.course_id,
    () => {
        if (!form.course_id) {
            selectedCourse.value = null
            return
        }
        selectedCourse.value =
            courseOptions.value.find(c => Number(c.id) === Number(form.course_id)) || null
    }
)

watch(
    () => form.module_id,
    () => {
        if (!form.module_id) {
            selectedModule.value = null
            return
        }
        selectedModule.value =
            moduleOptions.value.find(m => Number(m.id) === Number(form.module_id)) || null
    }
)

watch(
    () => form.lesson_id,
    () => {
        if (!form.lesson_id) {
            selectedLesson.value = null
            return
        }
        selectedLesson.value =
            lessonOptions.value.find(l => Number(l.id) === Number(form.lesson_id)) || null
    }
)

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

/** Отправка формы создания квиза */
const submitForm = () => {
    form.transform((data) => {
        return {
            ...data,
            activity: data.activity ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,
            images: newImages.value.map(img => ({
                file: img.file,
                order: img.order ?? 0,
                alt: img.alt ?? '',
                caption: img.caption ?? '',
            })),
        }
    })

    form.post(route('admin.quizzes.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Квиз успешно создан!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при создании Квиза:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createQuiz')">
        <template #header>
            <TitlePage>{{ t('createQuiz') }}</TitlePage>
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
                    <DefaultButton :href="route('admin.quizzes.index')">
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

                    <!-- Показывать в левом сайдбаре, в центральной части, в правом сайдбаре -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">

                        <!-- Показывать в левом сайдбаре -->
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.left"/>
                            <LabelCheckbox for="left" :text="t('left')"
                                           class="text-sm h-8 flex items-center"/>
                        </div>

                        <!-- Показывать в главных новостях -->
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.main"/>
                            <LabelCheckbox for="main" :text="t('main')"
                                           class="text-sm h-8 flex items-center"/>
                        </div>

                        <!-- Показывать в правом сайдбаре -->
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.right"/>
                            <LabelCheckbox for="right" :text="t('right')"
                                           class="text-sm h-8 flex items-center"/>
                        </div>

                    </div>

                    <!-- Дата публикации, Тип квиза -->
                    <div class="mb-3 flex justify-between flex-col
                                lg:flex-row items-center gap-4">

                        <!-- Тип квиза -->
                        <SelectQuizType
                            v-model="form.type"
                            :errorMessage="form.errors.type"
                        />

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
                            <InputError
                                class="mt-1 sm:mt-0"
                                :message="form.errors.published_at"
                            />
                        </div>

                    </div>

                    <!-- Параметры квиза -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        <!-- Лимит попыток -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="attempts_limit" :value="t('limitCount')" />
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

                        <!-- Ограничение по времени, мин -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="time_limit_minutes" :value="t('limitMinutes')" />
                            <InputNumber
                                id="time_limit_minutes"
                                type="number"
                                min="0"
                                v-model="form.time_limit_minutes"
                                autocomplete="time_limit_minutes"
                                class="w-full"
                            />
                            <InputError class="mt-1" :message="form.errors.time_limit_minutes" />
                        </div>

                        <!-- Проходной балл, % -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="pass_score" :value="t('passScore')" />
                            <InputNumber
                                id="pass_score"
                                type="number"
                                min="0"
                                max="100"
                                v-model="form.pass_score"
                                autocomplete="pass_score"
                                class="w-full"
                            />
                            <InputError class="mt-1" :message="form.errors.pass_score" />
                        </div>
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
                        <InputError class="mt-2" :message="form.errors.lesson_id" />
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
                        <InputError class="mt-2" :message="form.errors.module_id" />
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
                        <InputError class="mt-2" :message="form.errors.course_id" />
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

                    <!-- Описание квиза -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="description" :value="t('description')" />
                        <TinyEditor v-model="form.description" :height="500" />
                        <InputError
                            class="mt-2"
                            :message="form.errors.description"
                        />
                    </div>

                    <!-- Загрузка новых изображений -->
                    <div class="mt-4">
                        <MultiImageUpload @update:images="handleNewImagesUpdate" />
                    </div>

                    <!-- Кнопки сохранить/назад -->
                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.quizzes.index')"
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
