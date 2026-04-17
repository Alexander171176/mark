<script setup>
/**
 * Создание попытки (QuizAttempt)
 * attempt_number и percent вычисляются на бэке
 *
 * ✅ Автозаполнение/автокоррекция контекста:
 * - если выбираем Lesson -> автоподставляем Module и Course
 * - если выбираем Module -> автоподставляем Course + сбрасываем Lesson если не совпадает
 * - если меняем Course вручную -> сбрасываем Module/Lesson если не относятся к курсу
 * - если выбираем Quiz -> подтягиваем course/module/lesson из контекста квиза (+ fallback course из module)
 * - Enrollment фильтруется по User + Course; при выборе enrollment может подставить Course
 */
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'

import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

import VueMultiselect from 'vue-multiselect'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    quizzes: { type: Array, default: () => [] },      // QuizResource collection
    users: { type: Array, default: () => [] },        // [{id,name,email}]
    enrollments: { type: Array, default: () => [] },  // [{id,user_id,course_id}]
    courses: { type: Array, default: () => [] },      // [{id,title,locale}]
    modules: { type: Array, default: () => [] },      // [{id,title,course_id,locale}]
    lessons: { type: Array, default: () => [] },      // [{id,title,module_id,locale}]
    defaultQuizId: { type: Number, default: null },
    defaultUserId: { type: Number, default: null },
})

/** options-limit helper */
const dynamicOptionsLimit = (items) => {
    const arr = Array.isArray(items) ? items : (items?.value || [])
    return arr.length + 10
}

/** datetime-local helpers */
const toDatetimeLocal = (v) => {
    if (!v) return ''
    const d = new Date(v)
    if (!Number.isFinite(d.getTime())) return ''
    const pad = (n) => String(n).padStart(2, '0')
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}
const fromDatetimeLocal = (v) => {
    if (!v) return null
    const d = new Date(v)
    if (!Number.isFinite(d.getTime())) return null
    return d.toISOString()
}
const normalizeToEmptyString = (v) => (v === null || typeof v === 'undefined') ? '' : v

/** status map + options */
const statusMap = computed(() => ({
    in_progress: t('setStatusInProgress'),
    completed: t('setStatusCompleted'),
    graded: t('setStatusGraded'),
}))
const statusOptions = computed(() => ([
    { value: 'in_progress', label: statusMap.value.in_progress },
    { value: 'completed',   label: statusMap.value.completed },
    { value: 'graded',      label: statusMap.value.graded },
]))

/* ============================================================
   Options
   ============================================================ */

const quizOptions = computed(() =>
    (props.quizzes || []).map(q => {
        const title = q.title || q.slug || `#${q.id}`
        const locale = q.locale || '—'
        return {
            id: q.id,
            label: `[ID: ${q.id}] [${locale}] ${title}`,
            // контекст квиза
            course_id: q.course_id ?? null,
            module_id: q.module_id ?? null,
            lesson_id: q.lesson_id ?? null,
        }
    })
)

const userOptions = computed(() =>
    (props.users || []).map(u => ({
        id: u.id,
        label: `[ID: ${u.id}] ${u.name || '—'} (${u.email || '—'})`,
    }))
)

const courseOptions = computed(() =>
    (props.courses || []).map(c => {
        const locale = c.locale || '—'
        return {
            id: c.id,
            label: `[ID: ${c.id}] [${locale}] ${c.title || `#${c.id}`}`,
            locale,
        }
    })
)

const moduleOptionsAll = computed(() =>
    (props.modules || []).map(m => {
        const locale = m.locale || '—'
        return {
            id: m.id,
            course_id: m.course_id ?? null,
            label: `[ID: ${m.id}] [${locale}] ${m.title || `#${m.id}`}`,
            locale,
        }
    })
)

const lessonOptionsAll = computed(() =>
    (props.lessons || []).map(l => {
        const locale = l.locale || '—'
        return {
            id: l.id,
            module_id: l.module_id ?? null,
            label: `[ID: ${l.id}] [${locale}] ${l.title || `#${l.id}`}`,
            locale,
        }
    })
)

