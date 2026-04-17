<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Редактирование вопроса квиза (QuizQuestion)
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
 * Пропсы из QuizQuestionController@edit:
 *  - question (QuizQuestionResource)
 *  - quizzes  (QuizResource[])
 */
const props = defineProps({
    question: {
        type: Object,
        required: true
    },
    quizzes: {
        type: Array,
        default: () => []
    }
})

/**
 * Динамически считает кол-во строк для мультиселекта
 */
const dynamicOptionsLimit = computed(() => {
    const count = props.quizzes?.length ?? 0
    return count + 10
})

/**
 * Форма редактирования вопроса квиза.
 *
 * Поля соответствуют миграции:
 *  - quiz_id, sort, question_type, question_text,
 *    explanation, points, meta, activity
 */
const form = useForm({
    _method: 'PUT',

    quiz_id: props.question.quiz_id ?? null,
    sort: props.question.sort ?? 0,
    question_type: props.question.question_type ?? 'single_choice',
    question_text: props.question.question_text ?? '',
    explanation: props.question.explanation ?? '',
    points: props.question.points ?? 1,
    activity: Boolean(props.question.activity),

    /**
     * meta_raw — текстовое поле для редактирования JSON.
     * В transform() мы превратим его в meta (array/object|null).
     */
    meta_raw: props.question.meta
        ? JSON.stringify(props.question.meta, null, 2)
        : ''
})

/**
 * Опции селекта Квиза — берём ресурсы как есть (QuizResource)
 * Тут должны быть поля: id, locale, title, ...
 */
const quizOptions = computed(() => props.quizzes ?? [])

/**
 * Кастомная подпись опции:
 * ID:1 [ru] Название
 */
const quizOptionLabel = (option) => {
    if (!option) return ''

    const idPart = `ID:${option.id}`
    const localePart = option.locale ? ` [${option.locale}]` : ''
    const titlePart = option.title ? ` ${option.title}` : ''

    return `${idPart}${localePart}${titlePart}`
}

/**
 * Выбранный квиз в мультиселекте
 */
const selectedQuiz = ref(null)

/**
 * Инициализация selectedQuiz на основе form.quiz_id (текущий вопрос).
 * Логика такая же, как в Create.vue, но берём quiz_id из вопроса.
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
            // если по какой-то причине не нашли — сбрасываем
            selectedQuiz.value = null
            form.quiz_id = null
        }
    } else {
        // quiz_id нет — ничего не выбрано, юзер сам выбирает
        selectedQuiz.value = null
        form.quiz_id = null
    }
})

/**
 * Держим form.quiz_id в синхроне с выбранным объектом из мультиселекта
 */
watch(selectedQuiz, (val) => {
    form.quiz_id = val ? val.id : null
})

/**
 * Мапа: значение из БД → ключ перевода (как в таблице)
 */
const questionTypeLabelKeyMap = {
    single_choice: 'questionTypeSingleChoice',      // один правильный ответ
    multiple_choice: 'questionTypeMultipleChoice',  // несколько правильных ответов
    true_false: 'questionTypeTrueFalse',            // верно/неверно
    open_text: 'questionTypeOpenText'               // развернутый ответ
}

/**
 * Опции типа вопроса — строим на основе мапы
 */
const questionTypeOptions = Object.entries(questionTypeLabelKeyMap).map(
    ([value, labelKey]) => ({ value, labelKey })
)

/**
 * Отправка формы обновления вопроса.
 * meta_raw -> meta (JSON)
 */
const submitForm = () => {
    form.transform((data) => {
        const { meta_raw, ...rest } = data

        let meta = null

        if (meta_raw && meta_raw.trim()) {
            try {
                meta = JSON.parse(meta_raw)
            } catch (e) {
                console.error('❌ Ошибка парсинга meta JSON:', e)
                // оставим meta = null — бэкенд может отвалидировать/отвергнуть
            }
        }

        return {
            ...rest,
            activity: rest.activity ? 1 : 0,
            meta
        }
    })

    form.post(route('admin.quizQuestions.update', props.question.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Вопрос квиза успешно обновлён!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении вопроса квиза:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Проверьте правильность заполнения полей.')
        }
    })
}
</script>

<template>
    <AdminLayout :title="t('editQuizQuestion')">
        <template #header>
            <TitlePage>
                {{ t('editQuizQuestion') }}
                — ID: {{ question.id }}
                <span v-if="question.quiz" class="text-xs text-gray-600 dark:text-gray-400">
                    ({{ t('quiz') }}: {{ question.quiz.title }} [#{{ question.quiz.id }}])
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
                    <DefaultButton :href="route('admin.quizQuestions.index', { quiz_id: question.quiz_id })">
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
                    <!-- Активность + Квиз + Сортировка -->
                    <div class="mb-4 flex flex-col
                                lg:flex-row gap-4 lg:items-center lg:justify-between">
                        <!-- Активность -->
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <!-- Баллы -->
                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput
                                    for="points"
                                    :value="t('points')"
                                />
                            </div>
                            <InputNumber
                                id="points"
                                type="number"
                                min="0"
                                v-model="form.points"
                                autocomplete="points"
                                class="w-28"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.points"
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
                            :options-limit="dynamicOptionsLimit"
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

                    <!-- Тип вопроса -->
                    <div class="mb-4 flex flex-col items-end">
                        <LabelInput
                            for="question_type"
                            :value="t('questionType')"
                        />
                        <select
                            id="question_type"
                            v-model="form.question_type"
                            class="block w-fit py-0.5 border-slate-500 text-md
                                       focus:border-indigo-500 focus:ring-indigo-300
                                       rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                        >
                            <option
                                v-for="opt in questionTypeOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ t(opt.labelKey) || opt.value }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.question_type"
                        />
                    </div>

                    <!-- Текст вопроса -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput
                            for="question_text"
                            :value="t('question')"
                        />
                        <TinyEditor
                            id="question_text"
                            v-model="form.question_text"
                            :height="350"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.question_text"
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
                            :height="250"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.explanation"
                        />
                    </div>

                    <!-- Meta (JSON) -->
                    <div class="mb-4 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="meta_raw"
                                :value="t('metaJson')"
                            />
                            <span class="text-xs text-slate-500 dark:text-slate-300 mt-1">
                                {{ t('metaJsonHint') }}
                            </span>
                        </div>
                        <textarea
                            id="meta_raw"
                            v-model="form.meta_raw"
                            rows="6"
                            class="mt-1 block w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm rounded-md px-3 py-2 font-mono"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.meta"
                        />
                    </div>

                    <!-- Кнопки сохранить / назад -->
                    <div class="flex items-center justify-center mt-6 gap-3">
                        <DefaultButton
                            :href="route('admin.quizQuestions.index', { quiz_id: question.quiz_id })"
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
