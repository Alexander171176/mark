<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание попытки прохождения викторины
 */
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Props страницы создания
const props = defineProps({
    users: { type: Array, default: () => [] },
    quizzes: { type: Array, default: () => [] },
    enrollments: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
    lessons: { type: Array, default: () => [] },

    defaultUserId: { type: Number, default: null },
    defaultQuizId: { type: Number, default: null },
    defaultEnrollmentId: { type: Number, default: null },
})

// Динамический лимит опций multiselect
const dynamicOptionsLimit = (items) => {
    const list = Array.isArray(items) ? items : []

    return list.length + 10
}

// Преобразование datetime-local в ISO
const fromDatetimeLocal = (value) => {
    if (!value) return null

    const date = new Date(value)

    return Number.isFinite(date.getTime())
        ? date.toISOString()
        : null
}

// Нормализация чисел
const toNumberOrNull = (value) => {
    if (value === '' || value === null || typeof value === 'undefined') {
        return null
    }

    const number = Number(value)

    return Number.isFinite(number) ? number : null
}

// Список статусов
const statusOptions = computed(() => ([
    { value: 'in_progress', label: t('setStatusInProgress') },
    { value: 'completed', label: t('setStatusCompleted') },
    { value: 'graded', label: t('setStatusGraded') },
]))

// Опции пользователей
const userOptions = computed(() =>
    (props.users || []).map(user => ({
        id: user.id,
        label: `[ID: ${user.id}] ${user.name || '—'}${user.email ? ` (${user.email})` : ''}`,
    }))
)

// Опции квизов
const quizOptions = computed(() =>
    (props.quizzes || []).map(quiz => {
        const context = [
            quiz.course?.title ? `Курс: ${quiz.course.title}` : null,
            quiz.module?.title ? `Модуль: ${quiz.module.title}` : null,
            quiz.lesson?.title ? `Урок: ${quiz.lesson.title}` : null,
        ].filter(Boolean).join(' / ')

        return {
            id: quiz.id,
            school_course_id: quiz.school_course_id ?? quiz.course?.id ?? null,
            school_module_id: quiz.school_module_id ?? quiz.module?.id ?? null,
            school_lesson_id: quiz.school_lesson_id ?? quiz.lesson?.id ?? null,
            label: context
                ? `[ID: ${quiz.id}] ${quiz.title || quiz.slug || `#${quiz.id}`} — ${context}`
                : `[ID: ${quiz.id}] ${quiz.title || quiz.slug || `#${quiz.id}`}`,
        }
    })
)

// Опции курсов
const courseOptions = computed(() =>
    (props.courses || []).map(course => ({
        id: course.id,
        label: `[ID: ${course.id}] ${course.title || course.slug || `#${course.id}`}`,
    }))
)

// Все модули
const moduleOptionsAll = computed(() =>
    (props.modules || []).map(module => ({
        id: module.id,
        school_course_id: module.school_course_id ?? module.course?.id ?? null,
        label: `[ID: ${module.id}] ${module.title || module.slug || `#${module.id}`}`,
    }))
)

// Все уроки
const lessonOptionsAll = computed(() =>
    (props.lessons || []).map(lesson => ({
        id: lesson.id,
        school_module_id: lesson.school_module_id ?? lesson.module?.id ?? null,
        label: `[ID: ${lesson.id}] ${lesson.title || lesson.slug || `#${lesson.id}`}`,
    }))
)

// Опции зачислений
const enrollmentOptionsAll = computed(() =>
    (props.enrollments || []).map(enrollment => {
        const userTitle = enrollment.user?.name
            ? `${enrollment.user.name}${enrollment.user.email ? ` (${enrollment.user.email})` : ''}`
            : `User ID: ${enrollment.user_id || '—'}`

        const courseTitle = enrollment.course?.title
            ? enrollment.course.title
            : `Course ID: ${enrollment.school_course_id || '—'}`

        return {
            id: enrollment.id,
            user_id: enrollment.user_id ?? null,
            school_course_id: enrollment.school_course_id ?? enrollment.course?.id ?? null,
            label: `[ID: ${enrollment.id}] ${userTitle} — ${courseTitle}`,
        }
    })
)

// Выбранный пользователь
const selectedUser = ref(
    userOptions.value.find(item => Number(item.id) === Number(props.defaultUserId)) || null
)

// Выбранный квиз
const selectedQuiz = ref(
    quizOptions.value.find(item => Number(item.id) === Number(props.defaultQuizId)) || null
)

// Выбранное зачисление
const selectedEnrollment = ref(
    enrollmentOptionsAll.value.find(item => Number(item.id) === Number(props.defaultEnrollmentId)) || null
)

// Выбранный курс
const selectedCourse = ref(null)

// Выбранный модуль
const selectedModule = ref(null)

// Выбранный урок
const selectedLesson = ref(null)