const enrollmentOptionsAll = computed(() => {
    const userMap = new Map((props.users || []).map(u => [u.id, u]))
    const courseMap = new Map((props.courses || []).map(c => [c.id, c]))

    return (props.enrollments || []).map(e => {
        const u = userMap.get(e.user_id)
        const c = courseMap.get(e.course_id)
        const userLabel = u ? `${u.name || '—'} (${u.email || '—'})` : `user#${e.user_id ?? '—'}`
        const courseLabel = c ? (c.title || `#${c.id}`) : `course#${e.course_id ?? '—'}`
        return {
            id: e.id,
            user_id: e.user_id ?? null,
            course_id: e.course_id ?? null,
            label: `[ID: ${e.id}] ${userLabel} — ${courseLabel}`,
        }
    })
})

/* ============================================================
   Selected (multiselect)
   ============================================================ */

const selectedQuiz = ref(quizOptions.value.find(o => o.id === props.defaultQuizId) || null)
const selectedUser = ref(userOptions.value.find(o => o.id === props.defaultUserId) || null)

const selectedCourse = ref(null)
const selectedModule = ref(null)
const selectedLesson = ref(null)
const selectedEnrollment = ref(null)

/* ============================================================
   Dependent options (UI filter)
   ============================================================ */

const moduleOptions = computed(() => {
    const courseId = selectedCourse.value?.id ?? null
    if (!courseId) return moduleOptionsAll.value
    return moduleOptionsAll.value.filter(m => Number(m.course_id) === Number(courseId))
})

const lessonOptions = computed(() => {
    const moduleId = selectedModule.value?.id ?? null
    if (!moduleId) return lessonOptionsAll.value
    return lessonOptionsAll.value.filter(l => Number(l.module_id) === Number(moduleId))
})

const enrollmentOptions = computed(() => {
    const userId = selectedUser.value?.id ?? null
    const courseId = selectedCourse.value?.id ?? null

    let list = enrollmentOptionsAll.value

    if (userId) list = list.filter(e => Number(e.user_id) === Number(userId))
    if (courseId) list = list.filter(e => Number(e.course_id) === Number(courseId))

    return list
})

/* ============================================================
   Form
   ============================================================ */

const form = useForm({
    user_id: selectedUser.value?.id ?? null,
    quiz_id: selectedQuiz.value?.id ?? null,

    enrollment_id: null,
    course_id: null,
    module_id: null,
    lesson_id: null,

    // attempt_number и percent НЕ отправляем

    status: 'in_progress',

    score: '',
    max_score: '',
    started_at: '',
    finished_at: '',
    duration_seconds: '',
})

/** normalize numbers for InputNumber */
form.score = normalizeToEmptyString(form.score)
form.max_score = normalizeToEmptyString(form.max_score)
form.duration_seconds = normalizeToEmptyString(form.duration_seconds)

/* ============================================================
   Helpers: entities by ids (для автозамены)
   ============================================================ */

const selectedModuleEntity = computed(() =>
    moduleOptionsAll.value.find(m => Number(m.id) === Number(form.module_id))
)

const selectedLessonEntity = computed(() =>
    lessonOptionsAll.value.find(l => Number(l.id) === Number(form.lesson_id))
)

/* ============================================================
   Guard flags (анти-зацикливание)
   ============================================================ */
const isAutoSyncFromQuiz = ref(false)
const isAutoSyncFromLesson = ref(false)
const isAutoSyncFromModule = ref(false)
const isAutoSyncFromCourse = ref(false)

/* ============================================================
   1) Multiselect -> form.*
   ============================================================ */

watch(selectedUser, (val) => {
    form.user_id = val ? val.id : null

    // enrollment должен соответствовать user
    if (selectedEnrollment.value && val && Number(selectedEnrollment.value.user_id) !== Number(val.id)) {
        selectedEnrollment.value = null
    }
    if (!val) selectedEnrollment.value = null
})

