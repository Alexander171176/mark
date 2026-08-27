<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Список цен наборов курсов школы.
 *
 * - processing modes: frontend | server | auto
 * - locale-aware SchoolBundle
 * - frontend/server search
 * - frontend/server sorting
 * - frontend/server pagination
 * - bulk actions
 * - drag & drop sort
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
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolBundlePrice/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolBundlePrice/Sort/SortSelect.vue'
import BundlePriceTable from '@/Components/Admin/School/SchoolBundlePrice/Table/BundlePriceTable.vue'
import BundlePriceCardGrid from '@/Components/Admin/School/SchoolBundlePrice/View/BundlePriceCardGrid.vue'

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

    adminSchoolBundlePricesProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    prices: {
        type: [Array, Object],
        default: () => [],
    },

    pricesCount: {
        type: Number,
        default: 0,
    },

    filters: {
        type: Object,
        default: () => ({}),
    },

    adminSchoolBundlePricesPerPage: {
        type: Number,
        default: 10,
    },

    adminSchoolBundlePricesDefaultSort: {
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

    bundles: {
        type: Array,
        default: () => [],
    },

    currencies: {
        type: Array,
        default: () => [],
    },
})

/* ==========================================================
 * VIEW MODE
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem('admin_view_mode_bundle_prices')
    || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_bundle_prices',
        value
    )
})

/* ==========================================================
 * DATA SOURCE
 * ========================================================== */

const pricesList = computed(() => {
    if (Array.isArray(props.prices)) {
        return props.prices
    }

    if (Array.isArray(props.prices?.data)) {
        return props.prices.data
    }

    return []
})

const localPrices = ref([])

watch(
    pricesList,
    (newValue) => {
        localPrices.value = JSON.parse(
            JSON.stringify(newValue || [])
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
    props.adminSchoolBundlePricesPerPage
    || 10
)

watch(itemsPerPage, (newValue) => {
    router.put(
        route(
            'admin.settings.updateAdminCountSchoolBundlePrices'
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
})

/* ==========================================================
 * SORT
 * ========================================================== */

const sortParam = ref(
    props.sortParam
    || props.adminSchoolBundlePricesDefaultSort
    || 'idDesc'
)

const currentPage = ref(1)

watch(sortParam, (newValue) => {
    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminSortSchoolBundlePrices'
        ),
        {
            value: newValue,
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
                                newValue
                                || undefined,

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
    props.search
    || props.filters?.search
    || ''
)

/* ==========================================================
 * NORMALIZATION
 * ========================================================== */

const stripHtml = (html = '') => {
    return String(html || '')
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

const normalize = (value) => {
    return stripHtml(
        value ?? ''
    )
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
    if (!value) {
        return 0
    }

    const time =
        new Date(value).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

/* ==========================================================
 * RESOURCE HELPERS
 *
 * SchoolBundlePriceSharedResource
 * +
 * SchoolBundleSharedResource
 * ========================================================== */

const getBundleTitle = (price) => {
    return price?.bundle?.translation?.title
        || `ID: ${price?.school_bundle_id || ''}`
}

const getBundleSubtitle = (price) => {
    return price?.bundle?.translation?.subtitle
        || ''
}

const getBundleShort = (price) => {
    return price?.bundle?.translation?.short
        || ''
}

const getBundleDescription = (price) => {
    return price?.bundle?.translation?.description
        || ''
}

const getCurrencyCode = (price) => {
    return price?.currency?.code
        || ''
}

const getCurrencyName = (price) => {
    return price?.currency?.name
        || ''
}

const getCurrencySymbol = (price) => {
    return price?.currency?.symbol
        || ''
}

const getDeleteTitle = (price) => {
    const bundle =
        getBundleTitle(price)

    const currency =
        getCurrencyCode(price)

    const effectivePrice =
        price?.effective_price !== null
        && price?.effective_price !== undefined
            ? String(
                price.effective_price
            )
            : ''

    return [
        bundle,
        currency,
        effectivePrice,
    ]
        .filter(Boolean)
        .join(' • ')
}

/* ==========================================================
 * FRONTEND SORT HELPERS
 * ========================================================== */

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field])
    - safeNumber(b?.[field])
    || safeNumber(a?.id)
    - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field])
    - safeNumber(a?.[field])
    || safeNumber(b?.id)
    - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field])
    - safeDate(b?.[field])
    || safeNumber(a?.id)
    - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field])
    - safeDate(a?.[field])
    || safeNumber(b?.id)
    - safeNumber(a?.id)

