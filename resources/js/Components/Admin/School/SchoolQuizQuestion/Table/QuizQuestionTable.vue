<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'

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
 *
 * SchoolQuizQuestionSharedResource:
 *
 * question.translation.question_text
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
 * Заголовок родительского квиза.
 *
 * SchoolQuizSharedResource:
 *
 * question.quiz.translation.title
 */
const getQuizTitle = (question) => {
    return question?.quiz?.translation?.title
        || question?.quiz?.slug
        || `#${question?.school_quiz_id}`
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

/**
 * Index загружает answers +
 * translations выбранной locale.
 *
 * Поэтому текст ответа:
 *
 * answer.translation.text
 */
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
            .join(', ')

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
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <!-- ==================================================
             TOP PANEL
             ================================================== -->

        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div
                class="text-xs text-slate-600 dark:text-slate-200"
            >
                {{ t('selected') }}:
                {{ selectedQuestions.length }}
            </div>

            <label
                v-if="localQuestions.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
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
             TABLE
             ================================================== -->

        <div class="overflow-x-auto">
            <table
                v-if="localQuestions.length"
                class="table-auto w-full
                       text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-xs uppercase
                           bg-slate-200 dark:bg-cyan-900
                           border border-solid
                           border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <!-- Drag -->

                    <th
                        class="px-2 py-3
                                   w-px text-center"
                    >
                        <svg
                            class="w-4 h-4 opacity-60"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12.707 2.293a1 1 0 0 0-1.414 0l-5 5A1 1 0 1 0 7.707 8.707L12 4.414l4.293 4.293a1 1 0 0 0 1.414-1.414l-5-5z"
                            />

                            <path
                                d="M16.293 15.293 12 19.586l-4.293-4.293a1 1 0 0 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l5-5a1 1 0 0 0-1.414-1.414z"
                            />
                        </svg>
                    </th>

                    <!-- ID -->

                    <th
                        class="font-medium
                                   px-2 py-3
                                   w-px text-center"
                    >
                        ID
                    </th>

                    <!-- Quiz -->

                    <th
                        class="font-medium
                                   px-2 py-3
                                   whitespace-nowrap
                                   text-center"
                    >
                        {{ t('quiz') }}
                    </th>

                    <!-- Question -->

                    <th
                        class="font-medium
                                   px-2 py-3
                                   whitespace-nowrap
                                   text-left"
                    >
                        {{ t('question') }}
                    </th>

                    <!-- Answers -->

                    <th
                        class="font-medium
                                   px-2 py-3
                                   whitespace-nowrap
                                   text-center"
                    >
                        {{ t('answers') }}
                    </th>

                    <!-- Points -->

                    <th
                        class="font-medium
                                   px-2 py-3
                                   whitespace-nowrap
                                   text-center"
                    >
                        {{ t('points') }}
                    </th>

                    <!-- Actions -->

                    <th
                        class="font-medium
                                   px-2 py-3
                                   whitespace-nowrap
                                   text-end"
                    >
                        {{ t('actions') }}
                    </th>

                    <!-- Select -->

                    <th
                        class="px-2 py-3
                                   whitespace-nowrap
                                   text-center"
                    >
                        <input
                            type="checkbox"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localQuestions"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template
                        #item="{ element: question }"
                    >
                        <tr
                            class="text-sm font-semibold
                                   border-b-2
                                   hover:bg-slate-100
                                   dark:hover:bg-cyan-800"
                        >
                            <!-- Drag -->

                            <td
                                class="px-2 py-1
                                       text-center
                                       cursor-move handle"
                            >
                                <svg
                                    class="w-4 h-4
                                           text-gray-500
                                           dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <!-- ID -->

                            <td
                                class="px-2 py-3
                                       text-center text-xs
                                       whitespace-nowrap"
                                :title="`sort: ${question.sort ?? '—'}`"
                            >
                                {{ question.id }}
                            </td>

                            <!-- Quiz -->

                            <td
                                class="px-2 py-3
                                       text-center text-xs
                                       text-blue-600
                                       dark:text-blue-300"
                                :title="getQuizTitle(question)"
                            >
                                {{ getQuizTitle(question) }}
                            </td>

                            <!-- Question -->

                            <td
                                class="px-2 py-3
                                       text-xs min-w-72"
                            >
                                <div
                                    class="font-semibold
                                           text-slate-800
                                           dark:text-slate-100"
                                    :title="shortText(
                                        getQuestionText(question),
                                        300
                                    )"
                                >
                                    {{
                                        shortText(
                                            getQuestionText(
                                                question
                                            )
                                        )
                                    }}
                                </div>

                                <div
                                    v-if="
                                        getQuestionExplanation(
                                            question
                                        )
                                    "
                                    class="mt-1 text-[10px]
                                           text-slate-500
                                           dark:text-slate-300"
                                    :title="shortText(
                                        getQuestionExplanation(
                                            question
                                        ),
                                        300
                                    )"
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
                            </td>

                            <!-- Answers -->

                            <td
                                class="px-2 py-3
                                       text-center text-xs"
                            >
                                <div
                                    class="text-fuchsia-700
                                           dark:text-fuchsia-200"
                                >
                                    {{
                                        getQuestionTypeLabel(
                                            question.question_type
                                        )
                                    }}
                                </div>

                                <div
                                    class="text-emerald-600
                                           dark:text-emerald-300"
                                >
                                    {{
                                        getCorrectAnswersSummary(
                                            question
                                        )
                                    }}
                                </div>
                            </td>

                            <!-- Points -->

                            <td
                                class="px-2 py-3
                                       text-center text-xs
                                       text-amber-600
                                       dark:text-amber-300"
                            >
                                {{ question.points ?? 0 }}
                            </td>

                            <!-- Actions -->

                            <td
                                class="px-2 py-3
                                       whitespace-nowrap"
                            >
                                <div
                                    class="flex justify-end
                                           space-x-2"
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
                            </td>

                            <!-- Select -->

                            <td
                                class="px-2 py-3
                                       text-center"
                            >
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
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

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
    </div>
</template>