watch(selectedQuiz, (quiz) => {
    form.quiz_id = quiz ? quiz.id : null

    if (!quiz) {
        // чистим контекст
        selectedCourse.value = null
        selectedModule.value = null
        selectedLesson.value = null
        selectedEnrollment.value = null
        return
    }

    isAutoSyncFromQuiz.value = true

    // 🟦 COURSE (из квиза)
    selectedCourse.value = quiz.course_id
        ? (courseOptions.value.find(c => Number(c.id) === Number(quiz.course_id)) || null)
        : null

    // 🟦 MODULE (из квиза)
    selectedModule.value = quiz.module_id
        ? (moduleOptionsAll.value.find(m => Number(m.id) === Number(quiz.module_id)) || null)
        : null

    // ✅ fallback: если course_id не пришёл, но есть module_id — берём course_id из модуля
    if (!selectedCourse.value && selectedModule.value?.course_id) {
        selectedCourse.value =
            courseOptions.value.find(c => Number(c.id) === Number(selectedModule.value.course_id)) || null
    }

    // 🟦 LESSON (из квиза)
    selectedLesson.value = quiz.lesson_id
        ? (lessonOptionsAll.value.find(l => Number(l.id) === Number(quiz.lesson_id)) || null)
        : null

    // enrollment: если текущий уже не подходит — сброс
    if (selectedEnrollment.value) {
        const okUser = selectedUser.value?.id ? Number(selectedEnrollment.value.user_id) === Number(selectedUser.value.id) : true
        const okCourse = selectedCourse.value?.id ? Number(selectedEnrollment.value.course_id) === Number(selectedCourse.value.id) : true
        if (!okUser || !okCourse) selectedEnrollment.value = null
    }

    isAutoSyncFromQuiz.value = false
})

watch(selectedCourse, (val) => {
    form.course_id = val ? val.id : null
})

watch(selectedModule, (val) => {
    form.module_id = val ? val.id : null
})

watch(selectedLesson, (val) => {
    form.lesson_id = val ? val.id : null
})

watch(selectedEnrollment, (val) => {
    form.enrollment_id = val ? val.id : null

    // удобство: если выбрали enrollment и курс пустой — подставим курс
    if (val?.course_id && !selectedCourse.value) {
        selectedCourse.value = courseOptions.value.find(c => Number(c.id) === Number(val.course_id)) || null
    }
})

/* ============================================================
   2) Автозаполнение/автокоррекция (как у Create.vue сущностей)
   ============================================================ */

/**
 * ✅ При выборе Lesson — автоподставляем Module и Course.
 */
watch(
    () => form.lesson_id,
    () => {
        if (isAutoSyncFromQuiz.value || isAutoSyncFromModule.value || isAutoSyncFromCourse.value) return

        const lesson = selectedLessonEntity.value
        if (!lesson) return

        isAutoSyncFromLesson.value = true

        // module_id из урока
        if (lesson.module_id) {
            const newModuleId = Number(lesson.module_id)

            if (!form.module_id) {
                form.module_id = newModuleId
            } else if (Number(form.module_id) !== newModuleId) {
                form.module_id = newModuleId
                toast.info(t('module') + ': ' + t('autoCorrect'))
            }
        }

        // course_id из модуля урока
        const module = moduleOptionsAll.value.find(m => Number(m.id) === Number(lesson.module_id))
        const courseIdFromLesson = module?.course_id ?? null

        if (courseIdFromLesson) {
            const newCourseId = Number(courseIdFromLesson)

            if (!form.course_id) {
                form.course_id = newCourseId
            } else if (Number(form.course_id) !== newCourseId) {
                form.course_id = newCourseId
                toast.info(t('course') + ': ' + t('autoCorrect'))
            }
        }

        // UI sync
        if (form.module_id) {
            selectedModule.value =
                moduleOptionsAll.value.find(m => Number(m.id) === Number(form.module_id)) || null
        }
        if (form.course_id) {
            selectedCourse.value =
                courseOptions.value.find(c => Number(c.id) === Number(form.course_id)) || null
        }

        // enrollment может стать невалидным после смены курса
        if (selectedEnrollment.value && form.course_id && Number(selectedEnrollment.value.course_id) !== Number(form.course_id)) {
            selectedEnrollment.value = null
        }

        isAutoSyncFromLesson.value = false
    }
)

