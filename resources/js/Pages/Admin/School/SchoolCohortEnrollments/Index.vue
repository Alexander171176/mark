<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Записи пользователей на потоки.
 *
 * Новый Admin Index-контракт:
 *
 * - SchoolCohortEnrollmentSharedResource;
 * - server / frontend / auto;
 * - frontend search / sort / pagination;
 * - server search / sort / pagination;
 * - schedule.translation;
 * - schedule.course.translation;
 * - user;
 *
 * Никаких:
 *
 * - schedule.title;
 * - schedule.translations[0];
 * - course.title;
 * - course.translations[0];
 * - schedules/users selector props на Index.
 */

import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'

import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'

import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'

import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'

import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'

import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import CohortEnrollmentTable
    from '@/Components/Admin/School/SchoolCohortEnrollment/Table/CohortEnrollmentTable.vue'

import CohortEnrollmentCardGrid
    from '@/Components/Admin/School/SchoolCohortEnrollment/View/CohortEnrollmentCardGrid.vue'

import SortSelect
    from '@/Components/Admin/School/SchoolCohortEnrollment/Sort/SortSelect.vue'

/* ==========================================================
 * BASE
 * ========================================================== */

const { t } = useI18n()
const toast = useToast()

/* ==========================================================
 * PROPS
 * ========================================================== */

const props = defineProps({
    /**
     * Текущая locale.
     */
    currentLocale: {
        type: String,
        default: '',
    },

    /**
     * Processing mode.
     */
    adminSchoolCohortEnrollmentsProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    /**
     * Может быть:
     *
     * frontend:
     *   Array / ResourceCollection;
     *
     * server:
     *   paginator object.
     */
    enrollments: {
        type: [Array, Object],
        default: () => [],
    },

    enrollmentsCount: {
        type: Number,
        default: 0,
    },

    /**
     * Admin settings.
     */
    adminSchoolCohortEnrollmentsPerPage: {
        type: Number,
        default: 10,
    },

    adminSchoolCohortEnrollmentsDefaultSort: {
        type: String,
        default: 'idDesc',
    },

    /**
     * Текущие query-параметры.
     */
    sortParam: {
        type: String,
        default: '',
    },

    search: {
        type: String,
        default: '',
    },

    /**
     * Реальные серверные фильтры.
     */
    filters: {
        type: Object,
        default: () => ({}),
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ==========================================================
 * VIEW MODE
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem(
        'admin_view_mode_cohort_enrollments'
    ) || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_cohort_enrollments',
        value
    )
})

/* ==========================================================
 * DATA SOURCE
 * ========================================================== */

/**
 * Унифицированный список.
 *
 * frontend:
 * props.enrollments может быть массивом.
 *
 * server:
 * props.enrollments.data.
 */
const enrollmentsList = computed(() => {
    if (Array.isArray(props.enrollments)) {
        return props.enrollments
    }

    if (
        props.enrollments
        && Array.isArray(
            props.enrollments.data
        )
    ) {
        return props.enrollments.data
    }

    return []
})

/**
 * Локальная копия нужна frontend-режиму.
 *
 * Также дочерние компоненты могут локально
 * изменять status/notes после действий.
 */
const localEnrollments = ref([])

