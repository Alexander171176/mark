<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование вопроса квиза (SchoolQuizQuestion)
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
    question: { type: Object, required: true },
    quizzes: { type: Array, default: () => [] },
})

// Создание пустой структуры перевода
const makeTranslation = () => ({
    question_text: '',
    explanation: '',
})

// Сбор переводов из существующей записи
const buildTranslations = () => {
    const result = {}

    ;(props.question.translations || []).forEach((translation) => {
        result[translation.locale] = {
            question_text: translation.question_text || '',
            explanation: translation.explanation || '',
        }
    })

    const defaultLocale =
        props.currentLocale ||
        props.question.translation?.locale ||
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

// Активная локаль по умолчанию
const defaultLocale =
    props.currentLocale ||
    props.question.translation?.locale ||
    props.availableLocales[0] ||
    'ru'

// Текущая активная локаль
const activeLocale = ref(defaultLocale)

// Основная форма редактирования
const form = useForm({
    _method: 'PUT',

    school_quiz_id:
        props.question.school_quiz_id ??
        props.question.quiz?.id ??
        null,

    sort: props.question.sort ?? 0,
    question_type: props.question.question_type ?? 'single_choice',
    points: props.question.points ?? 1,
    activity: Boolean(props.question.activity),

    meta_raw: props.question.meta
        ? JSON.stringify(props.question.meta, null, 2)
        : '',

    translations: buildTranslations(),
})

// Получение активного перевода
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// Очистка HTML тегов и спецсимволов
const stripHtml = (value) => {
    return (value || '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/<[^>]*>/g, '')
        .replace(/\s+/g, ' ')
        .trim()
}

// Заголовок страницы редактирования
const pageTitle = computed(() => {
    return stripHtml(
        currentTranslation.value.question_text ||
        props.question.translation?.question_text ||
        props.question.question_text ||
        `ID: ${props.question.id}`
    )
})

// Получение ошибок текущей локали
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

// Ограничение количества элементов мультиселекта
const dynamicOptionsLimit = computed(() => {
    const count = props.quizzes?.length ?? 0

    return count + 10
})

// Опции квизов
const quizOptions = computed(() => props.quizzes ?? [])

// Подпись квиза в мультиселекте
const quizOptionLabel = (option) => {
    if (!option) return ''

    const idPart = `[ID: ${option.id}]`
    const titlePart = option.title || option.slug || `#${option.id}`

    return `${idPart} ${titlePart}`
}

// Выбранный квиз
const selectedQuiz = ref(null)

// Синхронизация выбранного квиза с текущим вопросом
watch(
    quizOptions,
    (options) => {
        selectedQuiz.value = options.find(
            item => Number(item.id) === Number(form.school_quiz_id)
        ) || null
    },
    { immediate: true }
)

// Выбранный квиз
watch(selectedQuiz, (val) => {
    form.school_quiz_id = val?.id ?? null
})

// Карта типов вопросов
const questionTypeLabelKeyMap = {
    single_choice: 'questionTypeSingleChoice',
    multiple_choice: 'questionTypeMultipleChoice',
    true_false: 'questionTypeTrueFalse',
    open_text: 'questionTypeOpenText',
}

// Опции типов вопросов
const questionTypeOptions = Object.entries(questionTypeLabelKeyMap).map(
    ([value, labelKey]) => ({ value, labelKey })
)

// Отправка формы
const submitForm = () => {
    form.transform((data) => {
        const { meta_raw, ...rest } = data

        let meta = null

        if (meta_raw && meta_raw.trim()) {
            try {
                meta = JSON.parse(meta_raw)
            } catch (e) {
                console.error('Ошибка парсинга meta JSON:', e)
            }
        }

        return {
            ...rest,
            school_quiz_id: selectedQuiz.value?.id ?? null,
            activity: data.activity ? 1 : 0,
            sort: data.sort === '' || data.sort === null ? 0 : Number(data.sort),
            points: data.points === '' || data.points === null ? 1 : Number(data.points),
            meta,
        }
    })

    form.post(route('admin.schoolQuizQuestions.update', {
        schoolQuizQuestion: props.question.id,
    }), {
        errorBag: 'editSchoolQuizQuestion',
        preserveScroll: true,
        onSuccess: () => toast.success('Вопрос квиза успешно обновлён!'),
        onError: (errors) => {
            console.error('Ошибка обновления вопроса квиза:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editQuizQuestion')">
        <template #header>
            <TitlePage>
                {{ t('editQuizQuestion') }}: {{ pageTitle }} [ID: {{ props.question.id }}]
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
                    <DefaultButton :href="route('admin.schoolQuizQuestions.index')">
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
                        <div class="mb-4 flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <LabelInput
                                    for="points"
                                    :value="t('points')"
                                />
                                <InputNumber
                                    id="points"
                                    type="number"
                                    min="0"
                                    v-model="form.points"
                                    autocomplete="points"
                                    class="w-28"
                                />
                                <InputError :message="form.errors.points" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <LabelInput
                                    for="sort"
                                    :value="t('sort')"
                                    class="text-sm"
                                />
                                <InputNumber
                                    id="sort"
                                    type="number"
                                    min="0"
                                    v-model="form.sort"
                                    autocomplete="sort"
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
                                :message="form.errors.school_quiz_id"
                            />
                        </div>

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
                                <LabelInput for="question_text">
                                    <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                    {{ t('question') }} [{{ activeLocale.toUpperCase() }}]
                                </LabelInput>
                                <TinyEditor
                                    id="question_text"
                                    v-model="currentTranslation.question_text"
                                    :height="350"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="getError('question_text')"
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
                                    :height="250"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="getError('explanation')"
                                />
                            </div>
                        </div>

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
                                class="mt-1 block w-full border border-slate-300
                                       dark:border-slate-600 bg-white dark:bg-slate-800
                                       text-sm rounded-md px-3 py-2 font-mono"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.meta"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton :href="route('admin.schoolQuizQuestions.index')">
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