/**
 * ✅ При выборе Module — автоподставляем Course.
 * + сбрасываем Lesson, если он не из выбранного Module.
 */
watch(
    () => form.module_id,
    () => {
        if (isAutoSyncFromQuiz.value || isAutoSyncFromLesson.value || isAutoSyncFromCourse.value) return

        const module = selectedModuleEntity.value
        if (!module) return

        isAutoSyncFromModule.value = true

        const courseIdFromModule = module.course_id ?? null
        if (courseIdFromModule) {
            const newCourseId = Number(courseIdFromModule)

            if (!form.course_id) {
                form.course_id = newCourseId
            } else if (Number(form.course_id) !== newCourseId) {
                form.course_id = newCourseId
                toast.info(t('course') + ': ' + t('autoCorrect'))
            }

            selectedCourse.value =
                courseOptions.value.find(c => Number(c.id) === Number(form.course_id)) || null
        }

        // если выбран урок и он не из этого модуля — сбрасываем
        if (form.lesson_id) {
            const lesson = selectedLessonEntity.value
            if (lesson && Number(lesson.module_id) !== Number(form.module_id)) {
                form.lesson_id = null
                selectedLesson.value = null
                toast.info(t('lesson') + ': ' + t('autoCorrect'))
            }
        }

        // enrollment может стать невалидным после смены курса
        if (selectedEnrollment.value && form.course_id && Number(selectedEnrollment.value.course_id) !== Number(form.course_id)) {
            selectedEnrollment.value = null
        }

        isAutoSyncFromModule.value = false
    }
)

/**
 * ✅ При ручной смене Course — сбрасываем Module/Lesson,
 * если они не относятся к этому курсу. (Автосмены — не трогаем)
 */
watch(
    () => form.course_id,
    (newVal, oldVal) => {
        if (isAutoSyncFromQuiz.value || isAutoSyncFromLesson.value || isAutoSyncFromModule.value) return

        // если курс очистили
        if (!newVal) {
            isAutoSyncFromCourse.value = true
            form.module_id = null
            form.lesson_id = null
            selectedModule.value = null
            selectedLesson.value = null
            selectedEnrollment.value = null
            isAutoSyncFromCourse.value = false
            return
        }

        if (Number(newVal) === Number(oldVal)) return

        isAutoSyncFromCourse.value = true

        // module не из курса — сброс module + lesson
        if (form.module_id) {
            const module = selectedModuleEntity.value
            if (module?.course_id && Number(module.course_id) !== Number(newVal)) {
                form.module_id = null
                selectedModule.value = null
                form.lesson_id = null
                selectedLesson.value = null
                toast.info(t('module') + ': ' + t('autoCorrect'))
            }
        }

        // lesson не из курса — сброс lesson
        if (form.lesson_id) {
            const lesson = selectedLessonEntity.value
            const module = lesson?.module_id
                ? moduleOptionsAll.value.find(m => Number(m.id) === Number(lesson.module_id))
                : null
            const lessonCourseId = module?.course_id ?? null

            if (lessonCourseId && Number(lessonCourseId) !== Number(newVal)) {
                form.lesson_id = null
                selectedLesson.value = null
                toast.info(t('lesson') + ': ' + t('autoCorrect'))
            }
        }

        // enrollment должен совпадать с course
        if (selectedEnrollment.value && Number(selectedEnrollment.value.course_id) !== Number(newVal)) {
            selectedEnrollment.value = null
        }

        isAutoSyncFromCourse.value = false
    }
)

/* ============================================================
   3) form.* -> UI sync (на случай программной установки form.*)
   ============================================================ */

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
            moduleOptionsAll.value.find(m => Number(m.id) === Number(form.module_id)) || null
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
            lessonOptionsAll.value.find(l => Number(l.id) === Number(form.lesson_id)) || null
    }
)

/* ============================================================
   Submit
   ============================================================ */

