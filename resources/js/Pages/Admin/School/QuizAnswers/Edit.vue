<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Редактирование варианта ответа квиза (QuizAnswer)
 */

import { ref, computed, watchEffect, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

import VueMultiselect from 'vue-multiselect'

// --- i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из QuizAnswerController@edit:
 *  - answer    (QuizAnswerResource)
 *  - quizzes   (QuizResource[])
 *  - questions (QuizQuestionResource[])
 */
const props = defineProps({
    answer: {
        type: Object,
        required: true
    },
    quizzes: {
        type: Array,
        default: () => []
    },
    questions: {
        type: Array,
        default: () => []
    }
})

/**
 * Форма редактирования варианта ответа.
 *
 * Поля соответствуют модели / миграции:
 *  - quiz_id, quiz_question_id, text, is_correct,
 *    weight, sort, explanation, activity
 */
const form = useForm({
    _method: 'PUT',

    quiz_id: props.answer.quiz_id ?? null,
    quiz_question_id: props.answer.quiz_question_id ?? null,

    text: props.answer.text ?? '',
    explanation: props.answer.explanation ?? '',
    is_correct: Boolean(props.answer.is_correct),
    weight: props.answer.weight ?? 0,
    sort: props.answer.sort ?? 0,
    activity: Boolean(props.answer.activity)
})

/** Опции квизов */
const quizOptions = computed(() => props.quizzes ?? [])

/** Опции вопросов: сортируем по id DESC (от последнего к первому) */
const questionOptions = computed(() => {
    const list = props.questions ?? []
    return [...list].sort((a, b) => b.id - a.id)
})

/** Подпись для опции квиза: ID:x [locale] title */
const quizOptionLabel = (option) => {
    if (!option) return ''

    const idPart = `ID:${option.id}`
    const localePart = option.locale ? ` [${option.locale}]` : ''
    const titlePart = option.title ? ` ${option.title}` : ''

    return `${idPart}${localePart}${titlePart}`
}

/** Удаляем HTML и сокращаем текст вопроса */
const stripHtml = (html = '') => {
    return html
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

const shortText = (html, limit = 100) => {
    const clean = stripHtml(html)
    return clean.length > limit ? clean.slice(0, limit) + '…' : clean
}

/** Подпись для вопроса: ID:x <обрезанный question_text> (quiz:id) */
const questionOptionLabel = (question) => {
    if (!question) return ''

    const idPart = `ID:${question.id}`
    const textPart = question.question_text ? ` ${shortText(question.question_text)}` : ''
    const quizPart = question.quiz_id ? ` (quiz:${question.quiz_id})` : ''

    return `${idPart}${textPart}${quizPart}`
}

/** Лимиты опций для мультиселектов */
const dynamicQuizOptionsLimit = computed(() => {
    const count = quizOptions.value.length
    return count + 10
})

const dynamicQuestionOptionsLimit = computed(() => {
    const count = questionOptions.value.length
    return count + 10
})

/** Выбранный квиз и вопрос в мультиселектах */
const selectedQuiz = ref(null)
const selectedQuestion = ref(null)

/**
 * Инициализация selectedQuiz на основе form.quiz_id
 * (логика как в Edit.vue для вопросов)
 */
watchEffect(() => {
    const options = quizOptions.value

    if (!options.length) {
        selectedQuiz.value = null
        form.quiz_id = null
        return
    }

    if (form.quiz_id) {
        const found = options.find(o => o.id === form.quiz_id)

        if (found) {
            selectedQuiz.value = found
            form.quiz_id = found.id
        } else {
            selectedQuiz.value = null
            form.quiz_id = null
        }
    } else {
        selectedQuiz.value = null
        form.quiz_id = null
    }
})

/**
 * Инициализация selectedQuestion на основе form.quiz_question_id
 */
watchEffect(() => {
    const options = questionOptions.value

    if (!options.length) {
        selectedQuestion.value = null
        form.quiz_question_id = null
        return
    }

    if (form.quiz_question_id) {
        const found = options.find(o => o.id === form.quiz_question_id)

        if (found) {
            selectedQuestion.value = found
            form.quiz_question_id = found.id
        } else {
            selectedQuestion.value = null
            form.quiz_question_id = null
        }
    } else {
        selectedQuestion.value = null
        form.quiz_question_id = null
    }
})

/**
 * Синхронизация form.quiz_id с выбранным квизом.
 * Если выбран квиз, а текущий вопрос к нему не относится — сбрасываем вопрос.
 */
watch(selectedQuiz, (val) => {
    form.quiz_id = val ? val.id : null

    if (val && selectedQuestion.value && selectedQuestion.value.quiz_id !== val.id) {
        selectedQuestion.value = null
        form.quiz_question_id = null
    }
})

/**
 * Синхронизация form.quiz_question_id с выбранным вопросом
 * и при необходимости подставляем quiz_id по question.quiz_id
 */
watch(selectedQuestion, (val) => {
    form.quiz_question_id = val ? val.id : null

    if (val && val.quiz_id) {
        const relatedQuiz = quizOptions.value.find(o => o.id === val.quiz_id)
        if (relatedQuiz) {
            selectedQuiz.value = relatedQuiz
            form.quiz_id = relatedQuiz.id
        }
    }
})

/**
 * Отправка формы обновления варианта ответа.
 */
const submitForm = () => {
    form.transform((data) => {
        return {
            ...data,
            activity: data.activity ? 1 : 0,
            is_correct: data.is_correct ? 1 : 0
        }
    })

    form.post(route('admin.quizAnswers.update', props.answer.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Вариант ответа квиза успешно обновлён!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении варианта ответа квиза:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Проверьте правильность заполнения полей.')
        }
    })
}
</script>

<template>
    <AdminLayout :title="t('editQuizAnswer')">
        <template #header>
            <TitlePage>
                {{ t('editQuizAnswer') }}
                — ID: {{ answer.id }}
                <span
                    v-if="answer.quiz"
                    class="text-xs text-gray-600 dark:text-gray-400"
                >
                    ({{ t('quiz') }}: {{ answer.quiz.title }} [#{{ answer.quiz.id }}])
                </span>
                <span
                    v-if="answer.question"
                    class="ml-2 text-xs text-gray-600 dark:text-gray-400"
                >
                    ({{ t('quizQuestion') }}: #{{ answer.question.id }})
                </span>
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Кнопка назад -->
                <div class="sm:flex sm:justify-between sm:items-center mb-4">
                    <DefaultButton
                        :href="route('admin.quizAnswers.index', {
                            quiz_id: form.quiz_id || undefined,
                            quiz_question_id: form.quiz_question_id || undefined
                        })"
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
                    <!-- Строка: активность, правильный, вес, сортировка -->
                    <div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <!-- Активность -->
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <!-- Правильный ответ -->
                        <div class="flex flex-row items-center gap-2">
                            <input
                                id="is_correct"
                                type="checkbox"
                                v-model="form.is_correct"
                                class="h-5 w-5 text-cyan-600 border-gray-500 rounded
                                       focus:ring-cyan-500 dark:bg-teal-500
                                       dark:border-slate-300 dark:focus:ring-cyan-600
                                       dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800"
                            />
                            <LabelCheckbox
                                for="is_correct"
                                :text="t('isCorrect')"
                                class="text-sm h-8 flex items-center"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.is_correct"
                            />
                        </div>

                        <!-- Вес -->
                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput
                                    for="weight"
                                    :value="t('points')"
                                />
                            </div>
                            <InputNumber
                                id="weight"
                                type="number"
                                min="0"
                                v-model="form.weight"
                                autocomplete="weight"
                                class="w-28"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.weight"
                            />
                        </div>

                        <!-- Сортировка -->
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
                                min="0"
                                v-model="form.sort"
                                autocomplete="sort"
                                class="w-28"
                            />
                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.sort"
                            />
                        </div>
                    </div>

                    <!-- Квиз -->
                    <div class="mb-4 flex flex-col items-start w-full">
                        <LabelInput
                            for="quiz"
                            :value="t('quiz')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="quiz"
                            v-model="selectedQuiz"
                            :options="quizOptions"
                            :options-limit="dynamicQuizOptionsLimit"
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
                            :message="form.errors.quiz_id"
                        />
                    </div>

                    <!-- Вопрос -->
                    <div class="mb-4 flex flex-col items-start w-full">
                        <LabelInput
                            for="quiz_question"
                            :value="t('quizQuestion')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="quiz_question"
                            v-model="selectedQuestion"
                            :options="questionOptions"
                            :options-limit="dynamicQuestionOptionsLimit"
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
                            :message="form.errors.quiz_question_id"
                        />
                    </div>

                    <!-- Текст ответа -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput
                            for="text"
                            :value="t('answer')"
                        />
                        <TinyEditor
                            id="text"
                            v-model="form.text"
                            :height="250"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.text"
                        />
                    </div>

                    <!-- Объяснение -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput
                            for="explanation"
                            :value="t('explanation')"
                        />
                        <TinyEditor
                            id="explanation"
                            v-model="form.explanation"
                            :height="220"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.explanation"
                        />
                    </div>

                    <!-- Кнопки сохранить / назад -->
                    <div class="flex items-center justify-center mt-6 gap-3">
                        <DefaultButton
                            :href="route('admin.quizAnswers.index', {
                                quiz_id: form.quiz_id || undefined,
                                quiz_question_id: form.quiz_question_id || undefined
                            })"
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