watch(
    enrollmentsList,
    (items) => {
        localEnrollments.value =
            JSON.parse(
                JSON.stringify(
                    items || []
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
    props.adminSchoolCohortEnrollmentsPerPage
    || 10
)

/**
 * В frontend режиме настройка сохраняется
 * здесь.
 *
 * В server режиме этим занимается
 * ServerItemsPerPageSelect.
 */
watch(itemsPerPage, (value) => {
    if (props.useServerProcessing) {
        return
    }

    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminCountSchoolCohortEnrollments'
        ),
        {
            value,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(
                    `Показ ${value} элементов на странице.`
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
})

/* ==========================================================
 * SORT
 * ========================================================== */

const sortParam = ref(
    props.sortParam
    || props.adminSchoolCohortEnrollmentsDefaultSort
    || 'idDesc'
)

/**
 * При изменении props после Inertia navigation
 * синхронизируем локальный sort.
 */
watch(
    () => props.sortParam,
    (value) => {
        if (
            value
            && value !== sortParam.value
        ) {
            sortParam.value = value
        }
    }
)

/**
 * Сохраняем сортировку.
 *
 * FRONTEND:
 * после сохранения Vue просто пересортирует
 * текущую коллекцию.
 *
 * SERVER:
 * после сохранения выполняем GET Index
 * с новым sort.
 */
watch(sortParam, (value, oldValue) => {
    if (value === oldValue) {
        return
    }

    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminSortSchoolCohortEnrollments'
        ),
        {
            value,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(
                                new URLSearchParams(
                                    window.location.search
                                )
                            ),

                            sort:
                                value || undefined,

                            page:
                            undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
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
})

/* ==========================================================
 * SEARCH
 * ========================================================== */

const searchQuery = ref(
    props.search || ''
)

/**
 * При server navigation синхронизируем
 * значение поля поиска с query.
 */
watch(
    () => props.search,
    (value) => {
        const normalized =
            value || ''

        if (
            normalized
            !== searchQuery.value
        ) {
            searchQuery.value =
                normalized
        }
    }
)

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const currentPage = ref(1)

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
 * HELPERS
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
    if (!value) {
        return 0
    }

    const time =
        new Date(value)
            .getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

/* ==========================================================
 * NEW SHARED RESOURCE CONTRACT
 * ========================================================== */

const getUserName = (enrollment) => {
    return enrollment?.user?.name || ''
}

const getUserEmail = (enrollment) => {
    return enrollment?.user?.email || ''
}

/**
 * Schedule.
 */
const getScheduleTitle = (enrollment) => {
    return enrollment
            ?.schedule
            ?.translation
            ?.title
        || ''
}

const getScheduleDescription = (enrollment) => {
    return enrollment
            ?.schedule
            ?.translation
            ?.description
        || ''
}

/**
 * Course связанного Schedule.
 */
const getCourseTitle = (enrollment) => {
    return enrollment
            ?.schedule
            ?.course
            ?.translation
            ?.title
        || ''
}

const getCourseShort = (enrollment) => {
    return enrollment
            ?.schedule
            ?.course
            ?.translation
            ?.short
        || ''
}

const getCourseDescription = (enrollment) => {
    return enrollment
            ?.schedule
            ?.course
            ?.translation
            ?.description
        || ''
}

/* ==========================================================
 * FRONTEND SORT
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

const byStringGetterAsc = (getter) =>
    (a, b) =>
        normalize(
            getter(a)
        ).localeCompare(
            normalize(
                getter(b)
            )
        )
        || safeNumber(a?.id)
        - safeNumber(b?.id)

const byStringGetterDesc = (getter) =>
    (a, b) =>
        normalize(
            getter(b)
        ).localeCompare(
            normalize(
                getter(a)
            )
        )
        || safeNumber(b?.id)
        - safeNumber(a?.id)

const byDateAsc = (field) =>
    (a, b) =>
        safeDate(a?.[field])
        - safeDate(b?.[field])
        || safeNumber(a?.id)
        - safeNumber(b?.id)

const byDateDesc = (field) =>
    (a, b) =>
        safeDate(b?.[field])
        - safeDate(a?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)

/**
 * Frontend-сортировка.
 *
 * Набор параметров должен совпадать
 * с applySort() Controller.
 */
const sortEnrollments = (items) => {
    const list =
        (items || []).slice()

    const sortMap = {
        idAsc:
            byNumberAsc('id'),

        idDesc:
            byNumberDesc('id'),

        enrolledAtAsc:
            byDateAsc(
                'enrolled_at'
            ),

        enrolledAtDesc:
            byDateDesc(
                'enrolled_at'
            ),

        statusAsc:
            (a, b) =>
                normalize(
                    a?.status
                ).localeCompare(
                    normalize(
                        b?.status
                    )
                )
                || safeNumber(a?.id)
                - safeNumber(b?.id),

        statusDesc:
            (a, b) =>
                normalize(
                    b?.status
                ).localeCompare(
                    normalize(
                        a?.status
                    )
                )
                || safeNumber(b?.id)
                - safeNumber(a?.id),

        userNameAsc:
            byStringGetterAsc(
                getUserName
            ),

        userNameDesc:
            byStringGetterDesc(
                getUserName
            ),

        userEmailAsc:
            byStringGetterAsc(
                getUserEmail
            ),

        userEmailDesc:
            byStringGetterDesc(
                getUserEmail
            ),

        scheduleTitleAsc:
            byStringGetterAsc(
                getScheduleTitle
            ),

        scheduleTitleDesc:
            byStringGetterDesc(
                getScheduleTitle
            ),

        createdAtAsc:
            byDateAsc(
                'created_at'
            ),

        createdAtDesc:
            byDateDesc(
                'created_at'
            ),

        updatedAtAsc:
            byDateAsc(
                'updated_at'
            ),

        updatedAtDesc:
            byDateDesc(
                'updated_at'
            ),
    }

    const sorter =
        sortMap[
            sortParam.value
            ]

    return sorter
        ? list.sort(sorter)
        : list
}

/* ==========================================================
 * FRONTEND SEARCH
 * ========================================================== */

const filteredEnrollments = computed(() => {
    const query =
        normalize(
            searchQuery.value
        )

    let items =
        localEnrollments.value || []

    if (query) {
        items = items.filter(
            (enrollment) => {
                const status =
                    normalize(
                        enrollment?.status
                    )

                const notes =
                    normalize(
                        enrollment?.notes
                    )

                const userName =
                    normalize(
                        getUserName(
                            enrollment
                        )
                    )

                const userEmail =
                    normalize(
                        getUserEmail(
                            enrollment
                        )
                    )

                const scheduleTitle =
                    normalize(
                        getScheduleTitle(
                            enrollment
                        )
                    )

                const scheduleDescription =
                    normalize(
                        getScheduleDescription(
                            enrollment
                        )
                    )

                const courseTitle =
                    normalize(
                        getCourseTitle(
                            enrollment
                        )
                    )

                const courseShort =
                    normalize(
                        getCourseShort(
                            enrollment
                        )
                    )

                const courseDescription =
                    normalize(
                        getCourseDescription(
                            enrollment
                        )
                    )

                return (
                    status.includes(query)
                    || notes.includes(query)
                    || userName.includes(query)
                    || userEmail.includes(query)
                    || scheduleTitle.includes(query)
                    || scheduleDescription.includes(query)
                    || courseTitle.includes(query)
                    || courseShort.includes(query)
                    || courseDescription.includes(query)
                )
            }
        )
    }

    return sortEnrollments(
        items
    )
})

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const paginatedEnrollments = computed(() => {
    const perPage =
        Math.max(
            1,
            Number(
                itemsPerPage.value
                || 10
            )
        )

    const totalPages =
        Math.max(
            1,
            Math.ceil(
                filteredEnrollments.value.length
                / perPage
            )
        )

    /**
     * Если после поиска/смены perPage
     * текущая страница стала недоступна,
     * возвращаемся на последнюю существующую.
     */
    if (
        currentPage.value
        > totalPages
    ) {
        currentPage.value =
            totalPages
    }

    const start =
        (
            currentPage.value - 1
        ) * perPage

    return filteredEnrollments.value.slice(
        start,
        start + perPage
    )
})

/* ==========================================================
 * DISPLAYED DATA
 * ========================================================== */

const displayedEnrollments = computed(() => {
    return props.useServerProcessing
        ? enrollmentsList.value
        : paginatedEnrollments.value
})

/* ==========================================================
 * COUNTS
 * ========================================================== */

/**
 * Общее количество сущностей,
 * пришедшее от backend.
 */
const totalCount = computed(() => {
    return Number(
        props.enrollmentsCount
        ?? 0
    )
})

/**
 * Для frontend-поиска полезно отдельно
 * знать количество найденных записей.
 */
const filteredCount = computed(() => {
    return props.useServerProcessing
        ? totalCount.value
        : filteredEnrollments.value.length
})
</script>

<template>
    <AdminLayout
        :title="t('cohortEnrollments')"
    >
        <template #header>
            <TitlePage>
                {{ t('cohortEnrollments') }}
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
                       shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Processing mode -->
                <div
                    class="sm:flex
                           sm:justify-end
                           sm:items-center
                           mb-3 gap-3"
                >
                    <ProcessingModeSwitcher
                        setting-key="adminSchoolCohortEnrollmentsProcessingMode"
                        :mode="adminSchoolCohortEnrollmentsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="totalCount"
                    />
                </div>

                <!-- Search -->
                <SearchInput
                    v-if="
                        totalCount
                        && !useServerProcessing
                    "
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <ServerSearchInput
                    v-if="
                        totalCount
                        && useServerProcessing
                    "
                    v-model="searchQuery"
                />

                <!-- Per page / sort -->
                <div
                    v-if="totalCount"
                    class="flex
                           justify-between
                           items-center
                           flex-col md:flex-row
                           my-3 gap-3"
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
                        update-route="admin.settings.updateAdminCountSchoolCohortEnrollments"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="
                            sortParam = $event
                        "
                    />
                </div>

                <!-- Count / view -->
                <div
                    v-if="totalCount"
                    class="flex
                           justify-between
                           items-center
                           flex-col md:flex-row
                           my-3 gap-3"
                >
                    <CountTable>
                        <template
                            v-if="
                                !useServerProcessing
                                && searchQuery
                            "
                        >
                            {{ filteredCount }}
                            /
                            {{ totalCount }}
                        </template>

                        <template v-else>
                            {{ totalCount }}
                        </template>
                    </CountTable>

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- Top pagination -->
                <div
                    v-if="totalCount"
                    class="flex
                           justify-center
                           items-center
                           flex-col md:flex-row
                           mb-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCount"
                        @update:currentPage="
                            currentPage = $event
                        "
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="enrollments"
                    />
                </div>

                <!-- Table -->
                <CohortEnrollmentTable
                    v-if="
                        viewMode === 'table'
                    "
                    :enrollments="
                        displayedEnrollments
                    "
                />

                <!-- Cards -->
                <CohortEnrollmentCardGrid
                    v-else
                    :enrollments="
                        displayedEnrollments
                    "
                />

                <!-- Bottom pagination -->
                <div
                    v-if="totalCount"
                    class="flex
                           justify-center
                           items-center
                           flex-col md:flex-row
                           mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCount"
                        @update:currentPage="
                            currentPage = $event
                        "
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="enrollments"
                    />
                </div>

                <!-- Empty database -->
                <div
                    v-if="!totalCount"
                    class="py-8 text-center
                           text-sm font-semibold
                           text-slate-500
                           dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>

                <!-- Frontend search empty -->
                <div
                    v-else-if="
                        !useServerProcessing
                        && searchQuery
                        && filteredCount === 0
                    "
                    class="py-4 text-center
                           text-sm font-semibold
                           text-slate-500
                           dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
