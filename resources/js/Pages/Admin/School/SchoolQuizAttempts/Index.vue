<script setup>
/**
 * Список попыток прохождения викторин
 *
 * - режимы обработки: frontend | server | auto
 * - локальный/серверный поиск
 * - локальная/серверная пагинация
 */

import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import SortSelect from '@/Components/Admin/School/SchoolQuizAttempt/Sort/SortSelect.vue'
import QuizAttemptTable from '@/Components/Admin/School/SchoolQuizAttempt/Table/QuizAttemptTable.vue'
import QuizAttemptCardGrid from '@/Components/Admin/School/SchoolQuizAttempt/View/QuizAttemptCardGrid.vue'
import BulkActionSelect from '@/Components/Admin/School/SchoolQuizAttempt/Select/BulkActionSelect.vue'

/* ==========================================================
 * I18N / TOAST
 * ========================================================== */

const { t } = useI18n()
const toast = useToast()

/* ==========================================================
 * PROPS
 * ========================================================== */

const props = defineProps({
    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    adminSchoolQuizAttemptsProcessingMode: {
        type: String,
        default: 'frontend',
    },

    attempts: {
        type: [Array, Object],
        default: () => [],
    },

    attemptsCount: {
        type: Number,
        default: 0,
    },

    filters: {
        type: Object,
        default: () => ({}),
    },

    adminSchoolQuizAttemptsPerPage: {
        type: Number,
        default: 10,
    },

    adminSchoolQuizAttemptsDefaultSort: {
        type: String,
        default: 'idDesc',
    },

    sortParam: {
        type: String,
        default: '',
    },

    search: {
        type: String,
        default: '',
    },

    users: {
        type: Array,
        default: () => [],
    },

    quizzes: {
        type: Array,
        default: () => [],
    },

    enrollments: {
        type: Array,
        default: () => [],
    },

    courses: {
        type: Array,
        default: () => [],
    },

    modules: {
        type: Array,
        default: () => [],
    },

    lessons: {
        type: Array,
        default: () => [],
    },
})

/* ==========================================================
 * VIEW MODE
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem(
        'admin_view_mode_quiz_attempts'
    ) || 'table'
)

watch(
    viewMode,
    (value) => {
        localStorage.setItem(
            'admin_view_mode_quiz_attempts',
            value
        )
    }
)

/* ==========================================================
 * ATTEMPTS
 * ========================================================== */

/**
 * Нормализация ResourceCollection:
 *
 * frontend:
 * attempts = [...]
 *
 * server:
 * attempts.data = [...]
 */
const attemptsList = computed(() => {
    if (Array.isArray(props.attempts)) {
        return props.attempts
    }

    if (
        Array.isArray(
            props.attempts?.data
        )
    ) {
        return props.attempts.data
    }

    return []
})

/**
 * Локальная копия нужна frontend-mode
 * и локальным bulk-изменениям.
 */
const localAttempts = ref([])

