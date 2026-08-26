<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список наборов курсов школы
 *
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolBundle/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolBundle/Sort/SortSelect.vue'
import BundleTable from '@/Components/Admin/School/SchoolBundle/Table/BundleTable.vue'
import BundleCardGrid from '@/Components/Admin/School/SchoolBundle/View/BundleCardGrid.vue'

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

    adminSchoolBundlesProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    bundles: {
        type: [Array, Object],
        default: () => [],
    },

    bundlesCount: {
        type: Number,
        default: 0,
    },

    adminSchoolBundlesPerPage: {
        type: Number,
        default: 6,
    },

    adminSchoolBundlesDefaultSort: {
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

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ==========================================================
 * VIEW MODE
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem('admin_view_mode_bundles') || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_bundles', value)
})

/* ==========================================================
 * DATA SOURCE
 * ========================================================== */

const bundlesList = computed(() => {
    if (Array.isArray(props.bundles)) return props.bundles
    if (Array.isArray(props.bundles?.data)) return props.bundles.data

    return []
})

const localBundles = ref([])

watch(
    bundlesList,
    (newValue) => {
        localBundles.value = JSON.parse(JSON.stringify(newValue || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * ITEMS PER PAGE
 * ========================================================== */

const itemsPerPage = ref(
    props.adminSchoolBundlesPerPage || 6
)

watch(itemsPerPage, (newValue) => {
    router.put(
        route('admin.settings.updateAdminCountSchoolBundles'),
        { value: newValue },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(`Показ ${newValue} элементов на странице.`)
            },

            onError: (errors) => {
                toast.error(
                    errors?.value || 'Ошибка обновления кол-ва элементов.'
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
    || props.adminSchoolBundlesDefaultSort
    || 'idDesc'
)

watch(sortParam, (newValue) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolBundles'),
        { value: newValue },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(
                                new URLSearchParams(window.location.search)
                            ),
                            sort: newValue || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info('Сортировка успешно изменена')
            },

            onError: (errors) => {
                toast.error(
                    errors?.value || 'Ошибка обновления сортировки.'
                )
            },
        }
    )
})

/* ==========================================================
 * SEARCH / PAGINATION
 * ========================================================== */

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/* ==========================================================
 * HELPERS
 * ========================================================== */

const stripHtml = (value = '') => {
    if (value === null || typeof value === 'undefined') return ''

    const html = typeof value === 'string'
        ? value
        : JSON.stringify(value)

    return html
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
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

const normalize = (value) => {
    return stripHtml(value)
        .toString()
        .trim()
        .toLowerCase()
}

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

/* ==========================================================
 * RESOURCE HELPERS
 * ========================================================== */

/**
 * SchoolBundleSharedResource:
 *
 * bundle.translation.*
 */
const getBundleTitle = (bundle) => {
    return bundle?.translation?.title
        || `ID: ${bundle?.id ?? '—'}`
}

const getBundleSubtitle = (bundle) => {
    return bundle?.translation?.subtitle || ''
}

const getBundleShort = (bundle) => {
    return bundle?.translation?.short || ''
}

const getBundleDescription = (bundle) => {
    return bundle?.translation?.description || ''
}

/**
 * slug не переводимый.
 */
const getBundleSlug = (bundle) => {
    return bundle?.slug || ''
}

/**
 * SchoolCourseSharedResource:
 *
 * course.translation.title
 */
const getCourseTitle = (course) => {
    return course?.translation?.title || ''
}

const getCoursesText = (bundle) => {
    const courses = Array.isArray(bundle?.courses)
        ? bundle.courses
        : []

    return courses
        .map(getCourseTitle)
        .filter(Boolean)
        .join(' ')
}

/* ==========================================================
 * FRONTEND SORT
 * ========================================================== */

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortBundles = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') {
        return list.filter(item => !!item.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter(item => !item.activity)
    }

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getBundleTitle(a))
                .localeCompare(
                    normalize(getBundleTitle(b)),
                    props.currentLocale
                )
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getBundleTitle(b))
                .localeCompare(
                    normalize(getBundleTitle(a)),
                    props.currentLocale
                )
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: (a, b) =>
            normalize(getBundleSlug(a))
                .localeCompare(
                    normalize(getBundleSlug(b)),
                    props.currentLocale
                )
            || safeNumber(a?.id) - safeNumber(b?.id),

        slugDesc: (a, b) =>
            normalize(getBundleSlug(b))
                .localeCompare(
                    normalize(getBundleSlug(a)),
                    props.currentLocale
                )
            || safeNumber(b?.id) - safeNumber(a?.id),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        likesAsc: byNumberAsc('likes'),
        likesDesc: byNumberDesc('likes'),

        coursesAsc: byNumberAsc('courses_count'),
        coursesDesc: byNumberDesc('courses_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        pricesAsc: byNumberAsc('prices_count'),
        pricesDesc: byNumberDesc('prices_count'),

        orderItemsAsc: byNumberAsc('order_items_count'),
        orderItemsDesc: byNumberDesc('order_items_count'),

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        dateAsc: byDateAsc('published_at'),
        dateDesc: byDateDesc('published_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/* ==========================================================
 * FRONTEND SEARCH
 * ========================================================== */

/**
 * Должен соответствовать server-side scopeSearch():
 *
 * slug
 * title
 * subtitle
 * short
 * description
 *
 * Дополнительно оставляем Courses,
 * поскольку они уже есть в SharedResource
 * и полезны frontend-поиску.
 */
const filteredBundles = computed(() => {
    let filtered = localBundles.value || []

    const query = normalize(searchQuery.value)

    if (!query) {
        return sortBundles(filtered)
    }

    filtered = filtered.filter((bundle) => {
        const values = [
            bundle.id,

            getBundleSlug(bundle),
            getBundleTitle(bundle),
            getBundleSubtitle(bundle),
            getBundleShort(bundle),
            getBundleDescription(bundle),
            getCoursesText(bundle),

            bundle.views,
            bundle.likes,
            bundle.courses_count,
            bundle.images_count,
            bundle.prices_count,
            bundle.order_items_count,
        ]

        return values.some(value =>
            normalize(value).includes(query)
        )
    })

    return sortBundles(filtered)
})

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const paginatedBundles = computed(() => {
    const per = Number(itemsPerPage.value || 6)
    const start = (currentPage.value - 1) * per

    return filteredBundles.value.slice(
        start,
        start + per
    )
})

const displayedBundles = computed(() => {
    return props.useServerProcessing
        ? bundlesList.value
        : paginatedBundles.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * DELETE
 * ========================================================== */

const showConfirmDeleteModal = ref(false)
const bundleToDelete = ref(null)

const confirmDelete = (bundle) => {
    bundleToDelete.value = bundle
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    bundleToDelete.value = null
}

const deleteBundle = () => {
    if (!bundleToDelete.value?.id) return

    const idToDelete = bundleToDelete.value.id
    const titleToDelete = getBundleTitle(bundleToDelete.value)

    router.delete(
        route('admin.schoolBundles.destroy', {
            schoolBundle: idToDelete,
        }),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Набор курсов "${titleToDelete}" удалён.`
                )
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]

                const errorMessage =
                    errors?.general
                    || errors?.[firstKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${errorMessage} (Набор: ${titleToDelete})`
                )
            },

            onFinish: closeModal,
        }
    )
}

/* ==========================================================
 * LOCAL PATCH
 * ========================================================== */

const patchBundle = (bundleId, payload) => {
    const index = localBundles.value.findIndex(
        bundle => bundle.id === bundleId
    )

    if (index === -1) return

    localBundles.value[index] = {
        ...localBundles.value[index],
        ...payload,
    }
}

/* ==========================================================
 * SELECTION
 * ========================================================== */

const selectedBundles = ref([])

const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedBundles.value.map(bundle => bundle.id)

    if (checked) {
        selectedBundles.value = [
            ...new Set([
                ...selectedBundles.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedBundles.value = selectedBundles.value.filter(
        id => !ids.includes(id)
    )
}

const toggleSelectBundle = (id) => {
    const index = selectedBundles.value.indexOf(id)

    if (index > -1) {
        selectedBundles.value.splice(index, 1)
        return
    }

    selectedBundles.value.push(id)
}

/* ==========================================================
 * SORT ORDER
 * ========================================================== */

const handleSortOrderUpdate = (orderedIds) => {
    const pageOffset = props.useServerProcessing
        ? 0
        : (currentPage.value - 1) * Number(itemsPerPage.value || 6)

    const items = orderedIds.map((id, index) => ({
        id,
        sort: pageOffset + index + 1,
    }))

    if (!items.length) return

    router.put(
        route('admin.actions.schoolBundles.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Порядок наборов курсов успешно обновлён.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления сортировки наборов курсов:',
                    errors
                )

                toast.error(
                    errors?.message
                    || errors?.general
                    || 'Не удалось обновить порядок наборов курсов.'
                )

                router.reload({
                    only: ['bundles'],
                    preserveScroll: true,
                })
            },
        }
    )
}

/* ==========================================================
 * BULK ACTIVITY
 * ========================================================== */

const bulkToggleActivity = (newActivity) => {
    if (!selectedBundles.value.length) {
        toast.warning(
            'Выберите наборы курсов для активации/деактивации.'
        )

        return
    }

    const idsToUpdate = [
        ...selectedBundles.value,
    ]

    router.put(
        route('admin.actions.schoolBundles.bulkUpdateActivity'),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                idsToUpdate.forEach(id => {
                    patchBundle(id, {
                        activity: newActivity,
                    })
                })

                selectedBundles.value = []

                toast.success(
                    'Активность выбранных наборов курсов обновлена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового обновления активности.'
                )
            },
        }
    )
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ checked: true })
    } else if (action === 'deselectAll') {
        toggleAll({ checked: false })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/* ==========================================================
 * SINGLE ACTIVITY
 * ========================================================== */

const toggleActivity = (bundle) => {
    const newActivity = !bundle.activity
    const bundleTitle = getBundleTitle(bundle)

    const actionText = newActivity
        ? t('activated')
        : t('deactivated')

    router.put(
        route('admin.actions.schoolBundles.updateActivity', {
            schoolBundle: bundle.id,
        }),
        {
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchBundle(bundle.id, {
                    activity: newActivity,
                })

                toast.success(
                    `Набор курсов "${bundleTitle}" ${actionText}.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности для набора "${bundleTitle}".`
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('bundles')">
        <template #header>
            <TitlePage>
                {{ t('bundles') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Header -->
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolBundles.create')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current opacity-50 shrink-0"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addBundle') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolBundlesProcessingMode"
                        :mode="adminSchoolBundlesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="bundlesCount"
                    />
                </div>

                <!-- Search -->
                <SearchInput
                    v-if="bundlesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="bundlesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Per page / Sort -->
                <div
                    v-if="bundlesCount"
                    class="flex justify-between items-center
                           flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSchoolBundles"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="value => sortParam = value"
                    />
                </div>

                <!-- Count / Bulk / View -->
                <div
                    v-if="bundlesCount"
                    class="flex flex-col lg:flex-row
                           items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ bundlesCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- Top Pagination -->
                <div
                    v-if="bundlesCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="bundles"
                    />
                </div>

                <!-- Table -->
                <BundleTable
                    v-if="viewMode === 'table'"
                    :bundles="displayedBundles"
                    :selected-bundles="selectedBundles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                />

                <!-- Cards -->
                <BundleCardGrid
                    v-else
                    :bundles="displayedBundles"
                    :selected-bundles="selectedBundles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                />

                <!-- Bottom Pagination -->
                <div
                    v-if="bundlesCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="bundles"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteBundle"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
