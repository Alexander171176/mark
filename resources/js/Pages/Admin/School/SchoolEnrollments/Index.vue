<script setup>
/**
 * Список зачислений студентов.
 *
 * Новый Admin Index-контракт:
 *
 * - SchoolEnrollmentSharedResource;
 * - server / frontend / auto;
 * - server search / sort / pagination;
 * - frontend search / sort / pagination;
 *
 * Связанные сущности:
 *
 * enrollment.user
 * enrollment.course.translation
 * enrollment.schedule.translation
 * enrollment.schedule.course.translation
 * enrollment.order
 * enrollment.certificate
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

import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'

import EnrollmentTable
    from '@/Components/Admin/School/SchoolEnrollment/Table/EnrollmentTable.vue'

import EnrollmentCardGrid
    from '@/Components/Admin/School/SchoolEnrollment/View/EnrollmentCardGrid.vue'

import SortSelect
    from '@/Components/Admin/School/SchoolEnrollment/Sort/SortSelect.vue'

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
     * Locale.
     */
    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    /**
     * Processing.
     */
    adminSchoolEnrollmentsProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    /**
     * FRONTEND:
     * Array / ResourceCollection.
     *
     * SERVER:
     * paginator object.
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
    adminSchoolEnrollmentsPerPage: {
        type: Number,
        default: 10,
    },

    adminSchoolEnrollmentsDefaultSort: {
        type: String,
        default: 'idDesc',
    },

    /**
     * Query state.
     */
    sortParam: {
        type: String,
        default: '',
    },

    search: {
        type: String,
        default: '',
    },

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
        'admin_view_mode_enrollments'
    ) || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_enrollments',
        value
    )
})

/* ==========================================================
 * DATA SOURCE
 * ========================================================== */

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
 * После Inertia navigation данные
 * автоматически синхронизируются.
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
    props.adminSchoolEnrollmentsPerPage
    || 10
)

watch(
    () => props.adminSchoolEnrollmentsPerPage,
    (value) => {
        if (
            value
            && Number(value) !== Number(itemsPerPage.value)
        ) {
            itemsPerPage.value =
                Number(value)
        }
    }
)

/**
 * В frontend-режиме сохраняем
 * настройку сами.
 *
 * В server-режиме этим занимается
 * ServerItemsPerPageSelect.
 */
