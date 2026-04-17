<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Создание варианта ответа квиза (QuizAnswer)
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
 * Пропсы из QuizAnswerController@create:
 *  - quizzes         (QuizResource[])
 *  - questions       (QuizQuestionResource[])
 *  - defaultQuizId   (int|null)
 *  - defaultQuestionId (int|null)
 */
const props = defineProps({
    quizzes: {
        type: Array,
        default: () => []
    },
    questions: {
        type: Array,
        default: () => []
    },
    defaultQuizId: {
        type: Number,
        default: null
    },
    defaultQuestionId: {
        type: Number,
        default: null
    }
})

/**
 * Форма создания варианта ответа.
 *
 * Поля соответствуют модели / миграции:
 *  - quiz_id, quiz_question_id, text, is_correct,
 *    weight, sort, explanation, activity
 */
const form = useForm({
    quiz_id: props.defaultQuizId ?? null,
    quiz_question_id: props.defaultQuestionId ?? null,

    text: '',
    explanation: '',
    is_correct: false,
    weight: 0,
    sort: 0,
    activity: true
})

/** Опции квизов */
const quizOptions = computed(() => props.quizzes ?? [])

/** Опции вопросов: сортируем по id DESC (от последнего к первому) */
const questionOptions = computed(() => {
    const list = props.questions ?? []
    return [...list].sort((a, b) => b.id - a.id)
})

/** Подпись для опции квиза: ID:[id] [locale] title */
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

/** Для VueMultiselect */
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
 * Инициализация selectedQuiz / selectedQuestion
 * на основе defaultQuizId / defaultQuestionId
 */
watchEffect(() => {
    const qz = quizOptions.value
    const qs = questionOptions.value

    // Инициализация квиза
    if (props.defaultQuizId && qz.length) {
        const foundQuiz = qz.find(o => o.id === props.defaultQuizId)
        if (foundQuiz) {
            selectedQuiz.value = foundQuiz
            form.quiz_id = foundQuiz.id
        }
    }

    // Инициализация вопроса
    if (props.defaultQuestionId && qs.length) {
        const foundQuestion = qs.find(o => o.id === props.defaultQuestionId)
        if (foundQuestion) {
            selectedQuestion.value = foundQuestion
            form.quiz_question_id = foundQuestion.id

            // Если квиз не выставлен, но есть quiz_id у вопроса — подставим
            if (!form.quiz_id && foundQuestion.quiz_id) {
                const relatedQuiz = qz.find(o => o.id === foundQuestion.quiz_id)
                if (relatedQuiz) {
                    selectedQuiz.value = relatedQuiz
                    form.quiz_id = relatedQuiz.id
                }
            }
        }
    }
})

/**
 * Синхронизация form.quiz_id с выбранным квизом
 */
watch(selectedQuiz, (val) => {
    form.quiz_id = val ? val.id : null

    // Если выбран квиз, а вопрос не из этого квиза — сбрасываем вопрос
    if (val && selectedQuestion.value && selectedQuestion.value.quiz_id !== val.id) {
        selectedQuestion.value = null
        form.quiz_question_id = null
    }
})

/**
 * Синхронизация form.quiz_question_id с выбранным вопросом
 * и при необходимости подставляем quiz_id
 */
watch(selectedQuestion, (val) => {
    form.quiz_question_id = val ? val.id : null

    // Если у вопроса есть quiz_id — подставим его в форму
    if (val && val.quiz_id) {
        const relatedQuiz = quizOptions.value.find(o => o.id === val.quiz_id)
        if (relatedQuiz) {
            selectedQuiz.value = relatedQuiz
            form.quiz_id = relatedQuiz.id
        }
    }
})

/**
 * Отправка формы создания ответа.
 */
const submitForm = () => {
    form.transform((data) => {
        return {
            ...data,
            activity: data.activity ? 1 : 0,
            is_correct: data.is_correct ? 1 : 0
        }
    })

    form.post(route('admin.quizAnswers.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Вариант ответа успешно создан!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при создании варианта ответа:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Проверьте правильность заполнения полей.')
        }
    })
}
</script>

<template>
    <AdminLayout :title="t('createQuizAnswer')">
        <template #header>
            <TitlePage>
                {{ t('createQuizAnswer') }}
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
