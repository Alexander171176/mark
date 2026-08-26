<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

/* ==========================================================
 * PROPS
 * ========================================================== */

const props = defineProps({
    questions: {
        type: Array,
        default: () => [],
    },

    selectedQuestions: {
        type: Array,
        default: () => [],
    },
})

/* ==========================================================
 * EMITS
 * ========================================================== */

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

/* ==========================================================
 * ЛОКАЛЬНАЯ КОЛЛЕКЦИЯ
 * ========================================================== */

const localQuestions = ref([])

watch(
    () => props.questions,
    (newVal) => {
        localQuestions.value = JSON.parse(
            JSON.stringify(
                newVal || []
            )
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ==========================================================
 * DRAG & DROP
 * ========================================================== */

const handleDragEnd = () => {
    const newOrderIds =
        localQuestions.value.map(
            (question) => question.id
        )

    emit(
        'update-sort-order',
        newOrderIds
    )
}

/* ==========================================================
 * ВЫБОР
 * ========================================================== */

const toggleAll = (event) => {
    const checked =
        event.target.checked

    const ids =
        localQuestions.value.map(
            (question) => question.id
        )

    emit(
        'toggle-all',
        {
            ids,
            checked,
        }
    )
}

/* ==========================================================
 * RESOURCE HELPERS
 * ========================================================== */

/**
 * Текст вопроса.
 */
const getQuestionText = (question) => {
    return question?.translation?.question_text
        || `ID: ${question?.id}`
}

/**
 * Объяснение вопроса.
 */
const getQuestionExplanation = (question) => {
    return question?.translation?.explanation
        || ''
}

/**
 * Заголовок квиза.
 */
const getQuizTitle = (question) => {
    return question?.quiz?.translation?.title
        || question?.quiz?.slug
        || `Quiz ID: ${question?.school_quiz_id}`
}

/* ==========================================================
 * ТЕКСТ
 * ========================================================== */

const stripHtml = (html) => {
    if (!html) {
        return ''
    }

    return html
        .replace(/<\/p>/gi, '\n')
        .replace(/<br\s*\/?>/gi, '\n')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/&amp;/gi, '&')
        .replace(/&quot;/gi, '"')
        .replace(/&#039;/gi, "'")
        .replace(/&lt;/gi, '<')
        .replace(/&gt;/gi, '>')
        .replace(/\s+/g, ' ')
        .trim()
}

const shortText = (
    html,
    length = 120
) => {
    const clean =
        stripHtml(html)

    return clean.length > length
        ? clean.slice(
        0,
        length
    ) + '…'
        : clean
}

/* ==========================================================
 * ПРАВИЛЬНЫЕ ОТВЕТЫ
 * ========================================================== */

const getCorrectAnswersSummary = (
    question
) => {
    if (!question?.answers?.length) {
        return '—'
    }

    const correct =
        question.answers.filter(
            (answer) =>
                Boolean(
                    answer.is_correct
                )
        )

    if (!correct.length) {
        return '—'
    }

    const list = correct
        .map(
            (answer) =>
                answer?.translation?.text
                || ''
        )
        .filter(Boolean)

    if (!list.length) {
        return '—'
    }

    const sliced =
        list
            .slice(0, 3)
            .join('; ')

    return sliced
        + (
            list.length > 3
                ? '…'
                : ''
        )
}

/* ==========================================================
 * ТИП ВОПРОСА
 * ========================================================== */

const questionTypeLabelKeyMap = {
    single_choice:
        'questionTypeSingleChoice',

    multiple_choice:
        'questionTypeMultipleChoice',

    true_false:
        'questionTypeTrueFalse',

    open_text:
        'questionTypeOpenText',
}

const getQuestionTypeLabel = (
    type
) => {
    if (!type) {
        return '—'
    }

    const key =
        questionTypeLabelKeyMap[
            type
            ]

    return key
        ? t(key)
        : type
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700
               shadow-lg rounded-sm
               border border-slate-400
               dark:border-slate-500 relative"
    >
        <!-- ==================================================
             TOP PANEL
             ================================================== -->

        <div
            class="flex items-center justify-between
                   px-3 py-2
                   border-b border-slate-400
                   dark:border-slate-500"
        >
            <div
                class="text-xs
                       text-slate-600
                       dark:text-slate-200"
            >
                {{ t('selected') }}:
                {{ selectedQuestions.length }}
            </div>

            <label
                v-if="localQuestions.length"
                class="flex items-center
                       text-xs text-slate-600
                       dark:text-slate-200
                       cursor-pointer"
            >
                <span>
                    {{ t('selectAll') }}
                </span>

                <input
                    type="checkbox"
                    class="mx-2"
                    @change="toggleAll"
                />
            </label>
        </div>

        <!-- ==================================================
             GRID
             ================================================== -->

        <div
            v-if="localQuestions.length"
            class="p-3"
        >
            <draggable
                v-model="localQuestions"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid gap-3
                       grid-cols-1
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template
                    #item="{ element: question }"
                >
                    <article
                        class="relative flex flex-col h-full
                               rounded-md
                               border border-slate-400
                               dark:border-slate-500
                               bg-slate-50/70
                               dark:bg-slate-800/80
                               shadow-sm hover:shadow-md
                               transition-shadow duration-150"
                    >
                        <!-- =====================================
                             HEADER
                             ===================================== -->

                        <header
                            class="flex items-center
                                   justify-between
                                   px-2 py-1
                                   border-b border-dashed
                                   border-slate-400
                                   dark:border-slate-500"
                        >
                            <div
                                class="flex items-center
                                       space-x-2"
                            >
                                <button
                                    type="button"
                                    class="drag-handle
                                           text-slate-400
                                           hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px]
                                           font-semibold
                                           px-2 py-0.5
                                           rounded-sm
                                           border border-gray-400
                                           bg-slate-200
                                           dark:bg-slate-700
                                           text-slate-800
                                           dark:text-blue-100"
                                    :title="`sort: ${question.sort ?? '—'}`"
                                >
                                    ID:
                                    {{ question.id }}
                                </div>
                            </div>

                            <div
                                class="flex items-center
                                       space-x-2"
                            >
                                <span
                                    class="text-[10px]
                                           px-1.5 py-0.5
                                           rounded-sm
                                           font-semibold
                                           border border-gray-400
                                           bg-amber-100
                                           dark:bg-amber-900
                                           text-amber-700
                                           dark:text-amber-300"
                                    :title="t('points')"
                                >
                                    {{ t('points') }}:
                                    {{ question.points ?? 0 }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="
                                        selectedQuestions.includes(
                                            question.id
                                        )
                                    "
                                    @change="
                                        emit(
                                            'toggle-select',
                                            question.id
                                        )
                                    "
                                />
                            </div>
                        </header>

                        <!-- =====================================
                             BODY
                             ===================================== -->

                        <div
                            class="flex flex-col
                                   flex-1
                                   px-3 py-2
                                   space-y-2
                                   text-[11px]"
                        >
                            <!-- Quiz -->

                            <div
                                class="text-[11px]
                                       text-center
                                       font-semibold
                                       text-blue-600
                                       dark:text-blue-300"
                                :title="
                                    getQuizTitle(
                                        question
                                    )
                                "
                            >
                                {{
                                    getQuizTitle(
                                        question
                                    )
                                }}
                            </div>

                            <!-- Question -->

                            <div
                                class="text-xs
                                       text-slate-800
                                       dark:text-slate-100
                                       font-semibold
                                       border border-dashed
                                       border-slate-400
                                       bg-slate-100/70
                                       dark:bg-slate-900/40
                                       rounded-sm
                                       px-2 py-1
                                       min-h-[3rem]"
                                :title="
                                    shortText(
                                        getQuestionText(
                                            question
                                        ),
                                        300
                                    )
                                "
                            >
                                {{
                                    shortText(
                                        getQuestionText(
                                            question
                                        )
                                    )
                                }}
                            </div>

                            <!-- Explanation -->

                            <div
                                v-if="
                                    getQuestionExplanation(
                                        question
                                    )
                                "
                                class="text-[10px]
                                       text-slate-600
                                       dark:text-slate-300
                                       font-semibold"
                                :title="
                                    shortText(
                                        getQuestionExplanation(
                                            question
                                        ),
                                        300
                                    )
                                "
                            >
                                {{
                                    shortText(
                                        getQuestionExplanation(
                                            question
                                        ),
                                        90
                                    )
                                }}
                            </div>

                            <!-- Type -->

                            <div
                                class="text-center
                                       text-[10px]
                                       px-2 py-0.5
                                       rounded-sm
                                       font-semibold
                                       border border-slate-400
                                       bg-fuchsia-50
                                       dark:bg-fuchsia-900/40
                                       text-fuchsia-700
                                       dark:text-fuchsia-200"
                            >
                                {{
                                    getQuestionTypeLabel(
                                        question.question_type
                                    )
                                }}
                            </div>

                            <!-- Correct answers -->

                            <div
                                class="flex flex-wrap
                                       justify-between
                                       gap-2 mt-1
                                       text-[10px]"
                            >
                                <span
                                    class="text-[10px]
                                           px-2 py-0.5
                                           rounded-sm
                                           border border-slate-400
                                           bg-emerald-50
                                           dark:bg-emerald-900/40
                                           text-emerald-700
                                           dark:text-emerald-200"
                                >
                                    <span>
                                        {{ t('answers') }}:
                                    </span>

                                    <span
                                        class="font-semibold"
                                    >
                                        {{
                                            getCorrectAnswersSummary(
                                                question
                                            )
                                        }}
                                    </span>
                                </span>
                            </div>

                            <!-- Counts -->

                            <div
                                class="grid grid-cols-2
                                       gap-1
                                       text-[10px]
                                       text-center"
                            >
                                <span
                                    class="border border-dashed
                                           border-slate-300
                                           dark:border-slate-600
                                           rounded-sm px-1"
                                >
                                    {{ t('answers') }}:
                                    {{
                                        question.answers_count
                                        ?? 0
                                    }}
                                </span>

                                <span
                                    class="border border-dashed
                                           border-slate-300
                                           dark:border-slate-600
                                           rounded-sm px-1"
                                >
                                    {{ t('attemptsLimit') }}:
                                    {{
                                        question.attempt_items_count
                                        ?? 0
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- =====================================
                             FOOTER
                             ===================================== -->

                        <footer
                            class="flex items-center
                                   justify-center
                                   px-3 py-2
                                   border-t border-dashed
                                   border-slate-400
                                   dark:border-slate-500"
                        >
                            <div
                                class="flex items-center
                                       space-x-1"
                            >
                                <ActivityToggle
                                    :isActive="
                                        question.activity
                                    "
                                    :title="
                                        question.activity
                                            ? t('enabled')
                                            : t('disabled')
                                    "
                                    @toggle-activity="
                                        emit(
                                            'toggle-activity',
                                            question
                                        )
                                    "
                                />

                                <IconEdit
                                    :href="
                                        route(
                                            'admin.schoolQuizQuestions.edit',
                                            {
                                                schoolQuizQuestion:
                                                    question.id,
                                            }
                                        )
                                    "
                                />

                                <DeleteIconButton
                                    :title="t('delete')"
                                    @delete="
                                        emit(
                                            'delete',
                                            question
                                        )
                                    "
                                />
                            </div>
                        </footer>
                    </article>
                </template>
            </draggable>
        </div>

        <!-- ==================================================
             EMPTY
             ================================================== -->

        <div
            v-else
            class="p-5 text-center
                   text-slate-700
                   dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