/* ==========================================================
 * FRONTEND SORT
 *
 * Соответствует:
 * SchoolBundlePrice::scopeSortByParam()
 * ========================================================== */

const sortPrices = (items) => {
    const list =
        (items || []).slice()

    if (
        sortParam.value === 'activity'
    ) {
        return list.filter(
            price =>
                Boolean(
                    price.activity
                )
        )
    }

    if (
        sortParam.value === 'inactive'
    ) {
        return list.filter(
            price =>
                !price.activity
        )
    }

    const sortMap = {
        idAsc:
            byNumberAsc('id'),

        idDesc:
            byNumberDesc('id'),

        sortAsc:
            byNumberAsc('sort'),

        sortDesc:
            byNumberDesc('sort'),

        activityAsc:
            byNumberAsc(
                'activity'
            ),

        activityDesc:
            byNumberDesc(
                'activity'
            ),

        bundleTitleAsc: (a, b) =>
            normalize(
                getBundleTitle(a)
            ).localeCompare(
                normalize(
                    getBundleTitle(b)
                ),
                props.currentLocale
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        bundleTitleDesc: (a, b) =>
            normalize(
                getBundleTitle(b)
            ).localeCompare(
                normalize(
                    getBundleTitle(a)
                ),
                props.currentLocale
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        currencyCodeAsc: (a, b) =>
            normalize(
                getCurrencyCode(a)
            ).localeCompare(
                normalize(
                    getCurrencyCode(b)
                ),
                props.currentLocale
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        currencyCodeDesc: (a, b) =>
            normalize(
                getCurrencyCode(b)
            ).localeCompare(
                normalize(
                    getCurrencyCode(a)
                ),
                props.currentLocale
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        effectivePriceAsc:
            byNumberAsc(
                'effective_price'
            ),

        effectivePriceDesc:
            byNumberDesc(
                'effective_price'
            ),

        priceAsc:
            byNumberAsc(
                'price'
            ),

        priceDesc:
            byNumberDesc(
                'price'
            ),

        salePriceAsc:
            byNumberAsc(
                'sale_price'
            ),

        salePriceDesc:
            byNumberDesc(
                'sale_price'
            ),

        compareAtPriceAsc:
            byNumberAsc(
                'compare_at_price'
            ),

        compareAtPriceDesc:
            byNumberDesc(
                'compare_at_price'
            ),

        discountPercentAsc:
            byNumberAsc(
                'discount_percent'
            ),

        discountPercentDesc:
            byNumberDesc(
                'discount_percent'
            ),

        startsAtAsc:
            byDateAsc(
                'starts_at'
            ),

        startsAtDesc:
            byDateDesc(
                'starts_at'
            ),

        endsAtAsc:
            byDateAsc(
                'ends_at'
            ),

        endsAtDesc:
            byDateDesc(
                'ends_at'
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
 *
 * Соответствует Model::scopeSearch():
 *
 * Price:
 * - price
 * - sale_price
 * - compare_at_price
 *
 * Currency:
 * - code
 * - name
 * - symbol
 *
 * Bundle translation:
 * - title
 * - subtitle
 * - short
 * - description
 * ========================================================== */

const filteredPrices = computed(() => {
    let filtered =
        localPrices.value || []

    const query =
        normalize(
            searchQuery.value
        )

    if (!query) {
        return sortPrices(
            filtered
        )
    }

    filtered = filtered.filter(
        (price) => {
            const values = [
                /**
                 * Price.
                 */
                price?.price,
                price?.sale_price,
                price?.compare_at_price,

                /**
                 * Bundle current locale.
                 */
                getBundleTitle(price),
                getBundleSubtitle(price),
                getBundleShort(price),
                getBundleDescription(price),

                /**
                 * Currency.
                 */
                getCurrencyCode(price),
                getCurrencyName(price),
                getCurrencySymbol(price),
            ]

            return values.some(
                value =>
                    normalize(
                        value
                    ).includes(
                        query
                    )
            )
        }
    )

    return sortPrices(
        filtered
    )
})

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const paginatedPrices = computed(() => {
    const per =
        Number(
            itemsPerPage.value
            || 10
        )

    const start =
        (
            currentPage.value
            - 1
        )
        * per

    return filteredPrices.value.slice(
        start,
        start + per
    )
})

const displayedPrices = computed(() => {
    return props.useServerProcessing
        ? pricesList.value
        : paginatedPrices.value
})

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

const priceToDelete =
    ref(null)

const confirmDelete = (priceOrId) => {
    if (
        typeof priceOrId === 'object'
    ) {
        priceToDelete.value =
            priceOrId
    } else {
        priceToDelete.value =
            displayedPrices.value.find(
                price =>
                    Number(price.id)
                    === Number(priceOrId)
            )
            || {
                id: priceOrId,
            }
    }

    showConfirmDeleteModal.value =
        true
}

const closeModal = () => {
    showConfirmDeleteModal.value =
        false

    priceToDelete.value =
        null
}

const deleteBundlePrice = () => {
    if (
        !priceToDelete.value?.id
    ) {
        return
    }

    const idToDelete =
        priceToDelete.value.id

    const titleToDelete =
        getDeleteTitle(
            priceToDelete.value
        )

    router.delete(
        route(
            'admin.schoolBundlePrices.destroy',
            {
                schoolBundlePrice:
                idToDelete,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Цена набора "${
                        titleToDelete
                        || `ID: ${idToDelete}`
                    }" удалена.`
                )
            },

            onError: (errors) => {
                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                const errorMsg =
                    errors?.general
                    || errors?.[firstKey]
                    || 'Ошибка при удалении цены набора.'

                toast.error(
                    `${errorMsg} ID: ${idToDelete}`
                )
            },

            onFinish: () => {
                closeModal()
            },
        }
    )
}

/* ==========================================================
 * LOCAL PATCH
 * ========================================================== */

const patchPrice = (
    priceId,
    payload
) => {
    const index =
        localPrices.value.findIndex(
            price =>
                Number(price.id)
                === Number(priceId)
        )

    if (index === -1) {
        return
    }

    localPrices.value[index] = {
        ...localPrices.value[index],
        ...payload,
    }
}

/* ==========================================================
 * BULK SELECT
 * ========================================================== */

const selectedPrices = ref([])

const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedPrices.value.map(
            price => price.id
        )

    if (checked) {
        selectedPrices.value = [
            ...new Set([
                ...selectedPrices.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedPrices.value =
        selectedPrices.value.filter(
            id =>
                !ids.includes(id)
        )
}

const toggleSelectPrice = (id) => {
    const index =
        selectedPrices.value.indexOf(
            id
        )

    if (index > -1) {
        selectedPrices.value.splice(
            index,
            1
        )

        return
    }

    selectedPrices.value.push(
        id
    )
}

/* ==========================================================
 * BULK ACTIVITY
 * ========================================================== */

const bulkToggleActivity = (
    newActivity
) => {
    if (
        !selectedPrices.value.length
    ) {
        toast.warning(
            'Выберите цены наборов для активации/деактивации'
        )

        return
    }

    const idsToUpdate = [
        ...selectedPrices.value,
    ]

    router.put(
        route(
            'admin.actions.schoolBundlePrices.bulkUpdateActivity'
        ),
        {
            ids:
            idsToUpdate,

            activity:
            newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                idsToUpdate.forEach(
                    id =>
                        patchPrice(
                            id,
                            {
                                activity:
                                newActivity,
                            }
                        )
                )

                selectedPrices.value =
                    []

                toast.success(
                    'Активность цен наборов массово обновлена'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Не удалось массово обновить активность'
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
        !selectedPrices.value.length
    ) {
        toast.warning(
            'Выберите цены наборов для удаления'
        )

        return
    }

    router.delete(
        route(
            'admin.actions.schoolBundlePrices.bulkDestroy'
        ),
        {
            data: {
                ids:
                selectedPrices.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedPrices.value =
                    []

                toast.success(
                    'Выбранные цены наборов удалены'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.general
                    || 'Не удалось массово удалить цены наборов'
                )
            },
        }
    )
}

/* ==========================================================
 * BULK ACTION
 * ========================================================== */

const handleBulkAction = (event) => {
    const action =
        event.target.value

    if (
        action === 'selectAll'
    ) {
        toggleAll({
            checked: true,
        })
    } else if (
        action === 'deselectAll'
    ) {
        toggleAll({
            checked: false,
        })
    } else if (
        action === 'activate'
    ) {
        bulkToggleActivity(
            true
        )
    } else if (
        action === 'deactivate'
    ) {
        bulkToggleActivity(
            false
        )
    } else if (
        action === 'delete'
    ) {
        bulkDestroy()
    }

    event.target.value =
        ''
}

/* ==========================================================
 * SINGLE ACTIVITY
 * ========================================================== */

const toggleActivity = (price) => {
    const newActivity =
        !price.activity

    const actionText =
        newActivity
            ? t('activated')
            : t('deactivated')

    router.put(
        route(
            'admin.actions.schoolBundlePrices.updateActivity',
            {
                schoolBundlePrice:
                price.id,
            }
        ),
        {
            activity:
            newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchPrice(
                    price.id,
                    {
                        activity:
                        newActivity,
                    }
                )

                price.activity =
                    newActivity

                toast.success(
                    `Цена набора "${
                        getDeleteTitle(price)
                        || `ID: ${price.id}`
                    }" ${actionText}.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности ID: ${price.id}`
                )
            },
        }
    )
}

/* ==========================================================
 * DRAG SORT
 * ========================================================== */

const handleSortOrderUpdate = (
    orderedIds
) => {
    const startSort =
        (
            currentPage.value
            - 1
        )
        * itemsPerPage.value

    const items =
        orderedIds.map(
            (id, index) => ({
                id,

                sort:
                    startSort
                    + index
                    + 1,
            })
        )

    if (!items.length) {
        return
    }

    router.put(
        route(
            'admin.actions.schoolBundlePrices.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Порядок цен наборов успешно обновлён.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления сортировки цен наборов:',
                    errors
                )

                toast.error(
                    errors?.items
                    || errors?.general
                    || 'Не удалось обновить порядок цен наборов.'
                )

                router.reload({
                    only: [
                        'prices',
                    ],

                    preserveScroll: true,
                })
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('bundlePrices')">
        <template #header>
            <TitlePage>
                {{ t('bundlePrices') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Header -->
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolBundlePrices.create')">
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

                        {{ t('addBundlePrice') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolBundlePricesProcessingMode"
                        :mode="adminSchoolBundlePricesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="pricesCount"
                    />
                </div>

                <!-- Search -->
                <SearchInput
                    v-if="pricesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <ServerSearchInput
                    v-if="pricesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Per page / Sort -->
                <div
                    v-if="pricesCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSchoolBundlePrices"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="value => sortParam = value"
                    />
                </div>

                <!-- Count / Bulk / View -->
                <div
                    v-if="pricesCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                >
                    <CountTable>
                        {{ pricesCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- Top pagination -->
                <div
                    v-if="pricesCount"
                    class="flex justify-center items-center flex-col md:flex-row mb-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPrices.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="prices"
                    />
                </div>

                <!-- Table -->
                <BundlePriceTable
                    v-if="viewMode === 'table'"
                    :prices="displayedPrices"
                    :selected-prices="selectedPrices"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPrice"
                    @toggle-all="toggleAll"
                />

                <!-- Cards -->
                <BundlePriceCardGrid
                    v-else
                    :prices="displayedPrices"
                    :selected-prices="selectedPrices"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPrice"
                    @toggle-all="toggleAll"
                />

                <!-- Bottom pagination -->
                <div
                    v-if="pricesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPrices.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="prices"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteBundlePrice"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