watch(
    attemptsList,
    (newValue) => {
        localAttempts.value =
            JSON.parse(
                JSON.stringify(
                    newValue || []
                )
            )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ==========================================================
 * ITEMS PER PAGE
 * ========================================================== */

const itemsPerPage = ref(
    props.adminSchoolQuizAttemptsPerPage
    || 10
)

watch(
    itemsPerPage,
    (newValue) => {
        router.put(
            route(
                'admin.settings.updateAdminCountSchoolQuizAttempts'
            ),
            {
                value: newValue,
            },
            {
                preserveScroll: true,
                preserveState: true,

                onSuccess: () => {
                    toast.info(
                        `Показ ${newValue} элементов на странице.`
                    )
                },

                onError: (errors) => {
                    toast.error(
                        errors?.value
                        || 'Ошибка обновления кол-ва элементов.'
                    )
                },
            }
        )
    }
)

/* ==========================================================
 * SORT
 * ========================================================== */

const sortParam = ref(
    props.sortParam
    || props.adminSchoolQuizAttemptsDefaultSort
    || 'idDesc'
)

watch(
    sortParam,
    (newValue) => {
        currentPage.value = 1

        router.put(
            route(
                'admin.settings.updateAdminSortSchoolQuizAttempts'
            ),
            {
                value: newValue,
            },
            {
                preserveScroll: true,
                preserveState: true,

                onSuccess: () => {
                    /**
                     * В server-mode после сохранения
                     * настройки запрашиваем уже
                     * отсортированные данные backend.
                     */
                    if (
                        props.useServerProcessing
                    ) {
                        router.get(
                            window.location.pathname,
                            {
                                ...Object.fromEntries(
                                    new URLSearchParams(
                                        window.location.search
                                    )
                                ),

                                sort:
                                    newValue
                                    || undefined,

                                page:
                                undefined,
                            },
                            {
                                preserveScroll:
                                    true,

                                preserveState:
                                    false,

                                replace:
                                    true,
                            }
                        )
                    }

                    toast.info(
                        'Сортировка успешно изменена'
                    )
                },

                onError: (errors) => {
                    toast.error(
                        errors?.value
                        || 'Ошибка обновления сортировки.'
                    )
                },
            }
        )
    }
)

/* ==========================================================
 * SEARCH / PAGE
 * ========================================================== */

const currentPage = ref(1)

const searchQuery = ref(
    props.search || ''
)

/* ==========================================================
 * SAFE HELPERS
 * ========================================================== */

const normalize = (value) => {
    return (value ?? '')
        .toString()
        .trim()
        .toLowerCase()
}

const safeNumber = (value) => {
    const number =
        Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

const safeDate = (value) => {
    const time =
        new Date(
            value || 0
        ).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

/* ==========================================================
 * RESOURCE HELPERS
 * ========================================================== */

/**
 * Заголовок переводимой
 * связанной сущности.
 *
 * Controller заранее загружает:
 *
 * translations(currentLocale)
 *
 * SharedResource формирует:
 *
 * item.translation.title
 */
const getNestedTitle = (item) => {
    return item?.translation?.title
        || item?.translation?.name
        || ''
}

/**
 * Пользователь попытки.
 *
 * User не переводимый.
 */
const getUserName = (attempt) => {
    return attempt?.user?.name
        || ''
}

/**
 * Название квиза.
 */
const getQuizTitle = (attempt) => {
    return getNestedTitle(
        attempt?.quiz
    )
}

/**
 * Название курса.
 */
const getCourseTitle = (attempt) => {
    return getNestedTitle(
        attempt?.course
    )
}

/**
 * Название модуля.
 */
const getModuleTitle = (attempt) => {
    return getNestedTitle(
        attempt?.module
    )
}

/**
 * Название урока.
 */
const getLessonTitle = (attempt) => {
    return getNestedTitle(
        attempt?.lesson
    )
}

/* ==========================================================
 * FRONTEND SORT HELPERS
 * ========================================================== */

const byNumberAsc = (field) =>
    (a, b) =>
        safeNumber(a?.[field])
        - safeNumber(b?.[field])
        || safeNumber(a?.id)
        - safeNumber(b?.id)

const byNumberDesc = (field) =>
    (a, b) =>
        safeNumber(b?.[field])
        - safeNumber(a?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)

const byStringAsc = (field) =>
    (a, b) =>
        normalize(
            a?.[field]
        ).localeCompare(
            normalize(
                b?.[field]
            )
        )
        || safeNumber(a?.id)
        - safeNumber(b?.id)

const byStringDesc = (field) =>
    (a, b) =>
        normalize(
            b?.[field]
        ).localeCompare(
            normalize(
                a?.[field]
            )
        )
        || safeNumber(b?.id)
        - safeNumber(a?.id)

const byDateAsc = (field) =>
    (a, b) =>
        safeDate(
            a?.[field]
        )
        - safeDate(
            b?.[field]
        )
        || safeNumber(a?.id)
        - safeNumber(b?.id)

const byDateDesc = (field) =>
    (a, b) =>
        safeDate(
            b?.[field]
        )
        - safeDate(
            a?.[field]
        )
        || safeNumber(b?.id)
        - safeNumber(a?.id)

/* ==========================================================
 * FRONTEND SORT
 * ========================================================== */

const sortAttempts = (items) => {
    const list =
        (items || []).slice()

    /**
     * Быстрые status-фильтры,
     * реализованные через sortParam.
     */
    if (
        sortParam.value ===
        'inProgress'
    ) {
        return list.filter(
            (item) =>
                item.status
                === 'in_progress'
        )
    }

    if (
        sortParam.value ===
        'completed'
    ) {
        return list.filter(
            (item) =>
                item.status
                === 'completed'
        )
    }

    if (
        sortParam.value ===
        'graded'
    ) {
        return list.filter(
            (item) =>
                item.status
                === 'graded'
        )
    }

    const sortMap = {
        idAsc:
            byNumberAsc('id'),

        idDesc:
            byNumberDesc('id'),

        attemptAsc:
            byNumberAsc(
                'attempt_number'
            ),

        attemptDesc:
            byNumberDesc(
                'attempt_number'
            ),

        scoreAsc:
            byNumberAsc('score'),

        scoreDesc:
            byNumberDesc('score'),

        maxScoreAsc:
            byNumberAsc(
                'max_score'
            ),

        maxScoreDesc:
            byNumberDesc(
                'max_score'
            ),

        percentAsc:
            byNumberAsc('percent'),

        percentDesc:
            byNumberDesc('percent'),

        durationAsc:
            byNumberAsc(
                'duration_seconds'
            ),

        durationDesc:
            byNumberDesc(
                'duration_seconds'
            ),

        startedAtAsc:
            byDateAsc(
                'started_at'
            ),

        startedAtDesc:
            byDateDesc(
                'started_at'
            ),

        finishedAtAsc:
            byDateAsc(
                'finished_at'
            ),

        finishedAtDesc:
            byDateDesc(
                'finished_at'
            ),

        statusAsc:
            byStringAsc('status'),

        statusDesc:
            byStringDesc('status'),

        itemsAsc:
            byNumberAsc(
                'items_count'
            ),

        itemsDesc:
            byNumberDesc(
                'items_count'
            ),

        userNameAsc:
            (a, b) =>
                normalize(
                    getUserName(a)
                ).localeCompare(
                    normalize(
                        getUserName(b)
                    )
                )
                || safeNumber(a?.id)
                - safeNumber(b?.id),

        userNameDesc:
            (a, b) =>
                normalize(
                    getUserName(b)
                ).localeCompare(
                    normalize(
                        getUserName(a)
                    )
                )
                || safeNumber(b?.id)
                - safeNumber(a?.id),

        quizTitleAsc:
            (a, b) =>
                normalize(
                    getQuizTitle(a)
                ).localeCompare(
                    normalize(
                        getQuizTitle(b)
                    )
                )
                || safeNumber(a?.id)
                - safeNumber(b?.id),

        quizTitleDesc:
            (a, b) =>
                normalize(
                    getQuizTitle(b)
                ).localeCompare(
                    normalize(
                        getQuizTitle(a)
                    )
                )
                || safeNumber(b?.id)
                - safeNumber(a?.id),
    }

    return sortMap[
        sortParam.value
        ]
        ? list.sort(
            sortMap[
                sortParam.value
                ]
        )
        : list
}

/* ==========================================================
 * FRONTEND SEARCH
 * ========================================================== */

const filteredAttempts = computed(
    () => {
        let filtered =
            localAttempts.value
            || []

        const query =
            normalize(
                searchQuery.value
            )

        if (!query) {
            return sortAttempts(
                filtered
            )
        }

        filtered = filtered.filter(
            (attempt) => {
                const values = [
                    attempt?.id,
                    attempt?.status,
                    attempt?.attempt_number,
                    attempt?.score,
                    attempt?.max_score,
                    attempt?.percent,
                    attempt?.duration_seconds,

                    /**
                     * User.
                     */
                    attempt?.user?.name,
                    attempt?.user?.email,

                    /**
                     * Quiz.
                     */
                    getQuizTitle(
                        attempt
                    ),

                    attempt?.quiz?.slug,

                    /**
                     * Course.
                     */
                    getCourseTitle(
                        attempt
                    ),

                    attempt?.course?.slug,

                    /**
                     * Module.
                     */
                    getModuleTitle(
                        attempt
                    ),

                    attempt?.module?.slug,

                    /**
                     * Lesson.
                     */
                    getLessonTitle(
                        attempt
                    ),

                    attempt?.lesson?.slug,

                    /**
                     * Технические данные.
                     */
                    attempt?.ip_address,
                    attempt?.user_agent,
                ]

                return values.some(
                    (value) =>
                        normalize(
                            value
                        ).includes(
                            query
                        )
                )
            }
        )

        return sortAttempts(
            filtered
        )
    }
)

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const paginatedAttempts = computed(
    () => {
        const per =
            Number(
                itemsPerPage.value
                || 10
            )

        const start =
            (
                currentPage.value
                - 1
            ) * per

        return filteredAttempts.value
            .slice(
                start,
                start + per
            )
    }
)

/* ==========================================================
 * DISPLAYED DATA
 * ========================================================== */

const displayedAttempts = computed(
    () => {
        return props.useServerProcessing
            ? attemptsList.value
            : paginatedAttempts.value
    }
)

watch(
    [
        itemsPerPage,
        searchQuery,
    ],
    () => {
        currentPage.value = 1
    }
)

/* ==========================================================
 * DELETE
 * ========================================================== */

const showConfirmDeleteModal =
    ref(false)

const attemptToDelete =
    ref(null)

const confirmDelete = (attempt) => {
    attemptToDelete.value =
        attempt

    showConfirmDeleteModal.value =
        true
}

const closeModal = () => {
    showConfirmDeleteModal.value =
        false

    attemptToDelete.value =
        null
}

const deleteAttempt = () => {
    if (
        !attemptToDelete.value?.id
    ) {
        return
    }

    const idToDelete =
        attemptToDelete.value.id

    router.delete(
        route(
            'admin.schoolQuizAttempts.destroy',
            {
                schoolQuizAttempt:
                idToDelete,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Попытка ID: ${idToDelete} удалена.`
                )
            },

            onError: (errors) => {
                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                const errorMessage =
                    errors?.general
                    || errors?.[firstKey]
                    || 'Ошибка при удалении попытки.'

                toast.error(
                    `${errorMessage} ID: ${idToDelete}`
                )
            },

            onFinish: () =>
                closeModal(),
        }
    )
}

/* ==========================================================
 * LOCAL PATCH
 * ========================================================== */

/**
 * Локально обновляет попытку
 * после bulk-action.
 */
const patchAttempt = (
    attemptId,
    payload
) => {
    const index =
        localAttempts.value.findIndex(
            (item) =>
                item.id
                === attemptId
        )

    if (index === -1) {
        return
    }

    localAttempts.value[index] = {
        ...localAttempts.value[
            index
            ],

        ...payload,
    }
}

/* ==========================================================
 * SELECTION
 * ========================================================== */

const selectedAttempts = ref([])

const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedAttempts.value.map(
            (attempt) =>
                attempt.id
        )

    if (checked) {
        selectedAttempts.value = [
            ...new Set([
                ...selectedAttempts.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedAttempts.value =
        selectedAttempts.value.filter(
            (id) =>
                !ids.includes(id)
        )
}

const toggleSelectAttempt = (id) => {
    const index =
        selectedAttempts.value.indexOf(
            id
        )

    if (index > -1) {
        selectedAttempts.value.splice(
            index,
            1
        )

        return
    }

    selectedAttempts.value.push(
        id
    )
}

/* ==========================================================
 * BULK STATUS
 * ========================================================== */

const bulkUpdateStatus = (
    status
) => {
    if (
        !selectedAttempts.value.length
    ) {
        toast.warning(
            'Выберите попытки.'
        )

        return
    }

    const idsToUpdate = [
        ...selectedAttempts.value,
    ]

    router.put(
        route(
            'admin.actions.schoolQuizAttempts.bulkUpdateStatus'
        ),
        {
            ids:
            idsToUpdate,

            status,
        },
        {
            preserveScroll:
                true,

            preserveState:
                true,

            onSuccess: () => {
                idsToUpdate.forEach(
                    (id) =>
                        patchAttempt(
                            id,
                            {
                                status,
                            }
                        )
                )

                selectedAttempts.value =
                    []

                toast.success(
                    'Статус выбранных попыток обновлён.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.status
                    || errors?.general
                    || 'Ошибка массового обновления статуса.'
                )
            },
        }
    )
}

/* ==========================================================
 * BULK DELETE
 * ========================================================== */

const bulkDestroy = () => {
    if (
        !selectedAttempts.value.length
    ) {
        toast.warning(
            'Выберите попытки для удаления.'
        )

        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные попытки?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.schoolQuizAttempts.bulkDestroy'
        ),
        {
            data: {
                ids:
                selectedAttempts.value,

                ...(
                    props.filters
                        ?.school_quiz_id
                        ? {
                            school_quiz_id:
                            props.filters
                                .school_quiz_id,
                        }
                        : {}
                ),

                ...(
                    props.filters
                        ?.user_id
                        ? {
                            user_id:
                            props.filters
                                .user_id,
                        }
                        : {}
                ),

                ...(
                    props.filters
                        ?.status
                        ? {
                            status:
                            props.filters
                                .status,
                        }
                        : {}
                ),
            },

            preserveScroll:
                true,

            preserveState:
                false,

            onSuccess: () => {
                selectedAttempts.value =
                    []

                toast.success(
                    'Выбранные попытки успешно удалены.'
                )
            },

            onError: (errors) => {
                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors?.[firstKey]
                    || 'Ошибка массового удаления попыток.'
                )
            },
        }
    )
}

/* ==========================================================
 * BULK ACTION HANDLER
 * ========================================================== */

const handleBulkAction = (
    event
) => {
    const action =
        event.target.value

    if (
        action === 'selectAll'
    ) {
        toggleAll({
            target: {
                checked: true,
            },
        })
    } else if (
        action === 'deselectAll'
    ) {
        toggleAll({
            target: {
                checked: false,
            },
        })
    } else if (
        action.startsWith(
            'status:'
        )
    ) {
        bulkUpdateStatus(
            action.split(':')[1]
        )
    } else if (
        action === 'delete'
    ) {
        bulkDestroy()
    }

    event.target.value = ''
}
</script>

<template>
    <AdminLayout :title="t('quizAttempts')">
        <template #header>
            <TitlePage>
                {{ t('quizAttempts') }}
            </TitlePage>
        </template>

        <div
            class="px-2 py-2
                   w-full max-w-12xl mx-auto"
        >
            <div
                class="p-4
                       bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden
                       shadow-md shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- ==================================================
                     PROCESSING MODE
                     ================================================== -->

                <div
                    class="sm:flex sm:justify-end
                           sm:items-center mb-3"
                >
                    <ProcessingModeSwitcher
                        setting-key="adminSchoolQuizAttemptsProcessingMode"
                        :mode="adminSchoolQuizAttemptsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="attemptsCount"
                    />
                </div>

                <!-- ==================================================
                     SEARCH
                     ================================================== -->

                <SearchInput
                    v-if="
                        attemptsCount
                        && !useServerProcessing
                    "
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <ServerSearchInput
                    v-if="
                        attemptsCount
                        && useServerProcessing
                    "
                    v-model="searchQuery"
                />

                <!-- ==================================================
                     PER PAGE / SORT
                     ================================================== -->

                <div
                    v-if="attemptsCount"
                    class="flex justify-between
                           items-center
                           flex-col md:flex-row
                           my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="
                            itemsPerPage = $event
                        "
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSchoolQuizAttempts"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="
                            value =>
                                sortParam = value
                        "
                    />
                </div>

                <!-- ==================================================
                     COUNT / BULK / VIEW
                     ================================================== -->

                <div
                    v-if="attemptsCount"
                    class="flex justify-between
                           items-center
                           flex-col md:flex-row
                           my-3"
                >
                    <CountTable>
                        {{ attemptsCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="
                            handleBulkAction
                        "
                    />

                    <ToggleViewButton
                        v-model:viewMode="
                            viewMode
                        "
                    />
                </div>

                <!-- ==================================================
                     TOP PAGINATION
                     ================================================== -->

                <div
                    v-if="attemptsCount"
                    class="flex justify-center
                           items-center
                           flex-col md:flex-row
                           mb-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAttempts.length"
                        @update:currentPage="
                            currentPage = $event
                        "
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="attempts"
                    />
                </div>

                <!-- ==================================================
                     TABLE
                     ================================================== -->

                <QuizAttemptTable
                    v-if="
                        viewMode === 'table'
                    "
                    :attempts="displayedAttempts"
                    :selected-attempts="selectedAttempts"
                    @toggle-select="toggleSelectAttempt"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <!-- ==================================================
                     CARD GRID
                     ================================================== -->

                <QuizAttemptCardGrid
                    v-else
                    :attempts="displayedAttempts"
                    :selected-attempts="selectedAttempts"
                    @toggle-select="toggleSelectAttempt"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <!-- ==================================================
                     BOTTOM PAGINATION
                     ================================================== -->

                <div
                    v-if="attemptsCount"
                    class="flex justify-center
                           items-center
                           flex-col md:flex-row
                           mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAttempts.length"
                        @update:currentPage="
                            currentPage = $event
                        "
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="attempts"
                    />
                </div>
            </div>
        </div>

        <!-- ======================================================
             DELETE MODAL
             ====================================================== -->

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteAttempt"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