const submitForm = () => {
    form.transform((data) => {
        const toNum = (v) => {
            if (v === '' || v === null || typeof v === 'undefined') return null
            const n = Number(v)
            return Number.isFinite(n) ? n : null
        }

        return {
            user_id: data.user_id,
            quiz_id: data.quiz_id,

            enrollment_id: data.enrollment_id || null,
            course_id: data.course_id || null,
            module_id: data.module_id || null,
            lesson_id: data.lesson_id || null,

            status: data.status,

            score: toNum(data.score),
            max_score: toNum(data.max_score),

            started_at: fromDatetimeLocal(data.started_at),
            finished_at: fromDatetimeLocal(data.finished_at),
            duration_seconds: toNum(data.duration_seconds),
        }
    })

    form.post(route('admin.quizAttempts.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Попытка успешно создана!'),
        onError: (errors) => {
            console.error('❌ Ошибка при создании попытки:', errors)
            const firstKey = Object.keys(errors || {})[0]
            toast.error(firstKey ? errors[firstKey] : 'Проверь поля формы.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createQuizAttempt')">
        <template #header>
            <TitlePage>
                {{ t('createQuizAttempt') }}
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="px-4 pt-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.quizAttempts.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.2-1.4-2.7l-2 .3c-.2 1.5 .9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" class="pt-3 w-full">

                    <!-- User (required) -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="user" class="mb-1">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
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

                    <!-- Quiz (required) -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="quiz" class="mb-1">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
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
                        <InputError class="mt-2" :message="form.errors.quiz_id" />
                    </div>

                    <!-- Context block -->
                    <div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300
                                bg-white/60 dark:bg-slate-800/40">
                        <div class="text-center text-md font-semibold opacity-80">
                            {{ t('context') }}
                        </div>

                        <!-- Lesson -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="lesson" class="mb-1">{{ t('lesson') }}</LabelInput>
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

                        <!-- Module -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="module" class="mb-1">
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
                            <InputError class="mt-2" :message="form.errors.module_id" />
                        </div>

                        <!-- Course -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="course" class="mb-1">
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
                            <InputError class="mt-2" :message="form.errors.course_id" />
                        </div>

                        <!-- Enrollment -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="enrollment" class="mb-1">{{ t('enrollment') }}</LabelInput>

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
                            <InputError class="mt-2" :message="form.errors.enrollment_id" />
                        </div>

                    </div>

                    <!-- Dates -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4">

                        <div class="flex flex-col items-start">
                            <LabelInput for="duration_seconds" class="mb-1">{{ t('duration') }}</LabelInput>
                            <InputNumber id="duration_seconds" type="number" min="0" v-model="form.duration_seconds"
                                         class="w-full" />
                            <InputError class="mt-2" :message="form.errors.duration_seconds" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="started_at" class="mb-1">{{ t('shortStarted') }}</LabelInput>
                            <InputText id="started_at" type="datetime-local" v-model="form.started_at" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.started_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="finished_at" class="mb-1">{{ t('shortExpires') }}</LabelInput>
                            <InputText id="finished_at" type="datetime-local" v-model="form.finished_at"
                                       class="w-full" />
                            <InputError class="mt-2" :message="form.errors.finished_at" />
                        </div>

                    </div>

                    <!-- Score / Max / Status -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4">

                        <div class="flex flex-col items-start">
                            <LabelInput for="score" class="mb-1">{{ t('score') }}</LabelInput>
                            <InputNumber id="score" type="number" min="0" v-model="form.score" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.score" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="max_score" class="mb-1">{{ t('maxScore') }}</LabelInput>
                            <InputNumber id="max_score" type="number" min="0" v-model="form.max_score" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.max_score" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="status" class="mb-1">{{ t('status') }}</LabelInput>

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full py-0.5 font-semibold text-sm
                                   border border-slate-500 rounded-sm shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-300
                                   dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>

                    </div>

                    <!-- Save -->
                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton :href="route('admin.quizAttempts.index')" class="mb-3">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4 .7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.2-1.4-2.7l-2 .3c-.2 1.5 .9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton class="mb-0" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1 .7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>
                            {{ t('create') || 'Создать' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
