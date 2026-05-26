<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание квиза школы
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

import SelectQuizType from '@/Components/Admin/School/Quiz/Select/SelectQuizType.vue'

// Локализация интерфейса
const { t } = useI18n()

// Уведомления
const toast = useToast()

// Входящие данные страницы
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
    lessons: { type: Array, default: () => [] },
})

// Создание пустого перевода для новой локали
const makeTranslation = () => ({
    title: '',
    short: '',
    description: '',
})

// Локаль по умолчанию
const defaultLocale = props.currentLocale || 'ru'

// Активная локаль вкладки переводов
const activeLocale = ref(defaultLocale)

// Форма создания квиза
const form = useForm({
    school_course_id: null,
    school_module_id: null,
    school_lesson_id: null,

    activity: true,
    left: false,
    main: false,
    right: false,

    sort: 0,
    slug: '',

    type: 'graded',
    attempts_limit: 0,
    time_limit_minutes: '',
    pass_score: 70,

    published_at: '',

    images: [],

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

// Текущий активный перевод
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// Получение ошибки текущей локали
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

// Динамический лимит опций multiselect
const dynamicOptionsLimit = (items) => {
    if (!items) return 10

    return items.length + 10
}

// Опции курсов для multiselect
const courseOptions = computed(() =>
    props.courses.map(course => ({
        id: course.id,
        label: `[ID: ${course.id}] ${course.title || course.slug || `#${course.id}`}`,
    }))
)

// Опции модулей для multiselect
const moduleOptions = computed(() =>
    props.modules.map(module => {
        const moduleTitle = module.title || module.slug || `#${module.id}`
        const courseTitle = module.course?.title

        return {
            id: module.id,
            label: courseTitle
                ? `[ID: ${module.id}] [${courseTitle}] ${moduleTitle}`
                : `[ID: ${module.id}] ${moduleTitle}`,
        }
    })
)

// Опции уроков для multiselect
const lessonOptions = computed(() =>
    props.lessons.map(lesson => {
        const lessonTitle = lesson.title || lesson.slug || `#${lesson.id}`
        const moduleTitle = lesson.module?.title
        const courseTitle = lesson.module?.course?.title

        let label = lessonTitle

        if (courseTitle && moduleTitle) {
            label = `[${courseTitle}] [${moduleTitle}] ${lessonTitle}`
        } else if (moduleTitle) {
            label = `[${moduleTitle}] ${lessonTitle}`
        }

        return {
            id: lesson.id,
            label: `[ID: ${lesson.id}] ${label}`,
        }
    })
)

// Выбранный курс
const selectedCourse = ref(null)

// Выбранный модуль
const selectedModule = ref(null)

// Выбранный урок
const selectedLesson = ref(null)

// Текущий выбранный модуль
const selectedModuleEntity = computed(() =>
    props.modules.find(module => Number(module.id) === Number(form.school_module_id))
)

// Текущий выбранный урок
const selectedLessonEntity = computed(() =>
    props.lessons.find(lesson => Number(lesson.id) === Number(form.school_lesson_id))
)

// Флаг автосинхронизации от урока
const isAutoSyncFromLesson = ref(false)

// Флаг автосинхронизации от модуля
const isAutoSyncFromModule = ref(false)

// Флаг автосинхронизации от курса
const isAutoSyncFromCourse = ref(false)

// Синхронизация выбранного курса с формой
watch(selectedCourse, (val) => {
    form.school_course_id = val?.id ?? null
})

// Синхронизация выбранного модуля с формой
watch(selectedModule, (val) => {
    form.school_module_id = val?.id ?? null
})

// Синхронизация выбранного урока с формой
watch(selectedLesson, (val) => {
    form.school_lesson_id = val?.id ?? null
})

// Автоподстановка курса и модуля из урока
watch(
    () => form.school_lesson_id,
    () => {
        if (isAutoSyncFromCourse.value || isAutoSyncFromModule.value) return

        const lesson = selectedLessonEntity.value

        if (!lesson) return

        isAutoSyncFromLesson.value = true

        if (lesson.school_module_id || lesson.module_id) {
            const newModuleId = Number(lesson.school_module_id || lesson.module_id)

            if (!form.school_module_id) {
                form.school_module_id = newModuleId
            } else if (Number(form.school_module_id) !== newModuleId) {
                form.school_module_id = newModuleId
                toast.info('Модуль автоматически подставлен из выбранного урока')
            }
        }

        const courseIdFromLesson =
            lesson.module?.school_course_id
            ?? lesson.module?.course_id
            ?? lesson.module?.course?.id
            ?? null

        if (courseIdFromLesson) {
            const newCourseId = Number(courseIdFromLesson)

            if (!form.school_course_id) {
                form.school_course_id = newCourseId
            } else if (Number(form.school_course_id) !== newCourseId) {
                form.school_course_id = newCourseId
                toast.info('Курс автоматически подставлен из выбранного урока')
            }
        }

        selectedModule.value =
            moduleOptions.value.find(module => Number(module.id) === Number(form.school_module_id)) || null

        selectedCourse.value =
            courseOptions.value.find(course => Number(course.id) === Number(form.school_course_id)) || null

        isAutoSyncFromLesson.value = false
    }
)

// Автоподстановка курса из модуля
watch(
    () => form.school_module_id,
    () => {
        if (isAutoSyncFromLesson.value || isAutoSyncFromCourse.value) return

        const module = selectedModuleEntity.value

        if (!module) return

        isAutoSyncFromModule.value = true

        const courseIdFromModule =
            module.school_course_id
            ?? module.course_id
            ?? module.course?.id
            ?? null

        if (courseIdFromModule) {
            const newCourseId = Number(courseIdFromModule)

            if (!form.school_course_id) {
                form.school_course_id = newCourseId
            } else if (Number(form.school_course_id) !== newCourseId) {
                form.school_course_id = newCourseId
                toast.info('Курс автоматически подставлен из выбранного модуля')
            }

            selectedCourse.value =
                courseOptions.value.find(course => Number(course.id) === Number(form.school_course_id)) || null
        }

        if (form.school_lesson_id) {
            const lesson = selectedLessonEntity.value
            const lessonModuleId = lesson?.school_module_id ?? lesson?.module_id

            if (lesson && Number(lessonModuleId) !== Number(form.school_module_id)) {
                form.school_lesson_id = null
                selectedLesson.value = null
                toast.info('Урок сброшен, так как не относится к выбранному модулю')
            }
        }

        isAutoSyncFromModule.value = false
    }
)

// Проверка соответствия курса, модуля и урока
watch(
    () => form.school_course_id,
    (newVal, oldVal) => {
        if (isAutoSyncFromLesson.value || isAutoSyncFromModule.value) return

        if (!newVal || Number(newVal) === Number(oldVal)) {
            if (!newVal) {
                isAutoSyncFromCourse.value = true

                form.school_module_id = null
                form.school_lesson_id = null
                selectedModule.value = null
                selectedLesson.value = null

                isAutoSyncFromCourse.value = false
            }

            return
        }

        isAutoSyncFromCourse.value = true

        if (form.school_module_id) {
            const module = selectedModuleEntity.value
            const moduleCourseId =
                module?.school_course_id
                ?? module?.course_id
                ?? module?.course?.id
                ?? null

            if (moduleCourseId && Number(moduleCourseId) !== Number(newVal)) {
                form.school_module_id = null
                form.school_lesson_id = null
                selectedModule.value = null
                selectedLesson.value = null

                toast.info('Модуль и урок сброшены: они не относятся к выбранному курсу')
            }
        }

        if (form.school_lesson_id) {
            const lesson = selectedLessonEntity.value
            const lessonCourseId =
                lesson?.module?.school_course_id
                ?? lesson?.module?.course_id
                ?? lesson?.module?.course?.id
                ?? null

            if (lessonCourseId && Number(lessonCourseId) !== Number(newVal)) {
                form.school_lesson_id = null
                selectedLesson.value = null

                toast.info('Урок сброшен: он не относится к выбранному курсу')
            }
        }

        isAutoSyncFromCourse.value = false
    }
)

// Синхронизация выбранного курса
watch(
    () => form.school_course_id,
    () => {
        if (!form.school_course_id) {
            selectedCourse.value = null
            return
        }

        selectedCourse.value =
            courseOptions.value.find(course => Number(course.id) === Number(form.school_course_id)) || null
    }
)

// Синхронизация выбранного модуля
watch(
    () => form.school_module_id,
    () => {
        if (!form.school_module_id) {
            selectedModule.value = null
            return
        }

        selectedModule.value =
            moduleOptions.value.find(module => Number(module.id) === Number(form.school_module_id)) || null
    }
)

// Синхронизация выбранного урока
watch(
    () => form.school_lesson_id,
    () => {
        if (!form.school_lesson_id) {
            selectedLesson.value = null
            return
        }

        selectedLesson.value =
            lessonOptions.value.find(lesson => Number(lesson.id) === Number(form.school_lesson_id)) || null
    }
)

// Новые загружаемые изображения
const newImages = ref([])

// Обновление списка новых изображений
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

// Генерация slug из заголовка
const handleSlugFocus = () => {
    if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

// Отправка формы создания квиза
const submit = () => {
    form.transform((data) => {
        const transformed = {
            ...data,

            school_course_id: data.school_course_id || null,
            school_module_id: data.school_module_id || null,
            school_lesson_id: data.school_lesson_id || null,

            attempts_limit: Number(data.attempts_limit || 0),
            time_limit_minutes: data.time_limit_minutes === '' || data.time_limit_minutes === null
                ? null
                : Number(data.time_limit_minutes),
            pass_score: Number(data.pass_score || 0),
            sort: Number(data.sort || 0),

            activity: data.activity ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,
        }

        newImages.value.forEach((image, index) => {
            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
        })

        return transformed
    })

    form.post(route('admin.schoolQuizzes.store'), {
        errorBag: 'createSchoolQuiz',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Квиз успешно создан!'),
        onError: (errors) => {
            console.error('Ошибка создания квиза:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
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
                    <DefaultButton :href="route('admin.schoolQuizzes.index')">
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
                    @submit.prevent="submit"
                    enctype="multipart/form-data"
                    class="p-3 w-full"
                >
                    <div class="pb-12">
                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox for="activity" :text="t('activity')"
                                               class="text-sm h-8 flex items-center" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                                <InputNumber id="sort" type="number" v-model.number="form.sort"
                                             class="w-full lg:w-28" />
                                <InputError :message="form.errors.sort" />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.left" />
                                <LabelCheckbox for="left" :text="t('left')" class="text-sm h-8 flex items-center" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.main" />
                                <LabelCheckbox for="main" :text="t('main')" class="text-sm h-8 flex items-center" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.right" />
                                <LabelCheckbox for="right" :text="t('right')" class="text-sm h-8 flex items-center" />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <SelectQuizType v-model="form.type" :errorMessage="form.errors.type" />

                            <div class="flex flex-col items-start">
                                <LabelInput for="published_at" :value="t('publishedAt')" />
                                <InputText
                                    id="published_at"
                                    type="datetime-local"
                                    v-model="form.published_at"
                                    class="w-full max-w-56"
                                />
                                <InputError :message="form.errors.published_at" />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="attempts_limit" :value="t('limitCount')" />
                                <InputNumber id="attempts_limit" type="number" min="0"
                                             v-model.number="form.attempts_limit" class="w-full" />
                                <InputError :message="form.errors.attempts_limit" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="time_limit_minutes" :value="t('limitMinutes')" />
                                <InputNumber id="time_limit_minutes" type="number" min="1"
                                             v-model.number="form.time_limit_minutes" class="w-full" />
                                <InputError :message="form.errors.time_limit_minutes" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="pass_score" :value="t('passScore')" />
                                <InputNumber id="pass_score" type="number" min="0" max="100"
                                             v-model.number="form.pass_score" class="w-full" />
                                <InputError :message="form.errors.pass_score" />
                            </div>
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="school_course_id" :value="t('course')" class="mb-1" />
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
                            <InputError class="mt-2" :message="form.errors.school_course_id" />
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
                            <LabelInput for="school_lesson_id" :value="t('lesson')" class="mb-1" />
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
                            <InputError class="mt-2" :message="form.errors.school_lesson_id" />
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
                                class="w-full"
                                autocomplete="slug"
                                @focus="handleSlugFocus"
                            />

                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

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
                            <LabelInput for="title">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('title') }}
                            </LabelInput>

                            <InputText
                                id="title"
                                type="text"
                                v-model="currentTranslation.title"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="getError('title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="short" :value="t('shortDescription')" />

                            <MetaDescTextarea
                                id="short"
                                v-model="currentTranslation.short"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="getError('short')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="description" :value="t('description')" />

                            <TinyEditor
                                v-model="currentTranslation.description"
                                :height="500"
                            />

                            <InputError class="mt-2" :message="getError('description')" />
                        </div>

                        <div class="mb-3">
                            <MultiImageUpload
                                v-model:images="newImages"
                                @update:images="handleNewImagesUpdate"
                            />

                            <InputError class="mt-2" :message="form.errors.images" />
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.schoolQuizzes.index')">
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
                            class="ms-4 mb-0"
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