watch(itemsPerPage, (value, oldValue) => {
    if (
        value === oldValue
        || props.useServerProcessing
    ) {
        return
    }

    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminCountSchoolEnrollments'
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
    || props.adminSchoolEnrollmentsDefaultSort
    || 'idDesc'
)

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

watch(sortParam, (value, oldValue) => {
    if (value === oldValue) {
        return
    }

    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminSortSchoolEnrollments'
        ),
        {
            value,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                /**
                 * Frontend пересортируется
                 * реактивно без нового GET.
                 *
                 * Server должен получить
                 * новую страницу с backend.
                 */
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
 * NEW SCHOOL ENROLLMENT SHARED CONTRACT
 * ========================================================== */

/**
 * User.
 */
const getUserName = (enrollment) => {
    return enrollment?.user?.name || ''
}

const getUserEmail = (enrollment) => {
    return enrollment?.user?.email || ''
}

/**
 * Course.
 */
const getCourseTitle = (enrollment) => {
    return enrollment
            ?.course
            ?.translation
            ?.title
        || ''
}

const getCourseSlug = (enrollment) => {
    return enrollment
            ?.course
            ?.slug
        || ''
}

const getCourseShort = (enrollment) => {
    return enrollment
            ?.course
            ?.translation
            ?.short
        || ''
}

const getCourseDescription = (enrollment) => {
    return enrollment
            ?.course
            ?.translation
            ?.description
        || ''
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

const getScheduleSlug = (enrollment) => {
    return enrollment
            ?.schedule
            ?.slug
        || ''
}

const getScheduleSubtitle = (enrollment) => {
    return enrollment
            ?.schedule
            ?.translation
            ?.subtitle
        || ''
}

const getScheduleShort = (enrollment) => {
    return enrollment
            ?.schedule
            ?.translation
            ?.short
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
 *
 * Это отдельный полезный источник
 * для frontend-поиска.
 */
const getScheduleCourseTitle = (enrollment) => {
    return enrollment
            ?.schedule
            ?.course
            ?.translation
            ?.title
        || ''
}

const getScheduleCourseShort = (enrollment) => {
    return enrollment
            ?.schedule
            ?.course
            ?.translation
            ?.short
        || ''
}

const getOrderNumber = (enrollment) => {
    return enrollment?.order?.number
        || enrollment?.order?.id
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

/**
 * Набор должен совпадать
 * с SchoolEnrollmentController::applySort().
 */
const sortEnrollments = (items) => {
    const list =
        (items || []).slice()

    const sortMap = {
        idAsc:
            byNumberAsc('id'),

        idDesc:
            byNumberDesc('id'),

        startedAtAsc:
            byDateAsc(
                'started_at'
            ),

        startedAtDesc:
            byDateDesc(
                'started_at'
            ),

        expiresAtAsc:
            byDateAsc(
                'expires_at'
            ),

        expiresAtDesc:
            byDateDesc(
                'expires_at'
            ),

        completedAtAsc:
            byDateAsc(
                'completed_at'
            ),

        completedAtDesc:
            byDateDesc(
                'completed_at'
            ),

        progressAsc:
            byNumberAsc(
                'progress_percent'
            ),

        progressDesc:
            byNumberDesc(
                'progress_percent'
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

        courseTitleAsc:
            byStringGetterAsc(
                getCourseTitle
            ),

        courseTitleDesc:
            byStringGetterDesc(
                getCourseTitle
            ),

        scheduleTitleAsc:
            byStringGetterAsc(
                getScheduleTitle
            ),

        scheduleTitleDesc:
            byStringGetterDesc(
                getScheduleTitle
            ),

        progressRecordsAsc:
            byNumberAsc(
                'progress_records_count'
            ),

        progressRecordsDesc:
            byNumberDesc(
                'progress_records_count'
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
                const values = [
                    /**
                     * Enrollment.
                     */
                    enrollment?.id,
                    enrollment?.status,
                    enrollment?.notes,
                    enrollment?.progress_percent,

                    /**
                     * User.
                     */
                    getUserName(enrollment),
                    getUserEmail(enrollment),

                    /**
                     * Course.
                     */
                    getCourseTitle(enrollment),
                    getCourseSlug(enrollment),
                    getCourseShort(enrollment),
                    getCourseDescription(enrollment),

                    /**
                     * Schedule.
                     */
                    getScheduleTitle(enrollment),
                    getScheduleSlug(enrollment),
                    getScheduleSubtitle(enrollment),
                    getScheduleShort(enrollment),
                    getScheduleDescription(enrollment),

                    /**
                     * Course самого Schedule.
                     */
                    getScheduleCourseTitle(enrollment),
                    getScheduleCourseShort(enrollment),

                    /**
                     * Order.
                     */
                    getOrderNumber(enrollment),
                ]

                return values.some(
                    (value) =>
                        normalize(value)
                            .includes(query)
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

const filteredCount = computed(() => {
    return props.useServerProcessing
        ? Number(
            props.enrollmentsCount || 0
        )
        : filteredEnrollments.value.length
})

const paginatedEnrollments = computed(() => {
    const perPage =
        Math.max(
            1,
            Number(
                itemsPerPage.value || 10
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
     * После удаления или поиска
     * текущая страница может исчезнуть.
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
 * DISPLAY DATA
 * ========================================================== */

const displayedEnrollments = computed(() => {
    return props.useServerProcessing
        ? enrollmentsList.value
        : paginatedEnrollments.value
})

const totalCount = computed(() => {
    return Number(
        props.enrollmentsCount || 0
    )
})

/* ==========================================================
 * DELETE
 * ========================================================== */

const showConfirmDeleteModal =
    ref(false)

const enrollmentToDelete =
    ref(null)

const confirmDelete = (enrollment) => {
    enrollmentToDelete.value =
        enrollment

    showConfirmDeleteModal.value =
        true
}

const closeModal = () => {
    showConfirmDeleteModal.value =
        false

    enrollmentToDelete.value =
        null
}

const deleteEnrollment = () => {
    if (
        !enrollmentToDelete.value?.id
    ) {
        return
    }

    const id =
        enrollmentToDelete.value.id

    router.delete(
        route(
            'admin.schoolEnrollments.destroy',
            {
                schoolEnrollment:
                id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Зачисление ID: ${id} удалено.`
                )
            },

            onError: (errors) => {
                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                const message =
                    errors?.general
                    || errors?.[firstKey]
                    || 'Ошибка при удалении зачисления.'

                toast.error(
                    `${message} ID: ${id}`
                )
            },

            onFinish: () => {
                closeModal()
            },
        }
    )
}
</script>

<template>
    <AdminLayout
        :title="t('enrollments')"
    >
        <template #header>
            <TitlePage>
                {{ t('enrollments') }}
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
                <!-- ==================================================
                     TOP
                     ================================================== -->

                <div
                    class="sm:flex
                           sm:justify-between
                           sm:items-center
                           mb-3 gap-3"
                >
                    <DefaultButton
                        :href="
                            route(
                                'admin.schoolEnrollments.create'
                            )
                        "
                    >
                        {{ t('addEnrollment') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolEnrollmentsProcessingMode"
                        :mode="adminSchoolEnrollmentsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="totalCount"
                    />
                </div>

                <!-- ==================================================
                     SEARCH
                     ================================================== -->

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

                <!-- ==================================================
                     PER PAGE / SORT
                     ================================================== -->

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
                        update-route="admin.settings.updateAdminCountSchoolEnrollments"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="
                            sortParam = $event
                        "
                    />
                </div>

                <!-- ==================================================
                     COUNT / VIEW
                     ================================================== -->

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

                <!-- ==================================================
                     TOP PAGINATION
                     ================================================== -->

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

                <!-- ==================================================
                     CONTENT
                     ================================================== -->

                <EnrollmentTable
                    v-if="
                        viewMode === 'table'
                    "
                    :enrollments="
                        displayedEnrollments
                    "
                    @delete="
                        confirmDelete
                    "
                />

                <EnrollmentCardGrid
                    v-else
                    :enrollments="
                        displayedEnrollments
                    "
                    @delete="
                        confirmDelete
                    "
                />

                <!-- ==================================================
                     BOTTOM PAGINATION
                     ================================================== -->

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

                <!-- ==================================================
                     EMPTY
                     ================================================== -->

                <div
                    v-if="!totalCount"
                    class="py-8 text-center
                           text-sm font-semibold
                           text-slate-500
                           dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>

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

        <!-- ==================================================
             DELETE MODAL
             ================================================== -->

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteEnrollment"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