// Форма создания
const form = useForm({
    user_id: selectedUser.value?.id ?? null,
    school_quiz_id: selectedQuiz.value?.id ?? null,

    school_enrollment_id: selectedEnrollment.value?.id ?? null,
    school_course_id: null,
    school_module_id: null,
    school_lesson_id: null,

    attempt_number: '',
    status: 'in_progress',

    score: '',
    max_score: '',
    started_at: '',
    finished_at: '',
    duration_seconds: '',
})

// Фильтр модулей по выбранному курсу
const moduleOptions = computed(() => {
    const courseId = selectedCourse.value?.id ?? null

    if (!courseId) return moduleOptionsAll.value

    return moduleOptionsAll.value.filter(module =>
        Number(module.school_course_id) === Number(courseId)
    )
})

// Фильтр уроков по выбранному модулю
const lessonOptions = computed(() => {
    const moduleId = selectedModule.value?.id ?? null

    if (!moduleId) return lessonOptionsAll.value

    return lessonOptionsAll.value.filter(lesson =>
        Number(lesson.school_module_id) === Number(moduleId)
    )
})

// Фильтр зачислений по пользователю и курсу
const enrollmentOptions = computed(() => {
    const userId = selectedUser.value?.id ?? null
    const courseId = selectedCourse.value?.id ?? null

    let list = enrollmentOptionsAll.value

    if (userId) {
        list = list.filter(enrollment =>
            Number(enrollment.user_id) === Number(userId)
        )
    }

    if (courseId) {
        list = list.filter(enrollment =>
            Number(enrollment.school_course_id) === Number(courseId)
        )
    }

    return list
})

// Поиск модуля по ID
const findModule = (id) => {
    return moduleOptionsAll.value.find(module =>
        Number(module.id) === Number(id)
    ) || null
}

// Поиск урока по ID
const findLesson = (id) => {
    return lessonOptionsAll.value.find(lesson =>
        Number(lesson.id) === Number(id)
    ) || null
}

// Поиск курса по ID
const findCourse = (id) => {
    return courseOptions.value.find(course =>
        Number(course.id) === Number(id)
    ) || null
}

// Синхронизация пользователя
watch(selectedUser, (value) => {
    form.user_id = value?.id ?? null

    if (
        selectedEnrollment.value &&
        value &&
        Number(selectedEnrollment.value.user_id) !== Number(value.id)
    ) {
        selectedEnrollment.value = null
    }

    if (!value) {
        selectedEnrollment.value = null
    }
})

// Синхронизация квиза и автоподстановка контекста
watch(selectedQuiz, (quiz) => {
    form.school_quiz_id = quiz?.id ?? null

    if (!quiz) {
        selectedCourse.value = null
        selectedModule.value = null
        selectedLesson.value = null
        selectedEnrollment.value = null
        return
    }

    selectedCourse.value = quiz.school_course_id
        ? findCourse(quiz.school_course_id)
        : null

    selectedModule.value = quiz.school_module_id
        ? findModule(quiz.school_module_id)
        : null

    if (!selectedCourse.value && selectedModule.value?.school_course_id) {
        selectedCourse.value = findCourse(selectedModule.value.school_course_id)
    }

    selectedLesson.value = quiz.school_lesson_id
        ? findLesson(quiz.school_lesson_id)
        : null

    if (
        selectedEnrollment.value &&
        selectedCourse.value &&
        Number(selectedEnrollment.value.school_course_id) !== Number(selectedCourse.value.id)
    ) {
        selectedEnrollment.value = null
    }
})

// Синхронизация курса
watch(selectedCourse, (value) => {
    form.school_course_id = value?.id ?? null

    if (
        value &&
        selectedModule.value &&
        Number(selectedModule.value.school_course_id) !== Number(value.id)
    ) {
        selectedModule.value = null
        selectedLesson.value = null
    }

    if (
        value &&
        selectedEnrollment.value &&
        Number(selectedEnrollment.value.school_course_id) !== Number(value.id)
    ) {
        selectedEnrollment.value = null
    }
})

// Синхронизация модуля и автоподстановка курса
watch(selectedModule, (value) => {
    form.school_module_id = value?.id ?? null

    if (value?.school_course_id) {
        selectedCourse.value = findCourse(value.school_course_id)
    }

    if (
        value &&
        selectedLesson.value &&
        Number(selectedLesson.value.school_module_id) !== Number(value.id)
    ) {
        selectedLesson.value = null
    }
})

// Синхронизация урока и автоподстановка модуля/курса
watch(selectedLesson, (value) => {
    form.school_lesson_id = value?.id ?? null

    if (!value?.school_module_id) return

    const module = findModule(value.school_module_id)

    if (module) {
        selectedModule.value = module

        if (module.school_course_id) {
            selectedCourse.value = findCourse(module.school_course_id)
        }
    }
})

