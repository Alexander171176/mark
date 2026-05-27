<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование ответа вопроса викторины (SchoolQuizAnswer)
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
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

// Локализация
const { t } = useI18n()

// Toast уведомления
const toast = useToast()

// Props страницы редактирования
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    answer: { type: Object, required: true },
    quizzes: { type: Array, default: () => [] },
    questions: { type: Array, default: () => [] },
})

// Создание пустой структуры перевода
const makeTranslation = () => ({
    text: '',
    explanation: '',
})

// Очистка HTML тегов из текста
const stripHtml = (html = '') => {
    return html
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

// Сокращение текста для отображения в select
const shortText = (html, limit = 120) => {
    const clean = stripHtml(html)

    return clean.length > limit ? clean.slice(0, limit) + '…' : clean
}

// Формирование переводов из ответа
const buildTranslations = () => {
    const result = {}

    ;(props.answer.translations || []).forEach((translation) => {
        result[translation.locale] = {
            text: translation.text || '',
            explanation: translation.explanation || '',
        }
    })

    const defaultLocale =
        props.currentLocale ||
        props.answer.translation?.locale ||
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
    props.answer.translation?.locale ||
    props.availableLocales[0] ||
    'ru'

// Текущая активная локаль
const activeLocale = ref(defaultLocale)

// Основная форма редактирования
const form = useForm({
    _method: 'PUT',

    school_quiz_id:
        props.answer.school_quiz_id ??
        props.answer.quiz?.id ??
        null,

    school_quiz_question_id:
        props.answer.school_quiz_question_id ??
        props.answer.question?.id ??
        null,

    is_correct: Boolean(props.answer.is_correct),
    weight: props.answer.weight ?? 0,
    sort: props.answer.sort ?? 0,
    activity: Boolean(props.answer.activity),

    translations: buildTranslations(),
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
    return stripHtml(
        currentTranslation.value.text ||
        props.answer.translation?.text ||
        props.answer.text ||
        `ID: ${props.answer.id}`
    )
})

// Получение ошибок текущей локали
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

// Динамический лимит select опций
const dynamicOptionsLimit = (items) => {
    if (!items) return 10

    return items.length + 10
}

// Опции квизов
const quizOptions = computed(() => props.quizzes ?? [])

// Опции вопросов
const questionOptions = computed(() => {
    const list = props.questions ?? []

    return [...list].sort((a, b) => (b.id ?? 0) - (a.id ?? 0))
})

// Подпись квиза в select
const quizOptionLabel = (quiz) => {
    if (!quiz) return ''

    const idPart = `[ID: ${quiz.id}]`
    const titlePart = quiz.title || quiz.slug || `#${quiz.id}`

    const context = [
        quiz.course?.title ? `Курс: ${quiz.course.title}` : null,
        quiz.module?.title ? `Модуль: ${quiz.module.title}` : null,
        quiz.lesson?.title ? `Урок: ${quiz.lesson.title}` : null,
    ].filter(Boolean).join(' / ')

    return context
        ? `${idPart} ${titlePart} — ${context}`
        : `${idPart} ${titlePart}`
}

// Подпись вопроса в select
const questionOptionLabel = (question) => {
    if (!question) return ''

    const idPart = `[ID: ${question.id}]`
    const questionText = question.question_text
        ? shortText(question.question_text)
        : `#${question.id}`

    const quizPart = question.school_quiz_id
        ? `Quiz ID: ${question.school_quiz_id}`
        : null

    return quizPart
        ? `${idPart} ${questionText} — ${quizPart}`
        : `${idPart} ${questionText}`
}

// Выбранный квиз и вопрос
const selectedQuiz = ref(null)
const selectedQuestion = ref(null)

// Синхронизация выбранного квиза
watch(
    quizOptions,
    (options) => {
        selectedQuiz.value = options.find(
            item => Number(item.id) === Number(form.school_quiz_id)
        ) || null
    },
    { immediate: true }
)

// Синхронизация выбранного вопроса
watch(
    questionOptions,
    (options) => {
        selectedQuestion.value = options.find(
            item => Number(item.id) === Number(form.school_quiz_question_id)
        ) || null
    },
    { immediate: true }
)

// Обновление school_quiz_id при выборе квиза
watch(selectedQuiz, (val) => {
    form.school_quiz_id = val?.id ?? null

    if (
        val &&
        selectedQuestion.value &&
        Number(selectedQuestion.value.school_quiz_id) !== Number(val.id)
    ) {
        selectedQuestion.value = null
        form.school_quiz_question_id = null
    }
})

// Обновление school_quiz_question_id при выборе вопроса
watch(selectedQuestion, (val) => {
    form.school_quiz_question_id = val?.id ?? null

    if (val?.school_quiz_id) {
        const relatedQuiz = quizOptions.value.find(
            item => Number(item.id) === Number(val.school_quiz_id)
        )

        if (relatedQuiz) {
            selectedQuiz.value = relatedQuiz
            form.school_quiz_id = relatedQuiz.id
        }
    }
})

// Отправка формы
const submitForm = () => {
    form.transform((data) => ({
        ...data,
        school_quiz_id: selectedQuiz.value?.id ?? null,
        school_quiz_question_id: selectedQuestion.value?.id ?? null,
        activity: data.activity ? 1 : 0,
        is_correct: data.is_correct ? 1 : 0,
        weight: data.weight === '' || data.weight === null ? 0 : Number(data.weight),
        sort: data.sort === '' || data.sort === null ? 0 : Number(data.sort),
    }))

    form.post(route('admin.schoolQuizAnswers.update', {
        schoolQuizAnswer: props.answer.id,
    }), {
        errorBag: 'editSchoolQuizAnswer',
        preserveScroll: true,
        onSuccess: () => toast.success('Ответ квиза успешно обновлён!'),
        onError: (errors) => {
            console.error('Ошибка обновления ответа квиза:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editQuizAnswer')">
        <template #header>
            <TitlePage>
                {{ t('editQuizAnswer') }}: {{ pageTitle }} [ID: {{ props.answer.id }}]
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-4">
                    <DefaultButton
                        :href="route('admin.schoolQuizAnswers.index')"
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
                </div>

                <form
                    @submit.prevent="submitForm"
                    class="p-3 w-full"
                >
                    <div class="pb-12">
                        <div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                                <InputError :message="form.errors.activity" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.is_correct" />
                                <LabelCheckbox
                                    for="is_correct"
                                    :text="t('isCorrect')"
                                    class="text-sm h-8 flex items-center"
                                />
                                <InputError :message="form.errors.is_correct" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <LabelInput
                                    for="weight"
                                    :value="t('points')"
                                />
                                <InputNumber
                                    id="weight"
                                    type="number"
                                    min="0"
                                    max="100"
                                    v-model="form.weight"
                                    class="w-28"
                                />
                                <InputError :message="form.errors.weight" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <LabelInput
                                    for="sort"
                                    :value="t('sort')"
                                />
                                <InputNumber
                                    id="sort"
                                    type="number"
                                    min="0"
                                    v-model="form.sort"
                                    class="w-28"
                                />
                                <InputError :message="form.errors.sort" />
                            </div>
                        </div>

                        <div class="mb-4 flex flex-col items-start w-full">
                            <LabelInput
                                for="school_quiz_id"
                                :value="t('quiz')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="school_quiz_id"
                                v-model="selectedQuiz"
                                :options="quizOptions"
                                :options-limit="dynamicOptionsLimit(quizOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="title"
                                track-by="id"
                                :custom-label="quizOptionLabel"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.school_quiz_id"
                            />
                        </div>

                        <div class="mb-4 flex flex-col items-start w-full">
                            <LabelInput
                                for="school_quiz_question_id"
                                :value="t('quizQuestion')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="school_quiz_question_id"
                                v-model="selectedQuestion"
                                :options="questionOptions"
                                :options-limit="dynamicOptionsLimit(questionOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="id"
                                track-by="id"
                                :custom-label="questionOptionLabel"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.school_quiz_question_id"
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

                            <div class="mb-4 flex flex-col items-start">
                                <LabelInput for="text">
                                    <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                    {{ t('answer') }} [{{ activeLocale.toUpperCase() }}]
                                </LabelInput>
                                <TinyEditor
                                    id="text"
                                    v-model="currentTranslation.text"
                                    :height="250"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="getError('text')"
                                />
                            </div>

                            <div class="mb-4 flex flex-col items-start">
                                <LabelInput
                                    for="explanation"
                                    :value="`${t('explanation')} [${activeLocale.toUpperCase()}]`"
                                />
                                <TinyEditor
                                    id="explanation"
                                    v-model="currentTranslation.explanation"
                                    :height="220"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="getError('explanation')"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton
                            :href="route('admin.schoolQuizAnswers.index')"
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
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