// Синхронизация зачисления и автоподстановка пользователя/курса
watch(selectedEnrollment, (value) => {
    form.school_enrollment_id = value?.id ?? null

    if (!value) return

    if (value.user_id) {
        selectedUser.value = userOptions.value.find(user =>
            Number(user.id) === Number(value.user_id)
        ) || selectedUser.value
    }

    if (value.school_course_id) {
        selectedCourse.value = findCourse(value.school_course_id) || selectedCourse.value
    }
})

// Отправка формы
const submitForm = () => {
    form.transform((data) => ({
        ...data,

        user_id: selectedUser.value?.id ?? null,
        school_quiz_id: selectedQuiz.value?.id ?? null,

        school_enrollment_id: selectedEnrollment.value?.id ?? null,
        school_course_id: selectedCourse.value?.id ?? null,
        school_module_id: selectedModule.value?.id ?? null,
        school_lesson_id: selectedLesson.value?.id ?? null,

        attempt_number: toNumberOrNull(data.attempt_number),
        score: toNumberOrNull(data.score),
        max_score: toNumberOrNull(data.max_score),

        started_at: fromDatetimeLocal(data.started_at),
        finished_at: fromDatetimeLocal(data.finished_at),
        duration_seconds: toNumberOrNull(data.duration_seconds),
    }))

    form.post(route('admin.schoolQuizAttempts.store'), {
        preserveScroll: true,
        onSuccess: () => toast.success('Попытка квиза успешно создана.'),
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors?.[firstKey] || 'Проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createQuizAttempt')">
        <template #header>
            <TitlePage>{{ t('createQuizAttempt') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.schoolQuizAttempts.index')">
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

                <form @submit.prevent="submitForm" class="p-3 w-full space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="user">
                                <span class="text-red-500 dark:text-red-300 font-semibold">
                                    *
                                </span>
                                {{ t('user') }}
                            </LabelInput>

                            <VueMultiselect
                                id="user"
                                v-model="selectedUser"
                                :options="userOptions"
                                :options-limit="dynamicOptionsLimit(userOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.user_id" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="quiz">
                                <span class="text-red-500 dark:text-red-300 font-semibold">
                                    *
                                </span>
                                {{ t('quiz') }}
                            </LabelInput>

                            <VueMultiselect
                                id="quiz"
                                v-model="selectedQuiz"
                                :options="quizOptions"
                                :options-limit="dynamicOptionsLimit(quizOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.school_quiz_id" />
                        </div>
                    </div>

                    <div
                        class="p-3 border border-dashed border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40"
                    >
                        <div class="text-center text-md font-semibold opacity-80 mb-3">
                            {{ t('context') }}
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="lesson">{{ t('lesson') }}</LabelInput>

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

                            <div class="flex flex-col items-start">
                                <LabelInput for="module">
                                    {{ t('module') }} ({{ t('autoCorrect') }})
                                </LabelInput>

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

                            <div class="flex flex-col items-start">
                                <LabelInput for="course">
                                    {{ t('course') }} ({{ t('autoCorrect') }})
                                </LabelInput>

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

                            <div class="flex flex-col items-start">
                                <LabelInput for="enrollment">{{ t('enrollment') }}</LabelInput>

                                <VueMultiselect
                                    id="enrollment"
                                    v-model="selectedEnrollment"
                                    :options="enrollmentOptions"
                                    :options-limit="dynamicOptionsLimit(enrollmentOptions)"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="t('select')"
                                    label="label"
                                    track-by="id"
                                    class="w-full"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.school_enrollment_id" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="duration_seconds">{{ t('duration') }}</LabelInput>
                            <InputNumber
                                id="duration_seconds"
                                type="number"
                                min="0"
                                v-model="form.duration_seconds"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.duration_seconds" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="started_at">{{ t('shortStarted') }}</LabelInput>
                            <InputText
                                id="started_at"
                                type="datetime-local"
                                v-model="form.started_at"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.started_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="finished_at">{{ t('shortExpires') }}</LabelInput>
                            <InputText
                                id="finished_at"
                                type="datetime-local"
                                v-model="form.finished_at"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.finished_at" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="attempt_number">{{ t('attemptNumber') }}</LabelInput>
                            <InputNumber
                                id="attempt_number"
                                type="number"
                                min="1"
                                v-model="form.attempt_number"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.attempt_number" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="score">{{ t('score') }}</LabelInput>
                            <InputNumber
                                id="score"
                                type="number"
                                min="0"
                                v-model="form.score"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.score" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="max_score">{{ t('maxScore') }}</LabelInput>
                            <InputNumber
                                id="max_score"
                                type="number"
                                min="0"
                                v-model="form.max_score"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.max_score" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="status">{{ t('status') }}</LabelInput>

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full py-0.5 font-semibold text-sm
                                       border border-slate-500 rounded-sm shadow-sm
                                       focus:border-indigo-500 focus:ring-indigo-300
                                       dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option
                                    v-for="option in statusOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton :href="route('admin.schoolQuizAttempts.index')">
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
